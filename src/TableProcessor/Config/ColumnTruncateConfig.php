<?php

namespace Sebihojda\Mbp\TableProcessor\Config;

use Sebihojda\Mbp\TableProcessor\TableProcessorConfigInterface;
use Webmozart\Assert\Assert;

readonly class ColumnTruncateConfig implements TableProcessorConfigInterface
{
    public const string OUTPUT_FORMAT_CSV  = 'csv';
    public const string OUTPUT_FORMAT_JSON = 'json';

    private const array OUTPUT_FORMATS = [
        self::OUTPUT_FORMAT_CSV,
        self::OUTPUT_FORMAT_JSON,
    ];

    private string $outputFormat;

    public function __construct(
        private string $column,
        private string $length,
        string $outputFormat,
    ) {
        Assert::inArray($outputFormat, self::OUTPUT_FORMATS);
        $this->outputFormat = $outputFormat;
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function getLength(): string
    {
        return $this->length;
    }

    public function getOutputFormat(): string
    {
        return $this->outputFormat;
    }
}