<?php
/**
 * Copyright (C) 2015 Arthur Halet
 *
 * This software is distributed under the terms and conditions of the 'MIT'
 * license which can be found in the file 'LICENSE' in this package distribution
 * or at 'http://opensource.org/licenses/MIT'.
 *
 * Author: Arthur Halet
 * Date: 19/03/2015
 */


namespace Sphring\MicroWebFramework\Controller;


use Sphring\MicroWebFramework\Auth\HttpBasicAuthentifier;
use Sphring\MicroWebFramework\Model\ServiceDescribe;
use Symfony\Component\HttpFoundation\Response;
use vierbergenlars\SemVer\version;

class IndexController extends AbstractController
{

    public function action()
    {
        $version = $this->getRequest()->headers->get('X-Broker-API-Version') . '.0';
        if (empty($version) || !$this->getBrokerVersionExpression()->satisfiedBy(new version($version))) {
            $this->response->setStatusCode(Response::HTTP_PRECONDITION_FAILED);
            return Response::$statusTexts[Response::HTTP_PRECONDITION_FAILED];
        }
        $basicAuth = $this->getBasicAuth();
        if (!$basicAuth->auth()) {
            $this->response->setStatusCode(Response::HTTP_UNAUTHORIZED);
            $this->response->headers->add(['WWW-Authenticate' => 'Basic realm="' . HttpBasicAuthentifier::REALM . '"']);
            return Response::$statusTexts[Response::HTTP_UNAUTHORIZED];
        }

        return null;
    }

    public function getServiceBroker($serviceId)
    {
        $em = $this->getDoctrineBoot()->getEntityManager();
        $repo = $em->getRepository(ServiceDescribe::class);
        $serviceDescribe = $repo->find($serviceId);
        $serviceBrokers = $this->getServiceBrokers();
        $serviceBroker = $serviceBrokers['default'];
        if ($serviceDescribe !== null && !empty($serviceBrokers[$serviceDescribe->getName()])) {
            $serviceBroker = $serviceBrokers[$serviceDescribe->getName()];
        }
        return $serviceBroker;
    }
}
