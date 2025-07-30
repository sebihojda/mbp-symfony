<?php

declare(strict_types=1);

namespace Sebihojda\Mbp\TableProcessor\Processor;

use Ds\Map;
use Ds\Vector;
use Sebihojda\Mbp\Model\DataTable;
use Sebihojda\Mbp\Model\DataTableRow\DataRow;
use Sebihojda\Mbp\Model\DataTableRow\HeaderRow;
use Sebihojda\Mbp\TableProcessor\ConfigurableInterface;
use Sebihojda\Mbp\TableProcessor\Config\TableInnerJoinerConfig;
use Sebihojda\Mbp\TableProcessor\TableProcessorConfigInterface;
use Sebihojda\Mbp\TableProcessor\TableProcessorInterface;
use Webmozart\Assert\Assert;

class TableInnerJoiner implements TableProcessorInterface, ConfigurableInterface
{
    private TableInnerJoinerConfig $config;

    public function configure(TableProcessorConfigInterface $config): static
    {
        Assert::isInstanceOf($config, TableInnerJoinerConfig::class);
        $this->config = $config;
        return $this;
    }

    public function process(DataTable ...$tables): array
    {
        Assert::count($tables, 2, 'Inner Join needs exactly 2 tables.');
        [$leftTable, $rightTable] = $tables;

        // Indexeaza al doilea tabel (rightTable) pentru cautari rapide
        $rightTableMap = new Map();
        /** @var DataRow $row */
        foreach ($rightTable->getDataRowsIterator() as $row) {
            $joinValue = $row->getColumn($this->config->getRightColumn());
            // Permitem join pe valori multiple, stocand un Vector de randuri
            if (!$rightTableMap->hasKey($joinValue)) {
                $rightTableMap->put($joinValue, new Vector());
            }
            $rightTableMap->get($joinValue)->push($row);
        }

        // Creeaza noul antet prin combinarea celor doua
        $newHeader = new Vector(array_merge(
            $leftTable->getHeaderRow()->toArray(),
            $rightTable->getHeaderRow()->toArray()
        ));
        $resultTable = DataTable::createEmpty(HeaderRow::fromVector($newHeader));

        // Parcurge primul tabel si cauta potriviri in tabelul indexat
        /** @var DataRow $row */
        foreach ($leftTable->getDataRowsIterator() as $leftRow) {
            $joinValue = $leftRow->getColumn($this->config->getLeftColumn());

            if ($rightTableMap->hasKey($joinValue)) {
                // Pentru fiecare rand gasit in tabelul din dreapta, creeaza un rand combinat
                foreach ($rightTableMap->get($joinValue) as $rightRow) {
                    $newRowData = array_merge($leftRow->toArray(), $rightRow->toArray());
                    $resultTable->appendRow(DataRow::fromArray($newRowData, $resultTable));
                }
            }
        }

        return [$resultTable];
    }
}
