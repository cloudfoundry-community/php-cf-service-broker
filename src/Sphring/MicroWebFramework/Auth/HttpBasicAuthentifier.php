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

namespace Sphring\MicroWebFramework\Auth;


use Arthurh\Sphring\Annotations\AnnotationsSphring\Required;
use Sphring\MicroWebFramework\Security\Encoder;

class HttpBasicAuthentifier
{
    CONST REALM = "Service Broker Realm";
    /**
     * @var Encoder
     */
    private $encoder;
    /**
     * @var string[]
     */
    private $validUsers;

    function __construct()
    {

    }

    function auth()
    {
        if (empty($this->validUsers[$_SERVER['PHP_AUTH_USER']])) {
            return false;
        }
        $cryptPass = $this->encoder->crypt($_SERVER['PHP_AUTH_PW']);
        if ($cryptPass !== $this->validUsers[$_SERVER['PHP_AUTH_USER']]) {
            return false;
        }
        return true;
    }

    /**
     * @Required
     * @param Encoder $encoder
     */
    public function setEncoder(Encoder $encoder)
    {
        $this->encoder = $encoder;
    }


    /**
     *
     * @return \string[]
     */
    public function getValidUsers()
    {
        return $this->validUsers;
    }

    /**
     * @Required
     * @param \string[] $validUsers
     */
    public function setValidUsers($validUsers)
    {
        $this->validUsers = $validUsers;
    }

    /**
     * @return Encoder
     */
    public function getEncoder()
    {
        return $this->encoder;
    }


}
