<?php
ini_set("variables_order", "EGPCS");
error_reporting(E_ALL & ~E_NOTICE);
date_default_timezone_set('GMT');
$loader = require_once __DIR__ . '/../vendor/autoload.php';
$loader->add('Sphring\\MicroWebFramework\\', __DIR__);
$logger = new \Monolog\Logger("MicroWebFramework");
$logger->pushHandler(new \Monolog\Handler\StreamHandler('php://stdout', \Monolog\Logger::INFO));
$loggerSphring = \Arthurh\Sphring\Logger\LoggerSphring::getInstance();
$loggerSphring->setLogger($logger);
$loggerSphring->setWithFile(false);
$loggerSphring->setWithClass(true);

return $loader;