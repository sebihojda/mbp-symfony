<?php

declare(strict_types=1);

namespace Sebihojda\Mbp\Command\ArgumentConverter;

use Sebihojda\Mbp\IO\GetOpt;
use Sebihojda\Mbp\TableProcessor\TableProcessorConfigInterface;

class CsvMergeArgumentConverter extends OptionsCompatibleArgumentConverter
{
    /**
     * Getter for the tool's --ignore-headers boolean flag
     */
    public static function hasIgnoreHeaders(): bool
    {
        return array_key_exists('ignore-headers', GetOpt::getParsedOptions());
    }

    public static function extractTableProcessorConfig(): ?TableProcessorConfigInterface
    {
        // no config necessary for this processing
        return null;
    }

    protected static function extraLongOptions(): array
    {
        // extra long option for the boolean flag to ignore the header rows in the merged files
        return ['ignore-headers'];
    }
}
