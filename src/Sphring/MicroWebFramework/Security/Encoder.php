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

namespace Sphring\MicroWebFramework\Security;


use Arthurh\Sphring\Annotations\AnnotationsSphring\Required;

class Encoder
{
    /**
     * @var string[]
     */
    private $encoders;
    private $salt = "";

    public function __construct()
    {
    }

    /**
     * @Required
     * @param $encoders
     */
    public function setEncoders($encoders)
    {
        if (!is_array($encoders)) {
            $this->encoders = explode('|', $encoders);
        } else {
            $this->encoders = $encoders;
        }
    }

    public function crypt($text)
    {
        if (empty($this->encoders) || in_array('plaintext', $this->encoders)) {
            return $text;
        }
        $encoderAvailable = hash_algos();
        foreach ($this->encoders as $encoder) {
            if (!in_array($encoder, $encoderAvailable)) {
                continue;
            }
            $text = hash($encoder, $text . $this->salt);
        }

        return $text;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @Required
     * @param $salt
     * @return $this
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
        return $this;
    }

    /**
     * @return \string[]
     */
    public function getEncoders()
    {
        return $this->encoders;
    }

}
