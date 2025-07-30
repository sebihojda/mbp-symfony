<?php

declare(strict_types=1);

namespace Sebihojda\Mbp\TableProcessor\Processor;

use InvalidArgumentException;
use Sebihojda\Mbp\Model\DataTable;
use Sebihojda\Mbp\TableProcessor\Config\ColumnDecryptorConfig;
use Sebihojda\Mbp\TableProcessor\ConfigurableInterface;
use Sebihojda\Mbp\TableProcessor\TableProcessorInterface;
use Sebihojda\Mbp\TableProcessor\TableProcessorConfigInterface;

class ColumnDecryptor implements TableProcessorInterface, ConfigurableInterface
{
    private ColumnDecryptorConfig $config;

    public function configure(TableProcessorConfigInterface $config): static
    {
        if (!$config instanceof ColumnDecryptorConfig) {
            throw new InvalidArgumentException('Invalid config for ColumnDecryptor.');
        }
        $this->config = $config;
        return $this;
    }

    public function process(DataTable ...$tables): array
    {
        $table = $tables[0];
        $columnToDecrypt = $this->config->getColumn();

        $resultTable = DataTable::createEmpty($table->getHeaderRow());

        foreach ($table->getDataRowsIterator() as $inputRow) {
            $valueToDecrypt = $inputRow->getColumn($columnToDecrypt);
            $binaryEncryptedValue = base64_decode($valueToDecrypt);

            openssl_private_decrypt($binaryEncryptedValue, $decryptedValue, $this->config->getPrivateKey());

            $resultRow = $inputRow->withColumnValue($columnToDecrypt, $decryptedValue);

            $resultTable->appendRow($resultRow);
        }

        return [$resultTable];
    }
}
