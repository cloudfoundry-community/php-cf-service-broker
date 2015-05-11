<?php
/**
 * Copyright (C) 2015 Arthur Halet
 *
 * This software is distributed under the terms and conditions of the 'MIT'
 * license which can be found in the file 'LICENSE' in this package distribution
 * or at 'http://opensource.org/licenses/MIT'.
 *
 * Author: Arthur Halet
 * Date: 11/05/2015
 */


namespace Sphring\MicroWebFramework\ServiceBroker;


use Sphring\MicroWebFramework\Model\Binding;
use Sphring\MicroWebFramework\Model\ServiceInstance;

class NullServiceBroker extends AbstractServiceBroker
{
    public static $CREDENTIALS = ['test' => 'mycredential'];

    public function provisioning(ServiceInstance $serviceInstance)
    {
        return '{}';
    }

    public function update(ServiceInstance $serviceInstance)
    {

    }

    public function binding(ServiceInstance $serviceInstance, Binding $binding)
    {
        $serviceInstance->setCredentials(NullServiceBroker::$CREDENTIALS);
    }

    public function unbinding(ServiceInstance $serviceInstance, Binding $binding)
    {

    }

    public function deprovisioning(ServiceInstance $serviceInstance)
    {

    }
}