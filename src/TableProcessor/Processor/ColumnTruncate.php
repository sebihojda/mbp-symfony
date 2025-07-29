<?php

namespace Sebihojda\Mbp\TableProcessor\Processor;

use InvalidArgumentException;
use Sebihojda\Mbp\Model\DataTable;
use Sebihojda\Mbp\Model\DataTableRow\DataRow;
use Sebihojda\Mbp\Model\DataTableRow\HeaderRow;
use Sebihojda\Mbp\TableProcessor\Config\ColumnTruncateConfig;
use Sebihojda\Mbp\TableProcessor\ConfigurableInterface;
use Sebihojda\Mbp\TableProcessor\TableProcessorConfigInterface;
use Sebihojda\Mbp\TableProcessor\TableProcessorInterface;

class ColumnTruncate implements TableProcessorInterface, ConfigurableInterface
{
    private ColumnTruncateConfig $config;

    public function process(DataTable ...$tables): array
    {
        $columnToTruncate = $this->config->getColumn(); // must verify/validate the column
        $length = $this->config->getLength(); // must verify/validate the length
        $results = [];
        foreach ($tables as $table) {
            $headers = $table->getHeaderRow()->copy();
            $result = DataTable::createEmpty($headers);
            /** @var DataRow|HeaderRow $row */
            foreach ($table->getDataRowsIterator() as $i => $row) {
                $truncatedRow = $row->withColumnTruncate($columnToTruncate, $length);
                $result->appendRow($truncatedRow);
            }
            $results[] = $result;
        }

        return $results;
    }

    public function configure(TableProcessorConfigInterface $config): static
    {
        if (!$config instanceof ColumnTruncateConfig) {
            throw new InvalidArgumentException('Invalid config type');
        }
        $this->config = $config;

        return $this;
    }
}