<?php

session_start();
require_once __DIR__ . '/../index.php';
\Sphring\MicroWebFramework\MicroWebFrameworkRunner::getInstance(__DIR__ . '/../composer.lock');
