<?php

namespace Sebihojda\Mbp\Command\ArgumentConverter;

use InvalidArgumentException;
use Sebihojda\Mbp\IO\GetOpt;
use Sebihojda\Mbp\TableProcessor\Config\ColumnReorderConfig;
use Sebihojda\Mbp\TableProcessor\Config\TableHeaderPrependerConfig;
use Sebihojda\Mbp\TableProcessor\TableProcessorConfigInterface;


class CsvColumnReorderArgumentConverter extends PipingCompatibleArgumentConverter
{

    public static function initOptions(): void
    {
        GetOpt::initOptions('c:o:', ['columns:','output-format:']);
    }

    public static function extractTableProcessorConfig(): ?TableProcessorConfigInterface
    {
        $options = GetOpt::getParsedOptions();

        $columns = $options['c'] ?? $options['columns'];

        if (null === $columns) {
            throw new InvalidArgumentException('The columns names to reorder must be provided');
        }

        $outputFormat = $options['o'] ?? ($options['output-format'] ?? TableHeaderPrependerConfig::OUTPUT_FORMAT_CSV);

        return new ColumnReorderConfig(explode(',', $columns), $outputFormat);
    }
}