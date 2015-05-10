<?php
/**
 * Copyright (C) 2014 Arthur Halet
 *
 * This software is distributed under the terms and conditions of the 'MIT'
 * license which can be found in the file 'LICENSE' in this package distribution
 * or at 'http://opensource.org/licenses/MIT'.
 *
 * Author: Arthur Halet
 * Date: 29/03/2015
 */

namespace Sphring\MicroWebFramework\PlatesExtension;


use Arthurh\Sphring\Annotations\AnnotationsSphring\Required;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Sphring\MicroWebFramework\Doctrine\DoctrineBoot;
use Sphring\MicroWebFramework\Model\User;

class UserSession implements ExtensionInterface
{
    /**
     * @var bool
     */
    private $devMode = false;
    /**
     * @var User
     */
    private $user;
    /**
     * @var DoctrineBoot
     */
    private $doctrineBoot;

    public function getUser()
    {
        if ($this->user !== null) {
            return $this->user;
        }
        $entityManager = $this->doctrineBoot->getEntityManager();
        if (!isset($_SESSION['user'])) {
            return null;
        }
        $this->user = $entityManager->find(User::class, $_SESSION['user']);
        return $this->user;
    }

    public function register(Engine $engine)
    {
        $engine->registerFunction('userSession', [$this, 'getUser']);
    }

    /**
     * @return boolean
     */
    public function isDevMode()
    {
        return $this->devMode;
    }

    /**
     * @param boolean $devMode
     */
    public function setDevMode($devMode)
    {
        $this->devMode = (boolean)$devMode;
    }

    /**
     * @return DoctrineBoot
     */
    public function getDoctrineBoot()
    {
        return $this->doctrineBoot;
    }

    /**
     * @param DoctrineBoot $doctrineBoot
     * @Required
     */
    public function setDoctrineBoot($doctrineBoot)
    {
        $this->doctrineBoot = $doctrineBoot;
    }

}
