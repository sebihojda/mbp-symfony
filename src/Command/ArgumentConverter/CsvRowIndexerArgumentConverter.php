<?php

namespace Sebihojda\Mbp\Command\ArgumentConverter;

use Sebihojda\Mbp\IO\GetOpt;
use Sebihojda\Mbp\TableProcessor\Config\OutputFormatConfig;
use Sebihojda\Mbp\TableProcessor\Config\TableHeaderPrependerConfig;
use Sebihojda\Mbp\TableProcessor\TableProcessorConfigInterface;

class CsvRowIndexerArgumentConverter extends PipingCompatibleArgumentConverter
{

    public static function initOptions(): void
    {
        GetOpt::initOptions('o:', ['output-format:']);
    }

    public static function extractTableProcessorConfig(): ?TableProcessorConfigInterface
    {
        // extracts the output-format option as configured through initialization
        $options = GetOpt::getParsedOptions();

        $outputFormat = $options['o'] ?? ($options['output-format'] ?? TableHeaderPrependerConfig::OUTPUT_FORMAT_CSV);

        return new OutputFormatConfig($outputFormat);
    }
}