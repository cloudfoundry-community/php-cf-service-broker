<?php
/**
 * Copyright (C) 2014 Arthur Halet
 *
 * This software is distributed under the terms and conditions of the 'MIT'
 * license which can be found in the file 'LICENSE' in this package distribution
 * or at 'http://opensource.org/licenses/MIT'.
 *
 * Author: Arthur Halet
 * Date: 28/03/2015
 */

namespace Sphring\MicroWebFramework\Doctrine;


use Arhframe\Util\File;
use Arhframe\Util\Folder;
use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Cache\Cache;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Sphring\MicroWebFramework\CloudFoundry\CloudFoundry;

class DoctrineBoot
{
    /**
     * @var Cache
     */
    private $cache;
    /**
     * @var Serializer
     */
    private $serializer;
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var bool
     */
    private $devMode = false;
    /**
     * @var Folder
     */
    private $entityFolder;
    /**
     * @var array
     */
    private $connection = [];

    /**
     * @var File
     */
    private $fileCreation;
    /**
     * @var CloudFoundry
     */
    private $cloudFoundryBoot;

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    public function boot()
    {
        $this->serializer = SerializerBuilder::create()
            ->setDebug($this->devMode)
            ->build();
        $this->entityFolder->create();
        AnnotationRegistry::registerAutoloadNamespace(
            'JMS\Serializer\Annotation', __DIR__ . '/../../../../vendor/jms/serializer/src'
        );
        $proxyDoctrineFolder = new Folder(sys_get_temp_dir() . '/doctrine');
        $config = Setup::createAnnotationMetadataConfiguration([$this->entityFolder->absolute()],
            $this->isDevMode(), $proxyDoctrineFolder->absolute());
        if ($this->cache !== null) {
            $config->setQueryCacheImpl($this->getCache());
            $config->setResultCacheImpl($this->getCache());
        }
        $this->entityManager = $this->createEntityManager($config);
        $debugStack = new DebugStack();
        $this->entityManager->getConnection()->getConfiguration()->setSQLLogger($debugStack);

        if ($this->getFileCreation()->getContent() == 1) {
            return;
        }

        if ($proxyDoctrineFolder->isFolder()) {
            $proxyDoctrineFolder->removeFiles();
        }
        $tool = new SchemaTool($this->entityManager);
        $metadatas = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $proxyDoctrineFolder->create();
        $this->entityManager->getProxyFactory()->generateProxyClasses($metadatas, $proxyDoctrineFolder->absolute());
        if ($this->cloudFoundryBoot->isInCloudFoundry()) {
            $tool->updateSchema($metadatas);
        } else {
            $tool->createSchema($metadatas);
        }
        $this->getFileCreation()->setContent(1);
    }

    /**
     * @return boolean
     *
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
     * @return Cache
     */
    public function getCache()
    {
        if ($this->cloudFoundryBoot !== null && $this->cloudFoundryBoot->isInCloudFoundry()) {
            return $this->cloudFoundryBoot->getDoctrineCache();
        }
        return $this->cache;
    }

    /**
     * @param Cache $cache
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
    }

    private function createEntityManager($config)
    {
        if ($this->cloudFoundryBoot->isInCloudFoundry()) {
            return EntityManager::create(["url" => $this->cloudFoundryBoot->getDatabaseUrl()], $config);
        }
        return EntityManager::create($this->connection, $config);
    }

    /**
     * @return File
     */
    public function getFileCreation()
    {
        if ($this->cloudFoundryBoot !== null && $this->cloudFoundryBoot->isInCloudFoundry()) {
            $this->fileCreation = $this->cloudFoundryBoot->getFileCreation();
        }
        return $this->fileCreation;
    }

    /**
     * @param File $fileCreation
     */
    public function setFileCreation(File $fileCreation)
    {
        $this->fileCreation = $fileCreation;
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
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return Folder
     */
    public function getEntityFolder()
    {
        return $this->entityFolder;
    }

    /**
     * @param Folder $entityFolder
     * @Required
     */
    public function setEntityFolder(Folder $entityFolder)
    {
        $this->entityFolder = $entityFolder;
    }

    /**
     * @return array
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param array $connection
     * @Required
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return Serializer
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * @param Serializer $serializer
     */
    public function setSerializer(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @return CloudFoundry
     */
    public function getCloudFoundryBoot()
    {
        return $this->cloudFoundryBoot;
    }

    /**
     * @Required()
     * @param CloudFoundry $cloudFoundryBoot
     */
    public function setCloudFoundryBoot(CloudFoundry $cloudFoundryBoot)
    {
        $this->cloudFoundryBoot = $cloudFoundryBoot;
    }


}
