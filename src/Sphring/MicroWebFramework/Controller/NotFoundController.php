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


namespace Sphring\MicroWebFramework\Controller;


use Symfony\Component\HttpFoundation\Response;

class NotFoundController extends AbstractController
{

    public function action()
    {
        $this->response->setStatusCode(Response::HTTP_NOT_FOUND);
        return $this->getEngine()->render('errors/404.php');
    }
}
