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

namespace Sphring\MicroWebFramework\Controller\ServiceBroker;


use Sphring\MicroWebFramework\Controller\IndexController;
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
            $service = [];
            if (!$serviceDescribe instanceof ServiceDescribe) {
                continue;
            }
            $service['id'] = $serviceDescribe->getId();
            $service['name'] = $serviceDescribe->getName();
            $service['description'] = $serviceDescribe->getDescription();
            $service['plans'] = [];
            foreach ($serviceDescribe->getPlans() as $plan) {
                $planJson = [];
                $planJson['id'] = $plan->getId();
                $planJson['name'] = $plan->getName();
                $planJson['description'] = $plan->getDescription();
                $planJson['free'] = $plan->isFree();
                $service['plans'][] = $planJson;
            }
            if ($serviceDescribe->getDashboard() === null) {
                $services['services'][] = $service;
                continue;
            }
            $service['dashboard_client']['id'] = $serviceDescribe->getDashboard()->getId();
            $service['dashboard_client']['secret'] = $serviceDescribe->getDashboard()->getSecret();
            $service['dashboard_client']['redirect_uri'] = $serviceDescribe->getDashboard()->getRedirectUri();
            $services['services'][] = $service;
        }
        return json_encode($services);
    }
}
