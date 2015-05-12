<?php
/**
 * Copyright (C) 2015 Arthur Halet
 *
 * This software is distributed under the terms and conditions of the 'MIT'
 * license which can be found in the file 'LICENSE' in this package distribution
 * or at 'http://opensource.org/licenses/MIT'.
 *
 * Author: Arthur Halet
 * Date: 12/05/2015
 */


namespace Sphring\MicroWebFramework\CloudFoundry;


use Arhframe\Util\File;
use CfCommunity\CfHelper\CfHelper;
use Doctrine\Common\Cache\FilesystemCache;

class CloudFoundry
{
    /**
     * @var CfHelper
     */
    private $cfHelper;

    public function __construct()
    {
        $this->cfHelper = CfHelper::getInstance();
    }

    public function getDatabaseUrl()
    {
        if (!$this->isInCloudFoundry()) {
            return null;
        }
        $dbConnector = $this->cfHelper->getDatabaseConnector();
        $dbConnector->load();
        $credentials = $dbConnector->getCredentials();
        if ($credentials === null) {
            return null;
        }
        $port = null;
        if (isset($credentials['port'])) {
            $port = ":" . $credentials['port'];
        }
        $password = null;
        if (isset($credentials['pass'])) {
            $password = ":" . $credentials['pass'];
        }
        return $credentials['type'] . "://" . $credentials['user']
        . $password . '@' . $credentials['host'] . $port . '/' . $credentials['database'];
    }

    public function isInCloudFoundry()
    {
        return $this->cfHelper->isInCloudFoundry();
    }

    public function getDoctrineCache()
    {
        if (!$this->isInCloudFoundry()) {
            return null;
        }
        return new FilesystemCache(sys_get_temp_dir());
    }

    public function getFolderCache()
    {
        if (!$this->isInCloudFoundry()) {
            return null;
        }
        return sys_get_temp_dir();
    }

    public function getFileCreation()
    {
        if (!$this->isInCloudFoundry()) {
            return null;
        }
        $file = new File(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'db.ct');
        $file->createFolder();
        if (!$file->isFile()) {
            $file->setContent(0);
        }
        return $file;
    }
}
