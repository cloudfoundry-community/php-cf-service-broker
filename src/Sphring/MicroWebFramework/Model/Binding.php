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
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\Table;

/**
 * Class App
 * @package Sphring\MicroWebFramework\Model
 * @Entity()
 * @Table(name="binding")
 */
class Binding
{
    /**
     * @var string
     * @Id()
     * @Column(type="string")
     */
    protected $id;

    /**
     * @var string
     * @Column(type="string")
     */
    protected $appGuid;
    /**
     * @var ServiceInstance[]
     * @ManyToMany(targetEntity="ServiceInstance", inversedBy="apps")
     */
    protected $serviceInstances;

    function __construct($id, $appGuid)
    {
        $this->id = $id;
        $this->appGuid = $appGuid;
        $this->serviceInstances = new ArrayCollection();
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
     * @return ServiceInstance[]
     */
    public function getServiceInstances()
    {
        return $this->serviceInstances;
    }

    /**
     * @param ServiceInstance[] $serviceInstances
     */
    public function setServiceInstances($serviceInstances)
    {
        $this->serviceInstances = new ArrayCollection($serviceInstances);
    }

    /**
     * @param ServiceInstance $serviceInstance
     */
    public function addServiceInstance(ServiceInstance $serviceInstance)
    {
        if ($this->serviceInstances->contains($serviceInstance)) {
            return;
        }
        $this->serviceInstances->add($serviceInstance);
        $serviceInstance->addBinding($this);
    }

    /**
     * @param ServiceInstance $serviceInstance
     */
    public function removeServiceInstance(ServiceInstance $serviceInstance)
    {
        if (!$this->serviceInstances->contains($serviceInstance)) {
            return;
        }
        $this->serviceInstances->removeElement($serviceInstance);
        $serviceInstance->removeBinding($this);
    }

    /**
     * @return mixed
     */
    public function getAppGuid()
    {
        return $this->appGuid;
    }

    /**
     * @param mixed $appGuid
     */
    public function setAppGuid($appGuid)
    {
        $this->appGuid = $appGuid;
    }

}
