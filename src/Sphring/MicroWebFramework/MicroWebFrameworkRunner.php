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


use Arthurh\Sphring\Annotations\AnnotationsSphring\AfterLoad;
use Arthurh\Sphring\Annotations\AnnotationsSphring\RootProject;
use Arthurh\Sphring\Runner\SphringRunner;
use League\Route\Http\Exception\HttpExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MicroWebFrameworkRunner
 * @package Sphring\MicroWebFramework
 * @RootProject(file="../../../")
 */
class MicroWebFrameworkRunner extends SphringRunner
{
    /**
     * @AfterLoad
     */
    public function run()
    {
        $microWebFrameWork = $this->getBean('microwebframe.main');
        $dispatcher = $microWebFrameWork->getRouter()->getDispatcher();
        $request = Request::createFromGlobals();
        try {
            /** @var Response $response */
            $response = $dispatcher->dispatch($request->getMethod(), $request->getPathInfo());

        } catch (\Exception $e) {

            if (is_subclass_of($e, HttpExceptionInterface::class)) {
                $statusCode = $e->getStatusCode();
                $errorSummary = preg_replace(
                    '/\s*/',
                    '',
                    Response::$statusTexts[ $statusCode ]
                );
                $headers = $e->getHeaders();

            } else {
                $statusCode = 500;
                $errorSummary = 'InternalServerError';
                $headers = [];
            }

            $body = [
                'error' => $errorSummary,
                'description' => $e->getMessage()
            ];
            $response = new JsonResponse(
                $body,
                $statusCode,
                $headers
            );
            $response->setEncodingOptions(
                $response->getEncodingOptions() | JSON_FORCE_OBJECT
            );
        }

        $response->send();
    }
}
