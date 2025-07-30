<?php

declare(strict_types=1);

namespace Sebihojda\Mbp\TableProcessor\Processor;

use Ds\Vector;
use Sebihojda\Mbp\Model\DataTable;
use Sebihojda\Mbp\Model\DataTableRow\DataRow;
use Sebihojda\Mbp\Model\DataTableRow\HeaderRow;
use Sebihojda\Mbp\TableProcessor\ConfigurableInterface;
use Sebihojda\Mbp\TableProcessor\Config\TableSelectWhereConfig;
use Sebihojda\Mbp\TableProcessor\TableProcessorConfigInterface;
use Sebihojda\Mbp\TableProcessor\TableProcessorInterface;
use Webmozart\Assert\Assert;

class TableSelectWhereProcessor implements TableProcessorInterface, ConfigurableInterface
{
    private TableSelectWhereConfig $config;

    public function configure(TableProcessorConfigInterface $config): static
    {
        Assert::isInstanceOf($config, TableSelectWhereConfig::class);
        $this->config = $config;
        return $this;
    }

    public function process(DataTable ...$tables): array
    {
        Assert::count($tables, 1, 'Select & Where needs exactly one input table.');
        $inputTable = $tables[0];

        // Pasul 1: Filtreaza randurile (clauza WHERE)
        $filteredRows = new Vector();
        /** @var DataRow $row */
        foreach ($inputTable->getDataRowsIterator() as $row) {
            if ($this->rowMatchesAllClauses($row)) {
                $filteredRows->push($row);
            }
        }

        // Pasul 2: Creeaza noul tabel cu coloanele selectate (clauza SELECT)
        $selectColumns = $this->config->getSelectColumns();
        if (empty($selectColumns)) {
            // Daca nu s-au specificat coloane, le folosim pe toate cele originale
            $selectColumns = $inputTable->getHeaderRow()->toArray();
        }

        $resultTable = DataTable::createEmpty($selectColumns);
        /** @var DataRow $row */
        foreach ($filteredRows as $row) {
            // Folosim metoda existenta pentru a reordona/selecta coloanele
            $newRow = $row->withColumnReorder($selectColumns);
            $resultTable->appendRow($newRow);
        }

        return [$resultTable];
    }

    private function rowMatchesAllClauses(DataRow $row): bool
    {
        foreach ($this->config->getWhereClauses() as $clause) {
            $columnValue = $row->getColumn($clause['column']);
            $conditionValue = $clause['value'];
            $operator = $clause['operator'];

            $match = match ($operator) {
                '=' => $columnValue == $conditionValue,
                '>' => $columnValue > $conditionValue,
                '<' => $columnValue < $conditionValue,
                '~' => (bool) preg_match($conditionValue, $columnValue), // Operator '~' pentru REGEXP
                default => false,
            };

            if (!$match) {
                return false; // Daca o singura conditie e falsa, randul este exclus
            }
        }
        return true; // Randul a trecut toate testele
    }
}
