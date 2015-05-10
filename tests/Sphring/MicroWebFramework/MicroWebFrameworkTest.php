<?php
/**
 * Copyright (C) 2015 Arthur Halet
 *
 * This software is distributed under the terms and conditions of the 'MIT'
 * license which can be found in the file 'LICENSE' in this package distribution
 * or at 'http://opensource.org/licenses/MIT'.
 *
 * Author: Arthur Halet
 * Date: 20/03/2015
 */


namespace Sphring\MicroWebFramework;


use Arthurh\Sphring\Sphring;
use Sphring\MicroWebFramework\FakeController\FakeController;
use Sphring\MicroWebFramework\Mock\MockRunner;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MicroWebFrameworkTest extends \PHPUnit_Framework_TestCase
{
    public function testMain()
    {
        $sphring = new Sphring(__DIR__ . '/../../../sphring/main.yml');
        $sphring->loadContext();
        $microWebFrameWork = $sphring->getBean('microwebframe.main');
        $dispatcher = $microWebFrameWork->getRouter()->getDispatcher();
        $request = Request::createFromGlobals();
        $response = $dispatcher->dispatch($request->getMethod(), "/");
        $this->assertInstanceOf(Response::class, $response);
        $this->assertNotNull($response->getContent());
    }

    public function testArgs()
    {
        $sphring = new Sphring(__DIR__ . '/../../../sphring/main.yml');
        $sphring->loadContext();
        $fakeController = new FakeController();
        $microWebFrameWork = $sphring->getBean('microwebframe.main');
        $route = [
            "route" => "/test/{name}",
            "method" => "GET",
            "controller" => $fakeController
        ];
        $microWebFrameWork->addRoute('test', $route);
        $microWebFrameWork->registerRoute($route);

        $this->assertNotEmpty($fakeController->getHelpers());
        $this->assertEmpty($fakeController->getRequest());
        $this->assertEmpty($fakeController->getResponse());


        $dispatcher = $microWebFrameWork->getRouter()->getDispatcher();
        $response = $dispatcher->dispatch("GET", "/test/jojo");
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals("jojo", $response->getContent());

        $routeExt = $sphring->getBean('microwebframe.platesExtensionRoute');
        $route = $routeExt->getRoute("test", "tutu");
        $this->assertContains("/test/tutu", $route);
    }

    public function testNotFound()
    {
        $sphring = new Sphring(__DIR__ . '/../../../sphring/main.yml');
        $sphring->loadContext();
        $microWebFrameWork = $sphring->getBean('microwebframe.main');
        $dispatcher = $microWebFrameWork->getRouter()->getDispatcher();
        $request = Request::createFromGlobals();
        $response = $dispatcher->dispatch($request->getMethod(), "/404");
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testRunner()
    {
        ob_start();
        MockRunner::getInstance();
        $content = ob_get_contents();
        ob_end_clean();
        $this->assertNotEmpty($content);
    }
}