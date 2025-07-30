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
        $columnToTruncate = $this->config->getColumn();
        $length = $this->config->getLength();
        if(!(is_numeric($length) && (int)$length > 0)){
            throw new InvalidArgumentException("Invalid column length: $length");
        }
        $results = [];
        foreach ($tables as $table) {
            $headers = $table->getHeaderRow()->copy();
            $exists = false;
            foreach ($headers->toArray() as $header) {
                if($header == $columnToTruncate){
                    $exists = true;
                    break;
                }
            }
            if(!$exists){
                throw new InvalidArgumentException("Invalid column name: $columnToTruncate");
            }
            $result = DataTable::createEmpty($headers);
            /** @var DataRow|HeaderRow $row */
            foreach ($table->getDataRowsIterator() as $row) {
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
