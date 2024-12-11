<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Level;


// create a log channel
$log = new Logger('name');
$log->pushHandler(new StreamHandler('/var/www/html/wmsd/logs/log.log', 100));

// add records to the log
$log->warning('Foo', array('extra' => 'bar'));
$log->error('Bar');