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

class Provisioning extends IndexController
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
        $serviceBroker = $this->getServiceBroker($data['service_id']);
        $em = $this->getDoctrineBoot()->getEntityManager();
        $serviceInstance = $serviceBroker->beforeProvisioning($data, $instanceId);
        $em->flush();
        if ($serviceInstance === null) {
            return '{}';
        }
        $returnFromMethod = $serviceBroker->provisioning($serviceInstance);
        if ($returnFromMethod !== null) {
            return $returnFromMethod;
        }
        return '{}';
    }
}
