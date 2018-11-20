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
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;

/**
 * Class ServiceDescribe
 * @package Sphring\MicroWebFramework\Model
 * @Entity()
 * @Table(name="service_describe")
 */
class ServiceDescribe
{
    /**
     * @var string
     * @Id()
     * @Column(type="string")
     */
    protected $id;

    /**
     * @var string
     * @Column(type="string", length=30)
     */
    protected $name;
    /**
     * @var string
     * @Column(type="string", length=100, nullable=true)
     */
    protected $description;
    /**
     * @var bool
     * @Column(type="boolean")
     */
    protected $bindable = true;
    /**
     * @var bool
     * @Column(type="boolean")
     */
    protected $planUpdateable = true;

    /**
     * @var String
     * @Column(type="string")
     */
    protected $requires = '{}';

    /**
     * @var String
     * @Column(type="string")
     */
    protected $tags = '{}';

    /**
     * @var Plan[]
     * @OneToMany(targetEntity="Plan", mappedBy="serviceDescribe")
     */
    protected $plans;
    /**
     * @var Metadata
     * @ManyToOne(targetEntity="Metadata")
     * @JoinColumn(name="metadata_id", referencedColumnName="name")
     */
    protected $metadata;
    /**
     * @var Dashboard
     * @ManyToOne(targetEntity="Dashboard", inversedBy="dashboard")
     * @JoinColumn(name="dashboard_id", referencedColumnName="id")
     */
    protected $dashboard;

    function __construct($id, $name, $description, $plans = null, $bindable = true)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->bindable = (boolean)$bindable;
        if ($plans === null || !is_array($plans)) {
            $this->plans = new ArrayCollection();
        } else {
            $this->setPlans($plans);
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return boolean
     */
    public function isBindable()
    {
        return $this->bindable;
    }

    /**
     * Specifies whether the "Fetching a Service Instance" endpoint is supported for all plans
     *
     * @return boolean
     *
     * @see https://github.com/openservicebrokerapi/servicebroker/blob/v2.14/spec.md#fetching-a-service-instance
     */
    public function isInstancesRetrievable()
    {
        return false;
    }

    /**
     * Specifies whether the "Fetching a Service Binding" endpoint is supported for all plans.
     *
     * @return boolean
     *
     * @see https://github.com/openservicebrokerapi/servicebroker/blob/v2.14/spec.md#fetching-a-service-binding
     */
    public function isBindingsRetrievable()
    {
        return false;
    }

    /**
     * @param boolean $bindable
     */
    public function setBindable($bindable)
    {
        $this->bindable = $bindable;
    }

    public function addPlan(Plan $plan)
    {
        if ($this->plans->contains($plan)) {
            return;
        }
        $this->plans->add($plan);
        $plan->setServiceDescribe($this);
    }

    public function removePlan(Plan $plan)
    {
        if (!$this->plans->contains($plan)) {
            return;
        }
        $this->plans->removeElement($plan);
        $plan->setServiceDescribe(null);
    }

    /**
     * @return Dashboard
     */
    public function getDashboard()
    {
        return $this->dashboard;
    }

    /**
     * @param Dashboard $dashboard
     */
    public function setDashboard($dashboard)
    {
        $this->dashboard = $dashboard;
        $dashboard->addServiceDescribe($this);
    }

    /**
     * @return Plan[]
     */
    public function getPlans()
    {
        return $this->plans;
    }

    /**
     * @param Plan[] $plans
     */
    public function setPlans($plans)
    {
        $this->plans = new ArrayCollection();
        foreach ($plans as $plan) {
            $this->addPlan($plan);
        }
    }

    /**
     * @return boolean
     */
    public function isPlanUpdateable()
    {
        return $this->planUpdateable;
    }

    /**
     * @param boolean $planUpdateable
     */
    public function setPlanUpdateable($planUpdateable)
    {
        $this->planUpdateable = $planUpdateable;
    }

    /**
     * @return array
     */
    public function getRequires()
    {
        return json_decode($this->requires, true);
    }

    /**
     * @param array $requires
     */
    public function setRequires(array $requires)
    {
        $this->requires = json_encode($requires);
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return json_decode($this->tags, true);
    }

    /**
     * @param array $tags
     */
    public function setTags(array $tags)
    {
        $this->tags = json_encode($tags);
    }

    /**
     * @return Metadata
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @param Metadata $metadata
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;
    }

}
