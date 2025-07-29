<?php

declare(strict_types=1);

namespace Sebihojda\Mbp\IO;

use Sebihojda\Mbp\Model\DataTable;
use Sebihojda\Mbp\Model\DataTableRow\DataRow;
use JsonException;

final class JsonWriter
{
    /**
     * @throws JsonException
     */
    public static function write(DataTable $table, $outputStream): void
    {
        $keys = $table->getHeaderRow()->toArray();
        $skipHeaders = !$table->getHeaderRow()->isDefault();
        /** @var DataRow $row */
        foreach ($table as $row) {
            if ($skipHeaders) {
                $skipHeaders = false;
                continue;
            }
            $object = array_combine($keys, $row->toArray());
            fwrite($outputStream, json_encode($object, JSON_THROW_ON_ERROR).PHP_EOL);
        }
    }

    public static function writeClose(DataTable $table, $outputStream): void
    {
        self::write($table, $outputStream);
        fclose($outputStream);
    }
}
