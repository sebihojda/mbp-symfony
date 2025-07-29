<?php

declare(strict_types=1);

namespace Sebihojda\Mbp\Command\ArgumentConverter;

use Sebihojda\Mbp\IO\GetOpt;
use Sebihojda\Mbp\TableProcessor\Config\TableHeaderPrependerConfig;
use InvalidArgumentException;

class CsvHeadersPrependArgumentConverter extends PipingCompatibleArgumentConverter
{
    public static function extractTableProcessorConfig(): ?TableHeaderPrependerConfig
    {
        // extracts the h/headers option as configured through initialization
        $options = GetOpt::getParsedOptions();
        $headers = $options['h'] ?? $options['headers'];
        if (null === $headers) {
            throw new InvalidArgumentException('Headers must be provided');
        }
        $outputFormat = $options['output-format'] ?? TableHeaderPrependerConfig::OUTPUT_FORMAT_CSV;

        return new TableHeaderPrependerConfig(explode(',', $headers), $outputFormat);
    }

    public static function initOptions(): void
    {
        // the only getopt() option is the list of headers, comma separated, either short or long version
        GetOpt::initOptions('h:', ['headers:', 'output-format:']);
    }
}
