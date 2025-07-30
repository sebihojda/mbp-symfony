<?php

declare(strict_types=1);

namespace Sebihojda\Mbp\TableProcessor\Config;

use Sebihojda\Mbp\TableProcessor\TableProcessorConfigInterface;

readonly class TableSelectWhereConfig implements TableProcessorConfigInterface
{
    /**
     * @param array $selectColumns Coloanele de afisat. Un array gol inseamna toate coloanele.
     * @param array $whereClauses Un array de conditii. Ex: [['column' => 'age', 'operator' => '>', 'value' => 30]]
     */
    public function __construct(
        private array $selectColumns = [],
        private array $whereClauses = [],
    ) {
    }

    public function getSelectColumns(): array
    {
        return $this->selectColumns;
    }

    public function getWhereClauses(): array
    {
        return $this->whereClauses;
    }
}
