<?php

declare(strict_types=1);

namespace Sebihojda\Mbp\TableProcessor\Processor;

use InvalidArgumentException;
use RuntimeException;
use Sebihojda\Mbp\Model\DataTable;
use Sebihojda\Mbp\Model\DataTableRow\DataRow;
use Sebihojda\Mbp\TableProcessor\Config\ColumnSignatureVerifierConfig;
use Sebihojda\Mbp\TableProcessor\ConfigurableInterface;
use Sebihojda\Mbp\TableProcessor\TableProcessorInterface;
use Sebihojda\Mbp\TableProcessor\TableProcessorConfigInterface;

class ColumnSignatureVerifier implements TableProcessorInterface, ConfigurableInterface
{
    private ColumnSignatureVerifierConfig $config;

    public function process(DataTable ...$tables): array
    {
        $columnForSignature = $this->config->getSignatureColumn();
        $outputTables = [];
        foreach ($tables as $inputTable) {
            $inputHeaderRow = $inputTable->getHeaderRow();
            if ($inputHeaderRow->isDefault()) {
                throw new InvalidArgumentException('Input table must have a header row');
            }
            // create a new immutable header row for the result table including the column for the signature
            $resultHeaderRow = $inputHeaderRow->withRemovedColumn($columnForSignature);
            // create the result table with the prepared headers
            $resultTable = DataTable::createEmpty($resultHeaderRow);
            /** @var DataRow $inputRow */
            foreach ($inputTable->getDataRowsIterator() as $inputRow) {
                $inputValue = $inputRow->getColumn($this->config->getColumn());
                $signature = base64_decode($inputRow->getColumn($columnForSignature));
                // signature validation for the current row using the plain value and its base64 encoded signature
                $isValid = openssl_verify($inputValue, $signature, $this->config->getPublicKey(), OPENSSL_ALGO_SHA256);
                if ($isValid !== 1) {
                    if ($isValid === 0) {
                        throw new RuntimeException('Invalid signature for row '.implode(', ', $inputRow->toArray()));
                    }

                    throw new RuntimeException("Error verifying signature: ".openssl_error_string());
                }

                // removal of the signature column to return the original table before signing
                $resultRow = $inputRow->withRemovedColumn($columnForSignature);
                $resultTable->appendRow($resultRow);
            }
            $outputTables[] = $resultTable;
        }

        return $outputTables;
    }

    public function configure(TableProcessorConfigInterface $config): static
    {
        if (!$config instanceof ColumnSignatureVerifierConfig) {
            throw new InvalidArgumentException('Invalid config type');
        }
        $this->config = $config;

        return $this;
    }
}
