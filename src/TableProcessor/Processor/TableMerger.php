<?php

declare(strict_types=1);

namespace Sebihojda\Mbp\TableProcessor\Processor;

use Sebihojda\Mbp\Model\DataTable;
use Sebihojda\Mbp\TableProcessor\TableProcessorInterface;
use Illuminate\Support\Arr;

class TableMerger implements TableProcessorInterface
{
    /**
     * @return array|DataTable[]
     */
    public function process(DataTable ...$tables): array
    {
        $result = DataTable::createEmpty(Arr::first($tables)->getHeaderRow());
        foreach ($tables as $table) {
            $result->appendRowsFrom($table);
        }

        return [$result];
    }
}
