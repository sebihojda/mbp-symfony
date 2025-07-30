<?php

declare(strict_types=1);

namespace Sebihojda\Mbp\TableProcessor\Processor;

use InvalidArgumentException;
use Sebihojda\Mbp\Model\DataTable;
use Sebihojda\Mbp\TableProcessor\Config\ColumnEncryptorConfig;
use Sebihojda\Mbp\TableProcessor\ConfigurableInterface;
use Sebihojda\Mbp\TableProcessor\TableProcessorInterface;
use Sebihojda\Mbp\TableProcessor\TableProcessorConfigInterface;

class ColumnEncryptor implements TableProcessorInterface, ConfigurableInterface
{
    private ColumnEncryptorConfig $config;

    public function configure(TableProcessorConfigInterface $config): static
    {
        if (!$config instanceof ColumnEncryptorConfig) {
            throw new InvalidArgumentException('Invalid config for ColumnEncryptor.');
        }
        $this->config = $config;
        return $this;
    }

    public function process(DataTable ...$tables): array
    {
        $table = $tables[0];
        $columnToEncrypt = $this->config->getColumn();

        $resultTable = DataTable::createEmpty($table->getHeaderRow());

        foreach ($table->getDataRowsIterator() as $inputRow) {
            $valueToEncrypt = $inputRow->getColumn($columnToEncrypt);

            openssl_public_encrypt($valueToEncrypt, $encryptedValue, $this->config->getPublicKey());
            $base64EncryptedValue = base64_encode($encryptedValue);

            $resultRow = $inputRow->withColumnValue($columnToEncrypt, $base64EncryptedValue);

            $resultTable->appendRow($resultRow);
        }

        return [$resultTable];
    }
}
