<?php
require 'vendor/autoload.php';
$sphring = new \Arthurh\Sphring\Sphring(__DIR__ . '/sphring/main.yml');
$sphring->setRootProject(__DIR__);
$sphring->setComposerLockFile(__DIR__ . '/composer.lock');

$sphring->loadContext();
$doctrineBoot = $sphring->getBean('microwebframe.doctrine');
return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($doctrineBoot->getEntityManager());