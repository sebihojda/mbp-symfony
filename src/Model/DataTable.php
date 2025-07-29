<?php

declare(strict_types=1);

namespace Sebihojda\Mbp\Model;

use Sebihojda\Mbp\Model\DataTableRow\DataRow;
use Sebihojda\Mbp\Model\DataTableRow\HeaderRow;
use Ds\Deque;
use Ds\Vector;
use IteratorAggregate;
use Traversable;
use Webmozart\Assert\Assert;

/**
 * Models a data table that consists of a header row and data rows
 */
class DataTable implements IteratorAggregate
{
    /**
     * Collection of rows in the data table.
     * @var Deque<DataTableRow>
     */
    private Deque $dataRows;

    private function __construct(
        private readonly HeaderRow $headerRow,
    ) {
        $this->dataRows = new Deque();
    }

    /**
     * Factory method that creates an empty data table.
     */
    public static function createEmpty(array|Vector|HeaderRow $headers): static
    {
        // handle the case when a header row is already available at table creation (perhaps from another table)
        if ($headers instanceof HeaderRow) {
            return new static($headers->copy());
        }

        // handle the case when the headers are provided as a list, either as an array, or as a Vector
        if (!$headers instanceof Vector) {
            $headers = new Vector($headers);
        }

        return new static(HeaderRow::fromVector($headers));
    }

    /**
     * Factory method that creates a data table from a plain two-dimensional array.
     */
    public static function createFromArray(array $table, bool $ignoreHeaders = false): static
    {
        // we can safely create an instance for a non-empty input
        Assert::notEmpty($table);

        // handle the header row, if present

        if ($ignoreHeaders) {
            // as fallback, we use the numeric keys as column names
            $headerRow = HeaderRow::fromDefaults(count(reset($table)));
        } else {
            // we use the first row as headers
            $headerRow = HeaderRow::fromVector(new Vector(array_shift($table)));
        }

        // create the data table and populate with rows
        $instance = new static($headerRow);
        foreach ($table as $row) {
            $instance->appendRow(DataRow::fromArray($row, $instance));
        }

        return $instance;
    }

    public function getIterator(): Traversable
    {
        $generator = function () {
            if (!$this->headerRow->isDefault()) {
                yield $this->headerRow;
            }
            foreach ($this->dataRows as $dataRow) {
                yield $dataRow;
            }
        };

        return $generator();
    }

    public function getDataRowsIterator(): Traversable
    {
        return $this->dataRows->getIterator();
    }

    public function getHeaderRow(): HeaderRow
    {
        return $this->headerRow;
    }

    public function prependRow(DataRow $dataRow)
    {
        $this->dataRows->unshift($dataRow);

        return $this;
    }

    public function appendRow(DataRow $dataRow): static
    {
        $this->dataRows->push($dataRow);

        return $this;
    }

    public function appendRowsFrom(DataTable $table): static
    {
        Assert::true($this->isMergeableWith($table), 'Tables are not compatible');
        foreach ($table->dataRows as $row) {
            $this->appendRow($row);
        }

        return $this;
    }

    private function isMergeableWith(DataTable $table): bool
    {
        // two tables with different number of columns are not mergeable
        if ($this->headerRow->count() !== $table->headerRow->count()) {
            return false;
        }

        // we allow merging with a table having default column names ("1","2",...)
        if ($table->headerRow->isDefault()) {
            return true;
        }

        // two tables are mergeable only when they have the same header
        return $this->headerRow->toArray() === $table->headerRow->toArray();
    }
}
