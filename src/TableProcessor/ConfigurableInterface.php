<?php

declare(strict_types=1);

namespace Sebihojda\Mbp\TableProcessor;

interface ConfigurableInterface
{
    public function configure(TableProcessorConfigInterface $config): static;
}
