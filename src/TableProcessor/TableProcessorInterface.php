<?php

declare(strict_types=1);

namespace Sebihojda\Mbp\TableProcessor;

use Sebihojda\Mbp\Model\DataTable;

interface TableProcessorInterface
{
    /**
     * @return array|DataTable[]
     */
    public function process(DataTable ...$tables): array;
}
