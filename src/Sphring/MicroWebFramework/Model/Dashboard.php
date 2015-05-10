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

namespace Sphring\MicroWebFramework\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;

/**
 * Class Dashboard
 * @package Sphring\MicroWebFramework\Model
 * @Entity()
 * @Table(name="dashboard")
 */
class Dashboard
{
    /**
     * @var string
     * @Id()
     * @Column(type="string")
     */
    protected $id;
    /**
     * @var string
     * @Column(type="string", length=60)
     */
    protected $secret;
    /**
     * @var string
     * @Column(type="string", length=60)
     */
    protected $redirectUri;

    /**
     * @var ServiceDescribe[]
     * @OneToMany(targetEntity="ServiceDescribe", mappedBy="dashboard")
     */
    protected $serviceDescribes;

    function __construct($id, $secret, $redirectUri, $serviceDescribes = null)
    {
        $this->id = $id;
        $this->secret = $secret;
        $this->redirectUri = $redirectUri;
        if ($serviceDescribes === null || !is_array($serviceDescribes)) {
            $this->serviceDescribes = new ArrayCollection();
        } else {
            $this->serviceDescribes = new ArrayCollection($serviceDescribes);
        }
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @param string $secret
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;
    }

    /**
     * @return string
     */
    public function getRedirectUri()
    {
        return $this->redirectUri;
    }

    /**
     * @param string $redirectUri
     */
    public function setRedirectUri($redirectUri)
    {
        $this->redirectUri = $redirectUri;
    }

    /**
     * @return ServiceDescribe[]
     */
    public function getServiceDescribes()
    {
        return $this->serviceDescribes;
    }

    /**
     * @param ServiceDescribe[] $serviceDescribes
     */
    public function setServiceDescribes($serviceDescribes)
    {
        $this->serviceDescribes = new ArrayCollection($serviceDescribes);
    }

    /**
     * @param ServiceDescribe $serviceDescribes
     */
    public function addServiceDescribe(ServiceDescribe $serviceDescribes)
    {
        if ($this->serviceDescribes->contains($serviceDescribes)) {
            return;
        }
        $this->serviceDescribes->add($serviceDescribes);
    }

    /**
     * @param ServiceDescribe $serviceDescribes
     */
    public function removeServiceDescribe(ServiceDescribe $serviceDescribes)
    {
        if (!$this->serviceDescribes->contains($serviceDescribes)) {
            return;
        }
        $this->serviceDescribes->removeElement($serviceDescribes);
    }
}
