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

namespace Sphring\MicroWebFramework\Controller\ServiceBroker;


use Sphring\MicroWebFramework\Controller\IndexController;
use Sphring\MicroWebFramework\Model\Metadata;
use Sphring\MicroWebFramework\Model\Plan;
use Sphring\MicroWebFramework\Model\ServiceDescribe;

class Catalog extends IndexController
{

    public function action()
    {
        $action = parent::action();
        if ($action !== null) {
            return $action;
        }
        $em = $this->getDoctrineBoot()->getEntityManager();
        $repo = $em->getRepository(ServiceDescribe::class);
        $serviceDescribes = $repo->findAll();
        $services = [];
        foreach ($serviceDescribes as $serviceDescribe) {

            $services['services'][] = $this->getServiceDescribeAsArray($serviceDescribe);
        }
        return json_encode($services);
    }

    public function getServiceDescribeAsArray(ServiceDescribe $serviceDescribe)
    {
        $service = [];
        $service['id'] = $serviceDescribe->getId();
        $service['name'] = $serviceDescribe->getName();
        $service['description'] = $serviceDescribe->getDescription();
        $service['bindable'] = $serviceDescribe->isBindable();
        $service['instances_retrievable'] = $serviceDescribe->isInstancesRetrievable();
        $service['bindings_retrievable'] = $serviceDescribe->isBindingsRetrievable();
        $service['plan_updateable'] = $serviceDescribe->isPlanUpdateable();
        $service['requires'] = $serviceDescribe->getRequires();
        $service['tags'] = $serviceDescribe->getTags();
        $service['plans'] = [];
        foreach ($serviceDescribe->getPlans() as $plan) {
            $service['plans'][] = $this->getPlanAsArray($plan);
        }
        if ($serviceDescribe->getMetadata() !== null) {
            $service['metadata'] = $this->getMetadataServiceDescribeAsArray($serviceDescribe->getMetadata());
        }
        if ($serviceDescribe->getDashboard() === null) {
            $services['services'][] = $service;
            return $service;
        }
        $service['dashboard_client']['id'] = $serviceDescribe->getDashboard()->getId();
        $service['dashboard_client']['secret'] = $serviceDescribe->getDashboard()->getSecret();
        $service['dashboard_client']['redirect_uri'] = $serviceDescribe->getDashboard()->getRedirectUri();
        return $service;
    }

    public function getPlanAsArray(Plan $plan)
    {
        $planArray = [];
        $planArray['id'] = $plan->getId();
        $planArray['name'] = $plan->getName();
        $planArray['description'] = $plan->getDescription();
        $planArray['free'] = $plan->isFree();
        if ($plan->getMetadata() !== null) {
            $planArray['metadata'] = $this->getMetadataPlanAsArray($plan->getMetadata());
        }
        return $planArray;
    }

    public function getMetadataPlanAsArray(Metadata $metadata)
    {
        $metadataArray = [];
        $metadataArray['name'] = $metadata->getName();
        $metadataArray['description'] = $metadata->getDescription();
        $metadataArray['bullets'] = $metadata->getBullets();
        $metadataArray['costs'] = $metadata->getCosts();
        $metadataArray['displayName'] = $metadata->getDisplayName();
        return $metadataArray;
    }

    public function getMetadataServiceDescribeAsArray(Metadata $metadata)
    {
        $metadataArray = [];
        $metadataArray['name'] = $metadata->getName();
        $metadataArray['description'] = $metadata->getDescription();
        $metadataArray['displayName'] = $metadata->getDisplayName();
        $metadataArray['imageUrl'] = $metadata->getImageUrl();
        $metadataArray['longDescription'] = $metadata->getLongDescription();
        $metadataArray['providerDisplayName'] = $metadata->getProviderDisplayName();
        $metadataArray['documentationUrl'] = $metadata->getDocumentationUrl();
        $metadataArray['supportUrl'] = $metadata->getSupportUrl();

        return $metadataArray;
    }
}
