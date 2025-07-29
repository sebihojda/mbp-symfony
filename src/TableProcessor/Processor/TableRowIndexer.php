<?php

namespace Sebihojda\Mbp\TableProcessor\Processor;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use Sebihojda\Mbp\Model\DataTable;
use Sebihojda\Mbp\Model\DataTableRow\DataRow;
use Sebihojda\Mbp\Model\DataTableRow\HeaderRow;
use Sebihojda\Mbp\TableProcessor\Config\OutputFormatConfig;
use Sebihojda\Mbp\TableProcessor\ConfigurableInterface;
use Sebihojda\Mbp\TableProcessor\TableProcessorConfigInterface;
use Sebihojda\Mbp\TableProcessor\TableProcessorInterface;

class TableRowIndexer implements TableProcessorInterface, ConfigurableInterface
{
    private OutputFormatConfig $config;


    /**
     * @throws \Exception
     */
    public function process(DataTable ...$tables): array
    {
        $results = [];
        foreach ($tables as $table) {
            $headers = $table->getHeaderRow()->withPrependColumn('index');
            $result = DataTable::createEmpty($headers);
            /** @var DataRow|HeaderRow $row */
            foreach ($table->getDataRowsIterator() as $i => $row) {
                    $indexedRow = $row->withPrependColumn($i+1, $result);
                    $result->appendRow($indexedRow);
            }
            $results[] = $result;
        }

        return $results;
    }

    public function configure(TableProcessorConfigInterface $config): static
    {
        if (!$config instanceof OutputFormatConfig) {
            throw new InvalidArgumentException('Invalid config type');
        }
        $this->config = $config;

        return $this;
    }
}