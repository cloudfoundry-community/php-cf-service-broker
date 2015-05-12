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
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * @var Request
     */
    private $request;

    function __construct()
    {

    }

    function auth()
    {
        if ($this->request === null) {
            return false;
        }
        $user = $this->request->server->get('PHP_AUTH_USER');
        $password = $this->request->server->get('PHP_AUTH_PW');
        if (empty($this->validUsers[$user])) {
            return false;
        }
        $cryptPass = $this->encoder->crypt($password);
        if ($cryptPass !== $this->validUsers[$user]) {
            return false;
        }
        return true;
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

    /**
     * @Required
     * @param Encoder $encoder
     */
    public function setEncoder(Encoder $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

}
