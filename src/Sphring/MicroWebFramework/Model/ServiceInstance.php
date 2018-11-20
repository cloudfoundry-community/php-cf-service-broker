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

namespace Sphring\MicroWebFramework\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Table;

/**
 * Class ServiceInstance
 * @package Sphring\MicroWebFramework\Model
 * @Entity()
 * @Table(name="service_instance")
 */
class ServiceInstance
{
    /**
     * @var string
     * @Id()
     * @Column(type="string")
     */
    protected $id;

    /**
     * @var ServiceDescribe
     * @OneToOne(targetEntity="ServiceDescribe")
     * @JoinColumn(name="service_describe_id", referencedColumnName="id")
     */
    protected $serviceDescribe;

    /**
     * @var Plan
     * @OneToOne(targetEntity="Plan")
     * @JoinColumn(name="plan_id", referencedColumnName="id")
     */
    protected $plan;

    /**
     * @var string
     * @Column(type="string")
     */
    protected $organization;

    /**
     * @var string
     * @Column(type="string")
     */
    protected $space;

    /**
     * @var Binding[]
     * @ManyToMany(targetEntity="Binding", inversedBy="serviceInstances")
     * @JoinTable(name="service_instances_bindings")
     */
    protected $bindings;

    /**
     * @var array
     * @Column(type="text")
     */
    protected $credentials = '{}';

    function __construct($id, $serviceDescribe, $plan, $organization, $space)
    {
        $this->id = $id;
        $this->serviceDescribe = $serviceDescribe;
        $this->plan = $plan;
        $this->organization = $organization;
        $this->space = $space;
        $this->bindings = new ArrayCollection();
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
     * @return ServiceDescribe
     */
    public function getServiceDescribe()
    {
        return $this->serviceDescribe;
    }

    /**
     * @param ServiceDescribe $serviceDescribe
     */
    public function setServiceDescribe($serviceDescribe)
    {
        $this->serviceDescribe = $serviceDescribe;
    }

    /**
     * @return Plan
     */
    public function getPlan()
    {
        return $this->plan;
    }

    /**
     * @param Plan $plan
     */
    public function setPlan($plan)
    {
        $this->plan = $plan;
    }

    /**
     * @return string
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param string $organization
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
    }

    /**
     * @return string
     */
    public function getSpace()
    {
        return $this->space;
    }

    /**
     * @param string $space
     */
    public function setSpace($space)
    {
        $this->space = $space;
    }

    /**
     * @return Binding[]
     */
    public function getBindings()
    {
        return $this->bindings;
    }

    /**
     * @param Binding[] $apps
     */
    public function setBindings($bindings)
    {
        $this->bindings = new ArrayCollection($bindings);
    }

    /**
     * @param Binding $app
     */
    public function removeBinding(Binding $binding)
    {
        if (!$this->bindings->contains($binding)) {
            return;
        }
        $this->bindings->removeElement($binding);
    }

    /**
     * @param Binding $app
     */
    public function addBinding(Binding $binding)
    {
        if ($this->bindings->contains($binding)) {
            return;
        }
        $this->bindings->add($binding);
    }

    /**
     * @return array
     */
    public function getCredentials()
    {
        return json_decode($this->credentials, true);
    }

    /**
     * @param array $credentials
     */
    public function setCredentials(array $credentials)
    {
        $this->credentials = json_encode($credentials);
    }

    public function __toString()
    {
        return json_encode(
            [
                'id' => $this->id,
                'serviceDescribe' => $this->serviceDescribe,
                'plan' => $this->plan,
                'organization' => $this->organization,
                'space' => $this->space,
                'credentials' => $this->credentials,
            ],
            JSON_FORCE_OBJECT
        );
    }
}
