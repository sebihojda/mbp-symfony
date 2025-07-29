<?php

declare(strict_types=1);

namespace Sebihojda\Mbp\Model\DataTableRow;

use Sebihojda\Mbp\Model\DataTable;
use Sebihojda\Mbp\Model\DataTableRow;
use Ds\Map;
use Webmozart\Assert\Assert;
use Symfony\Component\String\UnicodeString;
use Carbon\Carbon;



/**
 * Models a data table data row.
 */
class DataRow extends DataTableRow
{
    private function __construct(
        private readonly Map $row,
    ) {
        //
    }

    public static function fromArray(array $rowValues, DataTable $table): static
    {
        $headerRow = $table->getHeaderRow();
        Assert::eq(count($rowValues), $headerRow->count());

        $rowData = new Map();
        foreach (array_values($rowValues) as $i => $columnValue) {
            $rowData[$headerRow[$i]] = $columnValue;
        }

        return new static($rowData);
    }

    public function getColumn(string $columnToSign)
    {
        return $this->row[$columnToSign];
    }

    public function toArray(): array
    {
        return $this->row->values()->toArray();
    }

    /**
     * Creates another row from the current one with an added column and corresponding value
     */
    public function withAddedColumn(string $column, mixed $value): static
    {
        $map = $this->row->copy();
        $map->put($column, $value);

        return new static($map);
    }

    /**
     * Creates another row from the current one with the given column removed
     */
    public function withRemovedColumn(string $column): static
    {
        $map = $this->row->copy();
        $map->remove($column);

        return new static($map);
    }

    public function withPrependColumn(string $value, DataTable $table): static
    {
        //$this->row->put($header, $value);     // WRONG!!!
        $map = $this->row->copy();
        $mapToArray = $map->toArray();
        array_unshift($mapToArray, $value);

        return DataRow::fromArray($mapToArray, $table);
    }

    public function withColumnReorder(array $columnsOrder): static
    {
        $map = $this->row->copy();
        $newRow = new Map();
        foreach ($columnsOrder as $column) {
            $newRow->put($column, $map[$column]);
        }
        return new static($newRow);
    }

    public function withColumnTruncate(string $columnToTruncate, int $length): static
    {
        $map = $this->row->copy();
        $map[$columnToTruncate] = new UnicodeString($map[$columnToTruncate])->truncate($length);

        return new static($map);
    }

    /**
     * @throws \Exception
     */
    public function withDateReformat(string $dateColumn, string $dateFormat): static
    {
        $map = $this->row->copy();
        try{
            $map[$dateColumn] = Carbon::parse($map[$dateColumn])->format($dateFormat);
        }catch(\Exception $e){
            throw new \Exception('wrong date format');
        }
        return new static($map);
    }
}
