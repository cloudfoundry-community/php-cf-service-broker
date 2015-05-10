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

namespace Sphring\MicroWebFramework\ServiceBroker;


use Sphring\MicroWebFramework\Model\ServiceInstance;

class DefaultServiceBroker extends AbstractServiceBroker
{

    public function provisioning(ServiceInstance $serviceInstance)
    {
        // TODO: Implement provisioning() method.
    }

    public function update(ServiceInstance $serviceInstance)
    {
        // TODO: Implement update() method.
    }

    public function binding(ServiceInstance $serviceInstance)
    {
        // TODO: Implement binding() method.
    }

    public function unbinding(ServiceInstance $serviceInstance)
    {
        // TODO: Implement unbinding() method.
    }

    public function deprovisioning(ServiceInstance $serviceInstance)
    {
        // TODO: Implement deprovisioning() method.
    }
}
