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

namespace Sphring\MicroWebFramework\Util;


use Arthurh\Sphring\Annotations\AnnotationsSphring\Required;
use Rhumsaa\Uuid\Uuid;
use Sphring\MicroWebFramework\Doctrine\DoctrineBoot;
use Sphring\MicroWebFramework\Model\Dashboard;
use Sphring\MicroWebFramework\Model\Metadata;
use Sphring\MicroWebFramework\Model\Plan;
use Sphring\MicroWebFramework\Model\ServiceDescribe;

class ServiceDescribeCreator
{
    private $services;
    /**
     * @var DoctrineBoot
     */
    private $doctrineBoot;

    public function create()
    {
        foreach ($this->services['services'] as $service) {
            $this->createServiceDescribe($service);
        }
    }

    public function createServiceDescribe(array $service)
    {
        $em = $this->doctrineBoot->getEntityManager();
        $repo = $em->getRepository(ServiceDescribe::class);
        $id = Uuid::uuid5(Uuid::NAMESPACE_OID, $service['name'])->toString();
        $plans = [];
        foreach ($service['plans'] as $plan) {
            $plans[] = $this->createPlan($plan);
        }
        $bindable = (!isset($service['bindable']) || $service['bindable']) ? true : false;
        $planUpdateable = (!isset($service['plan_updateable']) || $service['plan_updateable']) ? true : false;
        $requires = (!isset($service['requires'])) ? [] : $service['requires'];
        $tags = (!isset($service['tags'])) ? [] : $service['tags'];
        $serviceDescribe = $repo->find($id);
        if ($serviceDescribe !== null) {
            $serviceDescribe->setPlans($plans);
            $serviceDescribe->setBindable($bindable);
        } else {
            $serviceDescribe = new ServiceDescribe($id, $service['name'], $service['description'], $plans, $bindable);
        }
        if (isset($service['dashboard_client'])) {
            $serviceDescribe->setDashboard($this->createDashboard($service['dashboard_client']));
        }
        if (isset($service['metadata'])) {
            $serviceDescribe->setMetadata($this->createMetadata($service['metadata']));
        }
        $serviceDescribe->setPlanUpdateable($planUpdateable);
        $serviceDescribe->setRequires($requires);
        $serviceDescribe->setTags($tags);
        $em->persist($serviceDescribe);
        $em->flush();
    }

    public function createPlan(array $plan)
    {
        $em = $this->doctrineBoot->getEntityManager();
        $id = Uuid::uuid5(Uuid::NAMESPACE_OID, $plan['name'])->toString();
        $repo = $em->getRepository(Plan::class);
        $planObject = $repo->find($id);
        $free = (!isset($service['free']) || $service['free']) ? true : false;

        if ($planObject === null) {
            $planObject = new Plan($id, $plan['name'], $plan['description']);
        }
        if (isset($plan['metadata'])) {
            $planObject->setMetadata($this->createMetadata($plan['metadata']));
        }
        $planObject->setFree($free);
        $em->persist($planObject);
        return $planObject;
    }

    public function createMetadata(array $metadata)
    {
        $em = $this->doctrineBoot->getEntityManager();
        $repo = $em->getRepository(Metadata::class);
        $metadataObject = null;
        if (!isset($metadata['name'])) {
            $metadata['name'] = $metadata['displayName'];
        }
        if ($metadata['name'] !== null) {
            $metadataObject = $repo->find($metadata['name']);
        }

        if ($metadataObject === null) {
            $metadataObject = new Metadata($metadata['name'], $metadata['displayName']);
        }
        $bullets = (!isset($metadata['bullets'])) ? [] : $metadata['bullets'];
        $costs = (!isset($metadata['costs'])) ? [] : $metadata['costs'];
        $metadataObject->setBullets($bullets);
        $metadataObject->setCosts($costs);
        $metadataObject->setDescription($metadata['description']);
        $metadataObject->setDocumentationUrl($metadata['documentationUrl']);
        $metadataObject->setImageUrl($metadata['imageUrl']);
        $metadataObject->setLongDescription($metadata['longDescription']);
        $metadataObject->setProviderDisplayName($metadata['providerDisplayName']);
        $metadataObject->setSupportUrl($metadata['supportUrl']);
        $em->persist($metadataObject);
        return $metadataObject;
    }

    public function createDashboard(array $dashboard)
    {
        $em = $this->doctrineBoot->getEntityManager();
        $repo = $em->getRepository(Dashboard::class);
        $dashboardObject = $repo->find($dashboard['id']);
        if ($dashboardObject !== null) {
            $dashboardObject->setSecret($dashboard['secret']);
            $dashboardObject->setRedirectUri($dashboard['redirect_uri']);
            return $dashboardObject;
        }
        $dashboardObject = new Dashboard($dashboard['id'], $dashboard['secret'], $dashboard['redirect_uri']);
        $em->persist($dashboardObject);
        return $dashboardObject;
    }

    /**
     * @return mixed
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @param mixed $services
     * @Required
     */
    public function setServices(array $services)
    {
        $this->services = $services;
    }

    /**
     * @return DoctrineBoot
     */
    public function getDoctrineBoot()
    {
        return $this->doctrineBoot;
    }

    /**
     * @Required
     * @param DoctrineBoot $doctrineBoot
     */
    public function setDoctrineBoot(DoctrineBoot $doctrineBoot)
    {
        $this->doctrineBoot = $doctrineBoot;
    }

}
