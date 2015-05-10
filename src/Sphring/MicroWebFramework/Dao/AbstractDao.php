<?php
/**
 * Copyright (C) 2015 Arthur Halet
 *
 * This software is distributed under the terms and conditions of the 'MIT'
 * license which can be found in the file 'LICENSE' in this package distribution
 * or at 'http://opensource.org/licenses/MIT'.
 *
 * Author: Arthur Halet
 * Date: 30/03/2015
 */


namespace Sphring\MicroWebFramework\Dao;


use Doctrine\ORM\EntityManager;
use Sphring\MicroWebFramework\Doctrine\DoctrineBoot;

class AbstractDao
{
    /**
     * @var DoctrineBoot
     */
    protected $doctrineBoot;
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @return DoctrineBoot
     */
    public function getDoctrineBoot()
    {
        return $this->doctrineBoot;
    }

    /**
     * @param DoctrineBoot $doctrineBoot
     */
    public function setDoctrineBoot(DoctrineBoot $doctrineBoot)
    {
        $this->doctrineBoot = $doctrineBoot;
        $this->entityManager = $doctrineBoot->getEntityManager();
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }


}