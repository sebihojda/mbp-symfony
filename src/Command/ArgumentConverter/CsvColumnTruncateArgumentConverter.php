<?php

namespace Sebihojda\Mbp\Command\ArgumentConverter;

use InvalidArgumentException;
use Sebihojda\Mbp\IO\GetOpt;
use Sebihojda\Mbp\TableProcessor\Config\ColumnTruncateConfig;
use Sebihojda\Mbp\TableProcessor\Config\TableHeaderPrependerConfig;
use Sebihojda\Mbp\TableProcessor\TableProcessorConfigInterface;

class CsvColumnTruncateArgumentConverter extends PipingCompatibleArgumentConverter
{

    public static function initOptions(): void
    {
        GetOpt::initOptions('c:l:o:', ['column:', 'length:', 'output-format:']);
    }

    public static function extractTableProcessorConfig(): ?TableProcessorConfigInterface
    {
        $options = GetOpt::getParsedOptions();

        $column = $options['c'] ?? $options['column'];

        $length = $options['l'] ?? $options['length'];


        if (null === $column && null === $length) {
            throw new InvalidArgumentException('The column name and the length must be provided');
        }

        $outputFormat = $options['o'] ?? ($options['output-format'] ?? TableHeaderPrependerConfig::OUTPUT_FORMAT_CSV);

        return new ColumnTruncateConfig($column, $length, $outputFormat);
    }
}