<?php

namespace Sebihojda\Mbp\Command\ArgumentConverter;

use Sebihojda\Mbp\TableProcessor\TableProcessorConfigInterface;
use InvalidArgumentException;
use Sebihojda\Mbp\IO\GetOpt;
use Sebihojda\Mbp\TableProcessor\Config\ColumnSignatureVerifierConfig;


class CsvColumnSignVerifyArgumentConverter extends PipingCompatibleArgumentConverter
{
    public static function initOptions(): void
    {
        GetOpt::initOptions('c:k:', ['column:', 'public-key:']);
    }

    public static function extractTableProcessorConfig(): ?TableProcessorConfigInterface
    {
        $options = GetOpt::getParsedOptions();
        $column = $options['c'] ?? $options['column'];
        if (null === $column) {
            throw new InvalidArgumentException('The column name to check signature for must be provided');
        }

        $signatureColumn = $column.'_signature';

        $publicKeyFile = $options['k'] ?? $options['public-key'];
        if (null === $publicKeyFile) {
            throw new InvalidArgumentException('The public key PEM file must be provided');
        }
        $publicKey = openssl_pkey_get_public(file_get_contents($publicKeyFile));

        return new ColumnSignatureVerifierConfig($column, $signatureColumn, $publicKey);
    }
}
