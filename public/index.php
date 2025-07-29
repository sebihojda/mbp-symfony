<?php
/*
require __DIR__ . '/../vendor/autoload.php';

print 'Hello there from PHP CLI'.PHP_EOL;

phpinfo();*/
use Sebihojda\Mbp\Kernel;

require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool)$context['APP_DEBUG']);
};
