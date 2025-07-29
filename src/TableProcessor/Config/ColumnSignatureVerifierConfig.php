<?php

namespace Sebihojda\Mbp\TableProcessor\Config;

use OpenSSLAsymmetricKey;
use Sebihojda\Mbp\TableProcessor\TableProcessorConfigInterface;

readonly class ColumnSignatureVerifierConfig implements TableProcessorConfigInterface
{
    public function __construct(
        private string $column,
        private string $signatureColumn,
        private OpenSSLAsymmetricKey $publicKey,
    ) {
        //
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function getSignatureColumn(): string
    {
        return $this->signatureColumn;
    }

    public function getPublicKey(): OpenSSLAsymmetricKey
    {
        return $this->publicKey;
    }
}
