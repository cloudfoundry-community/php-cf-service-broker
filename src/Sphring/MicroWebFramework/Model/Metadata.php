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


namespace Sphring\MicroWebFramework\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Rhumsaa\Uuid\Uuid;

/**
 * Class Metadata
 * @package Sphring\MicroWebFramework\Model
 * @Entity()
 * @Table(name="metadata")
 */
class Metadata
{

    /**
     * @var String
     * @Id()
     * @Column(type="string", length=20)
     */
    private $name;
    /**
     * @var String
     * @Column(type="string", nullable=true)
     */
    private $displayName;
    /**
     * @var String
     * @Column(type="string", nullable=true)
     */
    private $description;
    /**
     * @var String
     * @Column(type="string", nullable=true)
     */
    private $imageUrl;

    /**
     * @var String
     * @Column(type="text", nullable=true)
     */
    private $longDescription;
    /**
     * @var String
     * @Column(type="string", nullable=true)
     */
    private $providerDisplayName;
    /**
     * @var String
     * @Column(type="string", nullable=true)
     */
    private $documentationUrl;
    /**
     * @var String
     * @Column(type="string", nullable=true)
     */
    private $supportUrl;
    /**
     * @var array
     * @Column(type="string")
     */
    private $bullets = '{}';
    /**
     * @var array
     * @Column(type="string")
     */
    private $costs = '{}';

    public function __construct($name, $displayName = null)
    {
        if ($name === null && $displayName !== null) {
            $this->name = $displayName;
        } else if ($name === null) {
            $this->name = Uuid::uuid1()->toString();
        } else {
            $this->name = $name;
        }
        $this->displayName = $displayName;
    }


    /**
     * @return String
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param String $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return String
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param String $displayName
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    /**
     * @return String
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param String $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return String
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * @param String $imageUrl
     */
    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;
    }

    /**
     * @return String
     */
    public function getLongDescription()
    {
        return $this->longDescription;
    }

    /**
     * @param String $longDescription
     */
    public function setLongDescription($longDescription)
    {
        $this->longDescription = $longDescription;
    }

    /**
     * @return String
     */
    public function getProviderDisplayName()
    {
        return $this->providerDisplayName;
    }

    /**
     * @param String $providerDisplayName
     */
    public function setProviderDisplayName($providerDisplayName)
    {
        $this->providerDisplayName = $providerDisplayName;
    }

    /**
     * @return String
     */
    public function getDocumentationUrl()
    {
        return $this->documentationUrl;
    }

    /**
     * @param String $documentationUrl
     */
    public function setDocumentationUrl($documentationUrl)
    {
        $this->documentationUrl = $documentationUrl;
    }

    /**
     * @return String
     */
    public function getSupportUrl()
    {
        return $this->supportUrl;
    }

    /**
     * @param String $supportUrl
     */
    public function setSupportUrl($supportUrl)
    {
        $this->supportUrl = $supportUrl;
    }

    /**
     * @return array
     */
    public function getBullets()
    {
        return json_decode($this->bullets, true);
    }

    /**
     * @param array $bullets
     */
    public function setBullets(array $bullets)
    {
        $this->bullets = json_encode($bullets);
    }

    /**
     * @return array
     */
    public function getCosts()
    {
        return json_decode($this->costs);
    }

    /**
     * @param array $costs
     */
    public function setCosts(array $costs)
    {
        $this->costs = json_encode($costs);
    }

}