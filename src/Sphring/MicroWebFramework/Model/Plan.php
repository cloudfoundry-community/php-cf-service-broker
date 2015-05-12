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

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

/**
 * Class Plan
 * @package Sphring\MicroWebFramework\Model
 * @Entity()
 * @Table(name="plan")
 */
class Plan
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
    protected $free = true;
    /**
     * @var Metadata
     * @ManyToOne(targetEntity="Metadata")
     * @JoinColumn(name="metadata_id", referencedColumnName="name")
     */
    protected $metadata;
    /**
     * @var ServiceDescribe
     * @ManyToOne(targetEntity="ServiceDescribe", inversedBy="plans")
     * @JoinColumn(name="service_describe_id", referencedColumnName="id")
     */
    protected $serviceDescribe;

    function __construct($id, $name, $description)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
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
     * @return ServiceDescribe
     */
    public function getServiceDescribe()
    {
        return $this->serviceDescribe;
    }

    /**
     * @param ServiceDescribe $serviceDescribe
     */
    public function setServiceDescribe(ServiceDescribe $serviceDescribe)
    {
        $this->serviceDescribe = $serviceDescribe;
    }

    /**
     * @return boolean
     */
    public function isFree()
    {
        return $this->free;
    }

    /**
     * @param boolean $free
     */
    public function setFree($free)
    {
        $this->free = $free;
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
