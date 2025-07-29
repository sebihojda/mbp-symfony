<?php

namespace Sebihojda\Mbp\Command\ArgumentConverter;

use InvalidArgumentException;
use Sebihojda\Mbp\IO\GetOpt;
use Sebihojda\Mbp\TableProcessor\Config\DateReformatConfig;
use Sebihojda\Mbp\TableProcessor\Config\TableHeaderPrependerConfig;
use Sebihojda\Mbp\TableProcessor\TableProcessorConfigInterface;

class CsvDateReformatArgumentConverter  extends PipingCompatibleArgumentConverter
{
    public static function initOptions(): void
    {
        GetOpt::initOptions('c:f:o:', ['column:', 'date-format:', 'output-format:']);
    }

    public static function extractTableProcessorConfig(): ?TableProcessorConfigInterface
    {
        $options = GetOpt::getParsedOptions();

        $column = $options['c'] ?? $options['column'];

        $format = $options['f'] ?? $options['date-format'];


        if (null === $column && null === $format) {
            throw new InvalidArgumentException('The column name and the format must be provided');
        }

        $outputFormat = $options['o'] ?? ($options['output-format'] ?? TableHeaderPrependerConfig::OUTPUT_FORMAT_CSV);

        return new DateReformatConfig($column, $format, $outputFormat);
    }
}