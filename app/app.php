<?php
/**
 * Copyright (C) 2014 Arthur Halet
 *
 * This software is distributed under the terms and conditions of the 'MIT'
 * license which can be found in the file 'LICENSE' in this package distribution
 * or at 'http://opensource.org/licenses/MIT'.
 *
 * Author: Arthur Halet
 * Date: 10/05/2015
 */
/**
 * Copyright 2018. Plesk International GmbH.
 *
 * This software is distributed under the terms and conditions of the 'MIT'
 * license which can be found in the file 'LICENSE' in this package distribution
 * or at 'http://opensource.org/licenses/MIT'.
 */

session_start();
require_once __DIR__ . '/../index.php';

$logVars = ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION', '_SERVER'];

$logger = new \Monolog\Logger("MicroWebFramework");
$logger->pushHandler(new \Monolog\Handler\StreamHandler('php://stdout', \Monolog\Logger::WARNING));
//$logger->pushHandler(new \Monolog\Handler\StreamHandler(__DIR__ . '/../cache/log.log', \Monolog\Logger::WARNING));

$loggerSphring = \Arthurh\Sphring\Logger\LoggerSphring::getInstance();
$loggerSphring->setLogger($logger);
$loggerSphring->debug('$GLOBALS: ' . var_export(array_intersect_key ($GLOBALS, array_flip($logVars)), true));
$loggerSphring->debug("Body: \n" . file_get_contents("php://input"));

\Sphring\MicroWebFramework\MicroWebFrameworkRunner::getInstance(__DIR__ . '/../composer.lock');
