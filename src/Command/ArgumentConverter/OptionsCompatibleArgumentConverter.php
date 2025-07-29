<?php

declare(strict_types=1);

namespace Sebihojda\Mbp\Command\ArgumentConverter;

use Sebihojda\Mbp\IO\GetOpt;

/**
 * An abstract base argument converter that handles the input and output files through GetOpt options.
 *
 * Example command line feature: ... --input1 in1.csv --input2 in2.csv --output1 out.csv
 *
 * Contains a partial implementation for the ArgumentConverterInterface:
 * - getInputStreams()
 * - getOutputStreams()
 */
abstract class OptionsCompatibleArgumentConverter implements ArgumentConverterInterface
{
    private const int MAX_INPUTS  = 3;
    private const int MAX_OUTPUTS = 3;

    public static function initOptions(): void
    {
        $short = static::extraShortOptions();
        $inputsLong = array_map(static fn($i) => 'input'.$i.':', range(1, self::MAX_INPUTS));
        $inputsShort = array_map(static fn($i) => 'output'.$i.':', range(1, self::MAX_OUTPUTS));
        $long = [...$inputsShort, ...$inputsLong, ...static::extraLongOptions()];
        GetOpt::initOptions($short, $long);
    }

    /**
     * To be overridden in child classes to add extra short options.
     */
    protected static function extraShortOptions(): string
    {
        return '';
    }

    /**
     * To be overridden in child classes to add extra long options.
     */
    protected static function extraLongOptions(): array
    {
        return [];
    }

    public static function getInputStreams(): array
    {
        $inputHandles = [];
        $options = GetOpt::getParsedOptions();
        for ($i = 1; $i <= self::MAX_INPUTS; $i++) {
            $optionName = 'input'.$i;
            if (isset($options[$optionName])) {
                $inputHandles[] = fopen($options[$optionName], 'rb');
            }
        }

        return $inputHandles;
    }

    public static function getOutputStreams(): array
    {
        $outputHandles = [];
        $options = GetOpt::getParsedOptions();
        for ($i = 1; $i <= self::MAX_OUTPUTS; $i++) {
            $optionName = 'output'.$i;
            if (isset($options[$optionName])) {
                $outputHandles[] = fopen($options[$optionName], 'wb');
            }
        }

        return $outputHandles;
    }
}
