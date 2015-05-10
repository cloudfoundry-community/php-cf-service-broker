<?php
/**
 * Copyright (C) 2014 Arthur Halet
 *
 * This software is distributed under the terms and conditions of the 'MIT'
 * license which can be found in the file 'LICENSE' in this package distribution
 * or at 'http://opensource.org/licenses/MIT'.
 *
 * Author: Arthur Halet
 * Date: 21/03/2015
 */

namespace Sphring\MicroWebFramework\FakeController;


use Sphring\MicroWebFramework\Controller\AbstractController;

class FakeController extends AbstractController
{

    public function action()
    {
        $args = $this->getArgs();
        return $args['name'];
    }
}
