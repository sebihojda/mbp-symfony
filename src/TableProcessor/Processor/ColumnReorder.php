<?php

namespace Sebihojda\Mbp\TableProcessor\Processor;

use InvalidArgumentException;
use Sebihojda\Mbp\Model\DataTable;
use Sebihojda\Mbp\Model\DataTableRow\DataRow;
use Sebihojda\Mbp\Model\DataTableRow\HeaderRow;
use Sebihojda\Mbp\TableProcessor\Config\ColumnReorderConfig;
use Sebihojda\Mbp\TableProcessor\ConfigurableInterface;
use Sebihojda\Mbp\TableProcessor\TableProcessorConfigInterface;
use Sebihojda\Mbp\TableProcessor\TableProcessorInterface;

class ColumnReorder implements TableProcessorInterface, ConfigurableInterface
{
    private ColumnReorderConfig $config;

    public function process(DataTable ...$tables): array
    {
        $columnsOrder = $this->config->getColumnsOrder();
        $results = [];
        foreach ($tables as $table) {
            //$headers = $table->getHeaderRow();
            $result = DataTable::createEmpty($columnsOrder);
            /** @var DataRow|HeaderRow $row */
            foreach ($table->getDataRowsIterator() as $i => $row) {
                $orderedRow = $row->withColumnReorder($columnsOrder);
                $result->appendRow($orderedRow);
            }
            $results[] = $result;
        }

        return $results;
    }

    public function configure(TableProcessorConfigInterface $config): static
    {
        if (!$config instanceof ColumnReorderConfig) {
            throw new InvalidArgumentException('Invalid config type');
        }
        $this->config = $config;

        return $this;
    }
}