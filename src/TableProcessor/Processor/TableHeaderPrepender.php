<?php

declare(strict_types=1);

namespace Sebihojda\Mbp\TableProcessor\Processor;

use Sebihojda\Mbp\Model\DataTable;
use Sebihojda\Mbp\TableProcessor\Config\TableHeaderPrependerConfig;
use Sebihojda\Mbp\TableProcessor\ConfigurableInterface;
use Sebihojda\Mbp\TableProcessor\TableProcessorConfigInterface;
use Sebihojda\Mbp\TableProcessor\TableProcessorInterface;
use InvalidArgumentException;

class TableHeaderPrepender implements TableProcessorInterface, ConfigurableInterface
{
    private TableHeaderPrependerConfig $config;

    public function process(DataTable ...$tables): array
    {
        $results = [];
        foreach ($tables as $table) {
            $result = DataTable::createEmpty($this->config->getHeaders());
            $result->appendRowsFrom($table);
            $results[] = $result;
        }

        return $results;
    }

    public function configure(TableProcessorConfigInterface $config): static
    {
        if (!$config instanceof TableHeaderPrependerConfig) {
            throw new InvalidArgumentException('Invalid config type');
        }
        $this->config = $config;

        return $this;
    }
}