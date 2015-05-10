<?php
/**
 * Copyright (C) 2014 Arthur Halet
 *
 * This software is distributed under the terms and conditions of the 'MIT'
 * license which can be found in the file 'LICENSE' in this package distribution
 * or at 'http://opensource.org/licenses/MIT'.
 *
 * Author: Arthur Halet
 * Date: 22/03/2015
 */
namespace Sphring\MicroWebFramework\Mock;

use Sphring\MicroWebFramework\MicroWebFrameworkRunner;

/**
 * @RootProject(../../../../)
 */
class MockRunner extends MicroWebFrameworkRunner
{
    /**
     * @AfterLoad
     */
    public function run()
    {
        parent::run();
    }

}
