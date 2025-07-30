<?php

declare(strict_types=1);

namespace Sebihojda\Mbp\TableProcessor\Config;

use Sebihojda\Mbp\TableProcessor\TableProcessorConfigInterface;

readonly class TableInnerJoinerConfig implements TableProcessorConfigInterface
{
    public function __construct(
        private string $leftColumn,
        private string $rightColumn,
    ) {
        //
    }

    public function getLeftColumn(): string
    {
        return $this->leftColumn;
    }

    public function getRightColumn(): string
    {
        return $this->rightColumn;
    }
}
