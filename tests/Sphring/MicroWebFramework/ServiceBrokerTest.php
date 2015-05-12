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


namespace Sphring\MicroWebFramework;


use Arthurh\Sphring\Logger\LoggerSphring;
use Arthurh\Sphring\Sphring;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use League\Route\Dispatcher;
use Sphring\MicroWebFramework\Doctrine\DoctrineBoot;
use Sphring\MicroWebFramework\Model\Binding;
use Sphring\MicroWebFramework\Model\ServiceInstance;
use Sphring\MicroWebFramework\ServiceBroker\NullServiceBroker;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ServiceBrokerTest extends \PHPUnit_Framework_TestCase
{
    const CATALOG_URL = "/v2/catalog";
    const PROVISIONING_URL = "/v2/service_instances/%s";
    const BINDING_URL = "/v2/service_instances/%s/service_bindings/%s";
    const CATALOG_TO_CHECK = "/Resources/Sphring/Receive/catalog.json";
    /**
     * @var Sphring
     */
    private $sphring;
    /**
     * @var Dispatcher
     */
    private $dispatcher;
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var MicroWebFramework
     */
    private $microWebFrameWork;

    private $instanceId = "01-01";
    private $bindingId = "bind-01-01";

    public function testListCatalog()
    {
        $response = $this->dispatcher->dispatch(Request::METHOD_GET, ServiceBrokerTest::CATALOG_URL);

        $this->assertEquals(json_decode(file_get_contents(__DIR__ . ServiceBrokerTest::CATALOG_TO_CHECK)),
            json_decode($response->getContent()));

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testUpdate()
    {
        $data = [
            "plan_id" => "78aa3596-0296-5ed7-b600-42adecddbef9"
        ];
        $controller = $this->microWebFrameWork->getController('update');
        $controller->setInputData(json_encode($data));
        $this->testProvisioning();
        $response = $this->dispatcher->dispatch(Request::METHOD_PATCH, sprintf(ServiceBrokerTest::PROVISIONING_URL, $this->instanceId));
        $repo = $this->entityManager->getRepository(ServiceInstance::class);
        $serviceInstance = $repo->find($this->instanceId);
        $this->assertNotNull($serviceInstance);
        if (!($serviceInstance instanceof ServiceInstance)) {
            $this->assertInstanceOf(ServiceInstance::class, $serviceInstance);
        }
        $this->assertEquals($serviceInstance->getPlan()->getId(), "78aa3596-0296-5ed7-b600-42adecddbef9");
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('{}', $response->getContent());
    }

    public function testProvisioning()
    {
        $data = [
            "service_id" => "c76ed0a4-9a04-5710-90c2-75e955697b08",
            "plan_id" => "97f78da1-48ce-51dc-b550-6de43ee3cc77",
            "organization_guid" => "org-01",
            "space_guid" => "space-01"
        ];
        $controller = $this->microWebFrameWork->getController('provisioning');
        $controller->setInputData(json_encode($data));
        $response = $this->dispatcher->dispatch(Request::METHOD_PUT, sprintf(ServiceBrokerTest::PROVISIONING_URL, $this->instanceId));
        $repo = $this->entityManager->getRepository(ServiceInstance::class);
        $serviceInstance = $repo->find($this->instanceId);
        $this->assertNotNull($serviceInstance);
        if (!($serviceInstance instanceof ServiceInstance)) {
            $this->assertInstanceOf(ServiceInstance::class, $serviceInstance);
        }
        $this->assertEquals($serviceInstance->getPlan()->getId(), "97f78da1-48ce-51dc-b550-6de43ee3cc77");
        $this->assertEquals($serviceInstance->getServiceDescribe()->getId(), "c76ed0a4-9a04-5710-90c2-75e955697b08");
        $this->assertEquals($serviceInstance->getOrganization(), "org-01");
        $this->assertEquals($serviceInstance->getSpace(), "space-01");
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals('{}', $response->getContent());
    }

    public function testUpdateErrored()
    {
        $data = [
            "plan_id" => "78aa3596-0296-5ed7-b600-42adecddbef9"
        ];
        $controller = $this->microWebFrameWork->getController('update');
        $controller->setInputData(json_encode($data));
        $this->testProvisioning();
        $response = $this->dispatcher->dispatch(Request::METHOD_PATCH, sprintf(ServiceBrokerTest::PROVISIONING_URL, '01-02'));
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
    }

    public function testUnbindingSecondTime()
    {
        $this->testUnbinding();
        $response = $this->dispatcher->dispatch(Request::METHOD_DELETE,
            sprintf(ServiceBrokerTest::BINDING_URL, $this->instanceId, $this->bindingId));
        $this->assertEquals(Response::HTTP_GONE, $response->getStatusCode());
        $this->assertEquals('{}', $response->getContent());
    }

    public function testUnbinding()
    {
        $_GET['service_id'] = "c76ed0a4-9a04-5710-90c2-75e955697b08";
        $this->testBinding();
        $response = $this->dispatcher->dispatch(Request::METHOD_DELETE,
            sprintf(ServiceBrokerTest::BINDING_URL, $this->instanceId, $this->bindingId));
        $repo = $this->entityManager->getRepository(Binding::class);
        $binding = $repo->find($this->bindingId);
        $this->assertNull($binding);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('{}', $response->getContent());
    }

    public function testBinding()
    {
        $this->testProvisioning();
        $data = [
            "plan_id" => "97f78da1-48ce-51dc-b550-6de43ee3cc77",
            "service_id" => "c76ed0a4-9a04-5710-90c2-75e955697b08",
            "app_guid" => "app-01"
        ];
        $controller = $this->microWebFrameWork->getController('binding');
        $controller->setInputData(json_encode($data));
        $response = $this->dispatcher->dispatch(Request::METHOD_PUT,
            sprintf(ServiceBrokerTest::BINDING_URL, $this->instanceId, $this->bindingId));
        $repo = $this->entityManager->getRepository(Binding::class);
        $binding = $repo->find($this->bindingId);
        $this->assertNotNull($binding);
        if (!($binding instanceof Binding)) {
            $this->assertInstanceOf(Binding::class, $binding);
        }
        $serviceIntances = $binding->getServiceInstances();
        $this->assertEquals($binding->getAppGuid(), "app-01");
        $this->assertEquals($serviceIntances[0]->getId(), $this->instanceId);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $credentials = ['credentials' => NullServiceBroker::$CREDENTIALS];
        $this->assertEquals($credentials, json_decode($response->getContent(), true));
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testBindingSecondTime()
    {
        $this->testBinding();
        $data = [
            "plan_id" => "97f78da1-48ce-51dc-b550-6de43ee3cc77",
            "service_id" => "c76ed0a4-9a04-5710-90c2-75e955697b08",
            "app_guid" => "app-01"
        ];
        $controller = $this->microWebFrameWork->getController('binding');
        $controller->setInputData(json_encode($data));
        $response = $this->dispatcher->dispatch(Request::METHOD_PUT,
            sprintf(ServiceBrokerTest::BINDING_URL, $this->instanceId, $this->bindingId));
        $credentials = ['credentials' => NullServiceBroker::$CREDENTIALS];
        $this->assertEquals($credentials, json_decode($response->getContent(), true));
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testBindingSecondTimeErrored()
    {
        $this->testBinding();
        $data = [
            "plan_id" => "97f78da1-48ce-51dc-b550-6de43ee3cc77",
            "service_id" => "c76ed0a4-9a04-5710-90c2-75e955697",
            "app_guid" => "app-01"
        ];
        $controller = $this->microWebFrameWork->getController('binding');
        $controller->setInputData(json_encode($data));
        $response = $this->dispatcher->dispatch(Request::METHOD_PUT,
            sprintf(ServiceBrokerTest::BINDING_URL, $this->instanceId, $this->bindingId));
        $this->assertEquals('{}', $response->getContent());
        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
    }

    public function testProvisioningSecondTime()
    {
        $this->testProvisioning();
        $data = [
            "service_id" => "c76ed0a4-9a04-5710-90c2-75e955697b08",
            "plan_id" => "97f78da1-48ce-51dc-b550-6de43ee3cc77",
            "organization_guid" => "org-01",
            "space_guid" => "space-01"
        ];
        $controller = $this->microWebFrameWork->getController('provisioning');
        $controller->setInputData(json_encode($data));
        $response = $this->dispatcher->dispatch(Request::METHOD_PUT, sprintf(ServiceBrokerTest::PROVISIONING_URL, $this->instanceId));
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('{}', $response->getContent());
    }

    public function testProvisioningSecondErrored()
    {
        $this->testProvisioning();
        $data = [
            "service_id" => "c76ed0a4-9a04-5710-90c2-75e955697b08",
            "plan_id" => "97f78da1-48ce-51dc-b550-6de43ee3cc77",
            "organization_guid" => "org-02",
            "space_guid" => "space-01"
        ];
        $controller = $this->microWebFrameWork->getController('provisioning');
        $controller->setInputData(json_encode($data));
        $response = $this->dispatcher->dispatch(Request::METHOD_PUT, sprintf(ServiceBrokerTest::PROVISIONING_URL, $this->instanceId));
        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
        $this->assertEquals('{}', $response->getContent());
    }

    public function testDeprovisioningSecondTime()
    {
        $this->testDeprovisioning();
        $response = $this->dispatcher->dispatch(Request::METHOD_DELETE, sprintf(ServiceBrokerTest::PROVISIONING_URL, $this->instanceId));
        $this->assertEquals(Response::HTTP_GONE, $response->getStatusCode());
        $this->assertEquals('{}', $response->getContent());
    }

    public function testDeprovisioning()
    {
        $_GET['service_id'] = "c76ed0a4-9a04-5710-90c2-75e955697b08";

        $this->testProvisioning();
        $response = $this->dispatcher->dispatch(Request::METHOD_DELETE, sprintf(ServiceBrokerTest::PROVISIONING_URL, $this->instanceId));
        $repo = $this->entityManager->getRepository(ServiceInstance::class);
        $serviceInstance = $repo->find($this->instanceId);
        $this->assertNull($serviceInstance);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('{}', $response->getContent());
    }

    protected function setUp()
    {
        file_put_contents(__DIR__ . '/Resources/Sphring/db.ct', 0);
        $sphring = new Sphring(__DIR__ . '/Resources/Sphring/main.yml');
        $sphring->setRootProject(__DIR__ . '/../../..');
        $sphring->setComposerLockFile(__DIR__ . '/../../../composer.lock');

        $sphring->loadContext();
        $this->sphring = $sphring;
        $_SERVER['HTTP_X_Broker_API_Version'] = '2.4';
        $_SERVER['PHP_AUTH_USER'] = 'user';
        $_SERVER['PHP_AUTH_PW'] = 'changePassw0rd';
        $this->microWebFrameWork = $sphring->getBean('microwebframe.main');
        $this->dispatcher = $this->microWebFrameWork->getRouter()->getDispatcher();
        $doctrineBoot = $this->sphring->getBean('microwebframe.doctrine');
        $this->entityManager = $doctrineBoot->getEntityManager();
    }

    protected function tearDown()
    {

        $tool = new SchemaTool($this->entityManager);
        $tool->dropDatabase();

    }
}
