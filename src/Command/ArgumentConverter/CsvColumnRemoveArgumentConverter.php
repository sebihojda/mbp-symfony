<?php

namespace Sebihojda\Mbp\Command\ArgumentConverter;

use InvalidArgumentException;
use Sebihojda\Mbp\IO\GetOpt;
use Sebihojda\Mbp\TableProcessor\Config\ColumnRemoveConfig;
use Sebihojda\Mbp\TableProcessor\Config\TableHeaderPrependerConfig;
use Sebihojda\Mbp\TableProcessor\TableProcessorConfigInterface;

class CsvColumnRemoveArgumentConverter extends PipingCompatibleArgumentConverter
{

    public static function initOptions(): void
    {
        GetOpt::initOptions('c:o:', ['column-index:', 'output-format:']);
    }

    public static function extractTableProcessorConfig(): ?TableProcessorConfigInterface
    {
        $options = GetOpt::getParsedOptions();

        $column = $options['c'] ?? $options['column-index'];


        if (null === $column) {
            throw new InvalidArgumentException('The column name or the column index must be provided');
        }

        $outputFormat = $options['o'] ?? ($options['output-format'] ?? TableHeaderPrependerConfig::OUTPUT_FORMAT_CSV);

        return new ColumnRemoveConfig($column, $outputFormat);
    }
}