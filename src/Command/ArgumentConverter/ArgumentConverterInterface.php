<?php

declare(strict_types=1);

namespace Sebihojda\Mbp\Command\ArgumentConverter;

use Sebihojda\Mbp\TableProcessor\TableProcessorConfigInterface;

interface ArgumentConverterInterface
{
    /**
     * Initializes the GetOpt helper
     */
    public static function initOptions(): void;

    /**
     * Returns the list of input streams for the current tool execution
     */
    public static function getInputStreams(): array;

    /**
     * Returns the list of output streams for the current tool execution
     */
    public static function getOutputStreams(): array;

    /**
     * Extracts table processor configuration for the current tool execution
     */
    public static function extractTableProcessorConfig(): ?TableProcessorConfigInterface;
}
