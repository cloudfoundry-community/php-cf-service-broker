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
/**
 * Copyright 2018. Plesk International GmbH.
 *
 * This software is distributed under the terms and conditions of the 'MIT'
 * license which can be found in the file 'LICENSE' in this package distribution
 * or at 'http://opensource.org/licenses/MIT'.
 */


namespace Sphring\MicroWebFramework\Controller;


use Sphring\MicroWebFramework\Auth\HttpBasicAuthentifier;
use Sphring\MicroWebFramework\Model\ServiceDescribe;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use vierbergenlars\SemVer\SemVerException;
use vierbergenlars\SemVer\version;

class IndexController extends AbstractController
{

    public function action()
    {
        parent::action();

        $version = $this->getRequest()->headers->get('X-Broker-API-Version') . '.0';
        $brokerVersionExpression = $this->getBrokerVersionExpression();
        try {
            $satisfy = $brokerVersionExpression->satisfiedBy(new version($version));
        } catch (SemVerException $e) {
            $this->response->setStatusCode(Response::HTTP_PRECONDITION_FAILED);
            return $this->getErrorJson(
                'UnsupportedProtocolVersion',
                "Protocol version $version is unsupported. Supported protocol versions are: " . $brokerVersionExpression->getString()
            );
        }

        if (empty($version) || !$satisfy) {
            $this->response->setStatusCode(Response::HTTP_PRECONDITION_FAILED);
            return $this->getErrorJson(
                'UnsupportedProtocolVersion',
                "Protocol version $version is unsupported. Supported protocol versions are: " . $brokerVersionExpression->getString()
            );
        }
        $basicAuth = $this->getBasicAuth();
        $basicAuth->setRequest($this->request);
        if (!$basicAuth->auth()) {
            $this->response->setStatusCode(Response::HTTP_UNAUTHORIZED);
            $this->response->headers->add(['WWW-Authenticate' => 'Basic realm="' . HttpBasicAuthentifier::REALM . '"']);
            return $this->getErrorJson(
                Response::$statusTexts[Response::HTTP_UNAUTHORIZED]
            );
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
        $serviceBroker->setResponse($this->response);
        return $serviceBroker;
    }

    /**
     * @param string $error A single word in camel case that uniquely identifies the error condition.
     *  If present, MUST be a non-empty string.
     *  Default: ''
     * @param string $description A user-facing error message explaining why the request failed.
     *  If present, MUST be a non-empty string.
     *  Default: ''
     *
     * @return string JSON
     *
     * @see https://github.com/openservicebrokerapi/servicebroker/blob/v2.14/spec.md#service-broker-errors
     */
    public function getErrorJson($error = '', $description = '')
    {
        $result = [];
        if ($error) {
            $result['error'] = $error;
        }
        if ($description) {
            $result['description'] = $description;
        }
        return json_encode($result, JSON_FORCE_OBJECT);
    }
}
