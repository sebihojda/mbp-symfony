<?php

declare(strict_types=1);

namespace Sebihojda\Mbp\Command\ArgumentConverter;

/**
 * An abstract base argument converter that wires the input and output to STDIN and STDOUT.
 * This means that the tool can only consume from one input and produce to only one output.
 *
 *  Contains a partial implementation for the ArgumentConverterInterface:
 *  - getInputStreams()
 *  - getOutputStreams()
 */
abstract class PipingCompatibleArgumentConverter implements ArgumentConverterInterface
{
    public static function getInputStreams(): array
    {
        return [STDIN];
    }

    public static function getOutputStreams(): array
    {
        return [STDOUT];
    }
}