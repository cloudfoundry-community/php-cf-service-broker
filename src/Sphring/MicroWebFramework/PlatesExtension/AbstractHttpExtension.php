<?php
/**
 * Copyright (C) 2015 Arthur Halet
 *
 * This software is distributed under the terms and conditions of the 'MIT'
 * license which can be found in the file 'LICENSE' in this package distribution
 * or at 'http://opensource.org/licenses/MIT'.
 *
 * Author: Arthur Halet
 * Date: 20/03/2015
 */


namespace Sphring\MicroWebFramework\PlatesExtension;


use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

abstract class AbstractHttpExtension implements ExtensionInterface
{

    private $httpName;

    abstract public function register(Engine $engine);

    public function getHttpName()
    {
        if (!empty($this->httpName)) {
            return $this->httpName;
        }
        $servername = dirname($_SERVER['SCRIPT_NAME']);
        if ($servername == '/') {
            $servername = null;
        }
        $port = $this->getPort();
        $this->httpName = $this->getProtocol() . '://' . $_SERVER["SERVER_NAME"] . $port . $servername;
        return $this->httpName;
    }

    private function getPort()
    {
        if (!($_SERVER['SERVER_PORT'] == 80 && $this->getProtocol() == 'http') &&
            !($_SERVER['SERVER_PORT'] == 443 && $this->getProtocol() == 'https')
        ) {
            return ':' . $_SERVER['SERVER_PORT'];
        }
        return null;
    }

    private function getProtocol()
    {
        if (!empty($_SERVER["REQUEST_SCHEME"])) {
            return $_SERVER["REQUEST_SCHEME"];
        }
        if (!empty($_SERVER["HTTPS"])) {
            return 'https';
        } else {
            return 'http';
        }

    }
}
