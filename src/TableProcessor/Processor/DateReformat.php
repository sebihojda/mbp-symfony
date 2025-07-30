<?php

namespace Sebihojda\Mbp\TableProcessor\Processor;

use InvalidArgumentException;
use Sebihojda\Mbp\Model\DataTable;
use Sebihojda\Mbp\Model\DataTableRow\DataRow;
use Sebihojda\Mbp\Model\DataTableRow\HeaderRow;
use Sebihojda\Mbp\TableProcessor\Config\DateReformatConfig;
use Sebihojda\Mbp\TableProcessor\ConfigurableInterface;
use Sebihojda\Mbp\TableProcessor\TableProcessorConfigInterface;
use Sebihojda\Mbp\TableProcessor\TableProcessorInterface;

class DateReformat  implements TableProcessorInterface, ConfigurableInterface
{
    private DateReformatConfig $config;

    /**
     * @throws \Exception
     */
    public function process(DataTable ...$tables): array
    {
        $dateColumn = $this->config->getColumn();
        $dateFormat = $this->config->getDateFormat();
        $results = [];
        foreach ($tables as $table) {
            $headers = $table->getHeaderRow()->copy();
            $exists = false;
            foreach ($headers->toArray() as $header) {
                if($header == $dateColumn){
                    $exists = true;
                    break;
                }
            }
            if(!$exists){
                throw new InvalidArgumentException("Invalid column name: $dateColumn");
            }
            $result = DataTable::createEmpty($headers);
            /** @var DataRow|HeaderRow $row */
            foreach ($table->getDataRowsIterator() as $i => $row) {
                $reformatedRow = $row->withDateReformat($dateColumn, $dateFormat);
                $result->appendRow($reformatedRow);
            }
            $results[] = $result;
        }

        return $results;
    }

    public function configure(TableProcessorConfigInterface $config): static
    {
        if (!$config instanceof DateReformatConfig) {
            throw new InvalidArgumentException('Invalid config type');
        }
        $this->config = $config;

        return $this;
    }
}
