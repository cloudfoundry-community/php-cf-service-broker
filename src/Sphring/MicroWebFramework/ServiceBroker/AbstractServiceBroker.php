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


use Arthurh\Sphring\Annotations\AnnotationsSphring\Required;
use Sphring\MicroWebFramework\Doctrine\DoctrineBoot;
use Sphring\MicroWebFramework\Model\Binding;
use Sphring\MicroWebFramework\Model\Plan;
use Sphring\MicroWebFramework\Model\ServiceDescribe;
use Sphring\MicroWebFramework\Model\ServiceInstance;
use Symfony\Component\HttpFoundation\Response;
use League\Route\Http\Exception\UnprocessableEntityException;

abstract class AbstractServiceBroker
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @var DoctrineBoot
     */
    protected $doctrineBoot;

    public function responseUnprocessableEntity()
    {
        if ($this->response === null) {
            return;
        }
        $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function beforeProvisioning($data, $instanceId)
    {
        $em = $this->doctrineBoot->getEntityManager();
        $repoServiceInstance = $em->getRepository(ServiceInstance::class);
        $serviceInstance = $repoServiceInstance->find($instanceId);
        if ($serviceInstance !== null) {
            $this->checkServiceInstance($serviceInstance, $data);
            return null;
        }
        $repoPlan = $em->getRepository(Plan::class);
        $plan = $repoPlan->find($data['plan_id']);
        $repoServiceDescribe = $em->getRepository(ServiceDescribe::class);
        $serviceDescribe = $repoServiceDescribe->find($data['service_id']);

        $serviceInstance = new ServiceInstance($instanceId, $serviceDescribe,
            $plan, $data['organization_guid'], $data['space_guid']);
        $em->persist($serviceInstance);
        $this->responseCreated();
        return $serviceInstance;
    }

    public function checkServiceInstance(ServiceInstance $serviceInstance, $data)
    {
        if (isset($data['service_id']) && $serviceInstance->getServiceDescribe()->getId() !== $data['service_id']) {
            $this->responseConflict();
            return;
        }
        if (isset($data['plan_id']) && $serviceInstance->getPlan()->getId() !== $data['plan_id']) {
            $this->responseConflict();
            return;
        }
        if (isset($data['organization_guid']) && $serviceInstance->getOrganization() !== $data['organization_guid']) {
            $this->responseConflict();
            return;
        }
        if (isset($data['space_guid']) && $serviceInstance->getSpace() !== $data['space_guid']) {
            $this->responseConflict();
            return;
        }
        $this->responseOk();
    }

    public function responseConflict()
    {
        if ($this->response === null) {
            return;
        }
        $this->response->setStatusCode(Response::HTTP_CONFLICT);
    }

    public function responseOk()
    {
        if ($this->response === null) {
            return;
        }
        $this->response->setStatusCode(Response::HTTP_OK);
    }

    public function responseCreated()
    {
        if ($this->response === null) {
            return;
        }
        $this->response->setStatusCode(Response::HTTP_CREATED);
    }

    public function beforeUpdate($planId, $instanceId)
    {
        $em = $this->doctrineBoot->getEntityManager();
        $repoServiceInstance = $em->getRepository(ServiceInstance::class);
        $serviceInstance = $repoServiceInstance->find($instanceId);

        $repoPlan = $em->getRepository(Plan::class);
        $plan = $repoPlan->find($planId);
        $serviceInstance->setPlan($plan);
        $em->persist($serviceInstance);
        $this->responseOk();
        return $serviceInstance;
    }

    public function beforeBinding($data, $instanceId, $bindingId)
    {
        if (empty($data['bind_resource']) ||
            empty($data['bind_resource']['app_guid'])
        ) {
            throw new UnprocessableEntityException('bind_resource.app_guid is required');
        }

        $em = $this->doctrineBoot->getEntityManager();
        $repoServiceInstance = $em->getRepository(ServiceInstance::class);
        $serviceInstance = $repoServiceInstance->find($instanceId);

        $appGuid = $data['bind_resource']['app_guid'];
        $repoApp = $em->getRepository(Binding::class);
        $app = $repoApp->find($bindingId);
        if ($app === null) {
            $app = new Binding($bindingId, $appGuid);
        }
        if ($app->getServiceInstances()->contains($serviceInstance) && $app->getAppGuid() === $appGuid) {
            $this->responseConflict();
        }
        if ($app->getServiceInstances()->contains($serviceInstance)) {
            $this->checkServiceInstance($serviceInstance, $data);
            return $serviceInstance;
        }

        $app->addServiceInstance($serviceInstance);
        $em->persist($app);
        $this->responseCreated();
        return $serviceInstance;
    }

    public function afterUnbinding($instanceId, $bindingId)
    {
        $em = $this->doctrineBoot->getEntityManager();
        $repoServiceInstance = $em->getRepository(ServiceInstance::class);
        $serviceInstance = $repoServiceInstance->find($instanceId);

        $repoBinding = $em->getRepository(Binding::class);
        $binding = $repoBinding->find($bindingId);
        if ($binding === null) {
            $this->responseGone();
            return null;
        }
        $binding->removeServiceInstance($serviceInstance);
        $em->flush();
        $em->remove($binding);
        $this->responseOk();
        return $serviceInstance;
    }

    public function responseGone()
    {
        if ($this->response === null) {
            return;
        }
        $this->response->setStatusCode(Response::HTTP_GONE);
    }

    public function afterDeprovisioning($instanceId)
    {
        $em = $this->doctrineBoot->getEntityManager();
        $repoServiceInstance = $em->getRepository(ServiceInstance::class);
        $serviceInstance = $repoServiceInstance->find($instanceId);
        if ($serviceInstance === null) {
            $this->responseGone();
            return null;
        }

        $em->remove($serviceInstance);
        $this->responseOk();
        return $serviceInstance;
    }

    abstract public function provisioning(ServiceInstance $serviceInstance);

    abstract public function update(ServiceInstance $serviceInstance);

    abstract public function binding(ServiceInstance $serviceInstance, Binding $binding);

    abstract public function unbinding(ServiceInstance $serviceInstance, Binding $binding);

    abstract public function deprovisioning(ServiceInstance $serviceInstance);

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param Response $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
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
    public function setDoctrineBoot($doctrineBoot)
    {
        $this->doctrineBoot = $doctrineBoot;
    }


}
