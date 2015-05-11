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


namespace Sphring\MicroWebFramework;


use Arthurh\Sphring\Annotations\AnnotationsSphring\MethodInit;
use Arthurh\Sphring\Annotations\AnnotationsSphring\Required;
use League\Plates\Engine;
use League\Route\RouteCollection;
use Sphring\MicroWebFramework\Controller\AbstractController;
use Sphring\MicroWebFramework\Exception\MicroWebFrameException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class MicroWebFramework
{
    private static $validMethod = ['GET', 'PUT', 'DELETE', 'POST', 'PATCH'];
    private $routes;
    /**
     * @var RouteCollection
     */
    private $router;
    /**
     * @var Engine
     */
    private $templateEngine;

    private $helpers = [];

    private $modeDebug = true;

    /**
     * @var array
     */
    private $plateExtensions = [];

    public function __construct()
    {
    }

    /**
     * @MethodInit()
     */
    public function init()
    {
        $this->loadDebug();
        foreach ($this->plateExtensions as $extension) {
            $this->templateEngine->loadExtension($extension);
        }
        $routes = $this->routes;
        foreach ($routes as $route) {
            $this->registerRoute($route);
        }
    }

    public function loadDebug()
    {
        if (empty($this->modeDebug)) {
            ini_set("display_errors", false);
            return;
        }
        $whoops = new Run();
        $whoops->pushHandler(new PrettyPageHandler());
        $whoops->register();
    }

    public function registerRoute(array $route)
    {
        $route['controller']->setHelpers($this->helpers);
        $this->router->addRoute($route['method'], $route['route'], function (Request $req, Response $resp, $args) use ($route) {
            $route['controller']->setArgs($args);
            $route['controller']->setRequest(Request::createFromGlobals());
            $route['controller']->setResponse($resp);

            $content = $route['controller']->action();
            $resp = $route['controller']->getResponse();
            $resp->setContent($content);
            return $resp;
        });
    }

    public function getRouteName($name)
    {
        return $this->routes[$name]["route"];
    }

    public function getRoute($name)
    {
        return $this->routes[$name];
    }

    /**
     * @return mixed
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param AbstractController[] $routes
     * @Required
     */
    public function setRoutes(array $routes)
    {
        foreach ($routes as $routeName => $route) {
            $this->addRoute($routeName, $route);
        }
    }

    public function addRoute($routeName, $route)
    {
        if (empty($routeName)) {
            throw new MicroWebFrameException("A route name is missing.");
        }
        if (empty($route['method']) || !in_array($route['method'], self::$validMethod)) {
            $route['method'] = 'GET';
        }
        if (empty($route['route'])) {
            throw new MicroWebFrameException("Error for route '%s': route is missing.", $routeName);
        }
        if (empty($route['controller']) || !($route['controller'] instanceof AbstractController)) {
            throw new MicroWebFrameException("Error for route '%s': controller must extend '%s'.", $routeName, AbstractController::class);
        }
        $this->routes[$routeName] = $route;
    }

    public function __call($name, $arguments)
    {
        if (substr($name, 0, 3) !== 'get') {
            throw new MicroWebFrameException("Method '%s' doesn't exist.", $name);
        }

    }

    public function deleteRoute($routeName)
    {
        unset($this->routes[$routeName]);
    }


    /**
     * @return Engine
     */
    public function getTemplateEngine()
    {
        return $this->templateEngine;
    }

    /**
     * @param Engine $templateEngine
     * @Required
     */
    public function setTemplateEngine(Engine $templateEngine)
    {
        $this->templateEngine = $templateEngine;
        $this->templateEngine->setFileExtension(null);
    }

    /**
     * @return RouteCollection
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @param RouteCollection $router
     * @Required
     */
    public function setRouter(RouteCollection $router)
    {
        $this->router = $router;
    }

    /**
     * @return \ArrayObject
     */
    public function getHelpers()
    {
        return $this->helpers;
    }

    /**
     * @param \ArrayObject $helpers
     * @Required
     */
    public function setHelpers(\ArrayObject $helpers)
    {
        $this->helpers = $helpers;
    }

    /**
     * @param $routeName
     * @return AbstractController
     */
    public function getController($routeName)
    {
        return $this->routes[$routeName]["controller"];
    }

    /**
     * @return array
     */
    public function getPlateExtensions()
    {
        return $this->plateExtensions;
    }

    /**
     * @param array $plateExtensions
     * @Required
     */
    public function setPlateExtensions($plateExtensions)
    {
        $this->plateExtensions = $plateExtensions;
    }

    /**
     * @return boolean
     */
    public function isModeDebug()
    {
        return !empty($this->modeDebug);
    }

    /**
     * @param boolean $modeDebug
     */
    public function setModeDebug($modeDebug)
    {
        $this->modeDebug = $modeDebug;
    }

}
