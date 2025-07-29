<?php

namespace Sebihojda\Mbp\Command\ArgumentConverter;

use Sebihojda\Mbp\TableProcessor\TableProcessorConfigInterface;
use InvalidArgumentException;
use Sebihojda\Mbp\IO\GetOpt;
use Sebihojda\Mbp\TableProcessor\Config\ColumnSignerConfig;


class CsvColumnSignArgumentConverter extends PipingCompatibleArgumentConverter
{

    public static function initOptions(): void
    {
        GetOpt::initOptions('c:k:', ['column:', 'private-key:']);
    }

    public static function extractTableProcessorConfig(): ?TableProcessorConfigInterface
    {
        $options = GetOpt::getParsedOptions();
        $column = $options['c'] ?? $options['column'];
        if (null === $column) {
            throw new InvalidArgumentException('The column name to sign must be provided');
        }

        $signatureColumn = $column.'_signature';

        $privateKeyFile = $options['k'] ?? $options['private-key'];
        if (null === $privateKeyFile) {
            throw new InvalidArgumentException('The private key PEM file must be provided');
        }
        $privateKey = openssl_pkey_get_private(file_get_contents($privateKeyFile));

        return new ColumnSignerConfig($column, $signatureColumn, $privateKey);
    }
}
