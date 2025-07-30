<?php

declare(strict_types=1);

namespace Sebihojda\Mbp\TableProcessor\Config;

use OpenSSLAsymmetricKey;
use Sebihojda\Mbp\TableProcessor\TableProcessorConfigInterface;

readonly class ColumnEncryptorConfig implements TableProcessorConfigInterface
{
    public function __construct(
        private string $column,
        private OpenSSLAsymmetricKey $publicKey,
    ) {
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function getPublicKey(): OpenSSLAsymmetricKey
    {
        return $this->publicKey;
    }
}
