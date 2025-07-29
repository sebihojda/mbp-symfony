<?php

declare(strict_types=1);

namespace Sebihojda\Mbp\IO;

/**
 * Acts as storage of getopt() results throughout the execution, in order for the options to be accessed from
 * multiple places in the code.
 */
final class GetOpt
{
    private static array $options;

    public static function initOptions(string $short, array $long): void
    {
        self::$options = getopt($short, $long);
    }

    public static function getParsedOptions(): array
    {
        return self::$options;
    }
}
