<?php

namespace Sebihojda\Mbp\TableProcessor\Processor;

use InvalidArgumentException;
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
        $columnToBeRemoved = $this->config->getColumn();
        $results = [];
        foreach ($tables as $table) {
            $headers = $table->getHeaderRow();
            if(is_numeric($columnToBeRemoved)){
                if($headers->offsetExists((int)$columnToBeRemoved)){ // $columnToBeRemoved >= 0 && $columnToBeRemoved < count($headers)
                    $columnToBeRemoved = $headers->offsetGet((int)$columnToBeRemoved);
                }else{
                    throw new InvalidArgumentException("Invalid column number: $columnToBeRemoved");
                }
            }else{
                $exists = false;
                foreach ($headers->toArray() as $header) {
                    if($header == $columnToBeRemoved){
                        $exists = true;
                        break;
                    }
                }
                if(!$exists){
                    throw new InvalidArgumentException("Invalid column name: $columnToBeRemoved");
                }
            }
            $newHeaders = $table->getHeaderRow()->withRemovedColumn($columnToBeRemoved);
            $result = DataTable::createEmpty($newHeaders);
            /** @var DataRow|HeaderRow $row */
            foreach ($table->getDataRowsIterator() as $row) {
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
