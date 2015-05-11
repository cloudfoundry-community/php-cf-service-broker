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
use Sphring\MicroWebFramework\Model\ServiceInstance;
use Symfony\Component\HttpFoundation\Response;

class Update extends IndexController
{
    public function action()
    {
        $action = parent::action();
        if ($action !== null) {
            return $action;
        }
        $putData = $this->getInputData();
        $data = json_decode($putData, true);
        $args = $this->getArgs();
        $instanceId = $args['instance_id'];
        $em = $this->getDoctrineBoot()->getEntityManager();
        $repoServiceInstance = $em->getRepository(ServiceInstance::class);
        $serviceInstance = $repoServiceInstance->find($instanceId);
        if ($serviceInstance === null) {
            $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $error = ["description" => "Service not found."];
            return json_encode($error);
        }
        $serviceBroker = $this->getServiceBroker($serviceInstance->getServiceDescribe()->getId());

        $serviceInstance = $serviceBroker->beforeUpdate($data['plan_id'], $instanceId);
        $em->flush();
        if ($serviceInstance === null) {
            return '{}';
        }
        $serviceBroker->update($serviceInstance);
        return '{}';
    }
}
