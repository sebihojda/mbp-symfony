<?php

namespace Sebihojda\Mbp\TableProcessor\Processor;

use Sebihojda\Mbp\Model\DataTable;
use Sebihojda\Mbp\Model\DataTableRow\DataRow;
use Sebihojda\Mbp\Model\DataTableRow\HeaderRow;
use Sebihojda\Mbp\TableProcessor\Config\ColumnRemoveConfig;
use Sebihojda\Mbp\TableProcessor\ConfigurableInterface;
use Sebihojda\Mbp\TableProcessor\TableProcessorConfigInterface;
use Sebihojda\Mbp\TableProcessor\TableProcessorInterface;

class ColumnRemove implements TableProcessorInterface, ConfigurableInterface
{
    private ColumnRemoveConfig $config;

    public function process(DataTable ...$tables): array
    {
        $columnToBeRemoved = $this->config->getColumn(); // must verify/validate the column/index
        $results = [];
        foreach ($tables as $table) {
            $headers = $table->getHeaderRow()->withRemovedColumn($columnToBeRemoved);
            $result = DataTable::createEmpty($headers);
            /** @var DataRow|HeaderRow $row */
            foreach ($table->getDataRowsIterator() as $i => $row) {
                $orderedRow = $row->withRemovedColumn($columnToBeRemoved);
                $result->appendRow($orderedRow);
            }
            $results[] = $result;
        }

        return $results;
    }

    public function configure(TableProcessorConfigInterface $config): static
    {
        if (!$config instanceof ColumnRemoveConfig) {
            throw new InvalidArgumentException('Invalid config type');
        }
        $this->config = $config;

        return $this;
    }
}