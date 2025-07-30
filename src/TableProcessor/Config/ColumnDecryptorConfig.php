<?php

declare(strict_types=1);

namespace Sebihojda\Mbp\TableProcessor\Config;

use OpenSSLAsymmetricKey;
use Sebihojda\Mbp\TableProcessor\TableProcessorConfigInterface;

readonly class ColumnDecryptorConfig implements TableProcessorConfigInterface
{
    public function __construct(
        private string $column,
        private OpenSSLAsymmetricKey $privateKey,
    ) {
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function getPrivateKey(): OpenSSLAsymmetricKey
    {
        return $this->privateKey;
    }
}
