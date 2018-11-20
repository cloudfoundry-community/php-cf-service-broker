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

namespace Sphring\MicroWebFramework\ServiceBroker;


use Arthurh\Sphring\Logger\LoggerSphring;
use Sphring\MicroWebFramework\Model\Binding;
use Sphring\MicroWebFramework\Model\ServiceInstance;

/**
 * Class DefaultServiceBroker
 * @package Sphring\MicroWebFramework\ServiceBroker
 */
class DefaultServiceBroker extends AbstractServiceBroker
{
    /**
     * Provisioning your service
     * To return an error, return a string in json format following this:
     * {
     *   "description": "description of your error"
     * }
     * You can also return a dashbord url in json format:
     * {
     *   "dashboard_url": "http://url_of_your_dashboard
     * }
     * Note: no need to add serviceInstance in the database we do it already
     * @param ServiceInstance $serviceInstance
     */
    public function provisioning(ServiceInstance $serviceInstance)
    {
        LoggerSphring::getInstance()->info(__METHOD__ . ': serviceInstance: ' . $serviceInstance);

        // TODO: Implement provisioning() method.

        LoggerSphring::getInstance()->info(__METHOD__ . ': end');
    }

    /**
     * Update your service plan (cloud foundry's doc says that you can just update plan)
     * To return an error, return a string in json format following this:
     * {
     *   "description": "description of your error"
     * }
     * Note: no need to update serviceInstance in the database we do it already
     * @param ServiceInstance $serviceInstance
     */
    public function update(ServiceInstance $serviceInstance)
    {
        LoggerSphring::getInstance()->info(__METHOD__ . ': ' . $serviceInstance);

        // TODO: Implement update() method.

        LoggerSphring::getInstance()->info(__METHOD__ . ': end');
    }

    /**
     * Bind your service to an app and update credentials in ServiceInstance with method setCredentials(array)
     * To return an error, return a string in json format following this:
     * {
     *   "description": "description of your error"
     * }
     * Note: no need to add binding from in database we do it already
     * @param ServiceInstance $serviceInstance
     * @param Binding $binding
     */
    public function binding(ServiceInstance $serviceInstance, Binding $binding)
    {
        LoggerSphring::getInstance()->info(
            __METHOD__ . ':' .
                ' serviceInstance: ' . $serviceInstance .
                ' binding: ' . $binding
        );

        // TODO: Implement binding() method.

        LoggerSphring::getInstance()->info(__METHOD__ . ': end');
    }

    /**
     * Unbind a service to an app
     * To return an error, return a string in json format following this:
     * {
     *   "description": "description of your error"
     * }
     * Note: no need to remove binding and update serviceInstance in the database we do it already
     * @param ServiceInstance $serviceInstance
     * @param Binding $binding
     */
    public function unbinding(ServiceInstance $serviceInstance, Binding $binding)
    {
        LoggerSphring::getInstance()->info(
            __METHOD__ . ':' .
                ' serviceInstance: ' . $serviceInstance .
                ' binding: ' . $binding
        );

        // TODO: Implement unbinding() method.

        LoggerSphring::getInstance()->info(__METHOD__ . ': end');
    }

    /**
     * Deprovision your service
     * Note: no need to remove serviceInstance in the database we do it already
     * @param ServiceInstance $serviceInstance
     */
    public function deprovisioning(ServiceInstance $serviceInstance)
    {
        LoggerSphring::getInstance()->info(__METHOD__ . ': ' . $serviceInstance);

        // TODO: Implement deprovisioning() method.

        LoggerSphring::getInstance()->info(__METHOD__ . ': end');
    }
}
