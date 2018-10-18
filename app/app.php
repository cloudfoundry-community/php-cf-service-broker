<?php

session_start();
require_once __DIR__ . '/../index.php';

$logVars = ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION', '_SERVER'];

$logger = new \Monolog\Logger("MicroWebFramework");
$logger->pushHandler(new \Monolog\Handler\StreamHandler('php://stdout', \Monolog\Logger::INFO));

$loggerSphring = \Arthurh\Sphring\Logger\LoggerSphring::getInstance();
$loggerSphring->setLogger($logger);
$loggerSphring->debug('$GLOBALS: ' . var_export(array_intersect_key ($GLOBALS, array_flip($logVars)), true));
$loggerSphring->debug("Body: \n" . file_get_contents("php://input"));

\Sphring\MicroWebFramework\MicroWebFrameworkRunner::getInstance(__DIR__ . '/../composer.lock');
