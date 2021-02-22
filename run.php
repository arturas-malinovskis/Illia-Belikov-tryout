<?php

declare(strict_types=1);

use App\Adapter\CsvFileDataAdapter;
use App\Kernel;
use App\Service\TransactionManager;

ini_set('date.timezone', 'Europe/Kiev');
require_once __DIR__ . '/vendor/autoload.php';

if (empty($argv[1])) {
    throw new InvalidArgumentException('Too few arguments');
}

if (!is_file($argv[1])) {
    die(sprintf("Argument %s is not a file", $argv[1]));
}

$adapter = new CsvFileDataAdapter($argv[1]);
$kernel = new Kernel(new TransactionManager($adapter->convert()));
$kernel->run();
