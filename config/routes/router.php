<?php
/**
 * Copyright 2018. Plesk International GmbH.
 *
 * This software is distributed under the terms and conditions of the 'MIT'
 * license which can be found in the file 'LICENSE' in this package distribution
 * or at 'http://opensource.org/licenses/MIT'.
 */

if (preg_match('/\.[^\.\s]+$/', $_SERVER["REQUEST_URI"])) {
    return false;
} else {
    require __DIR__ . '/../../app/app.php';
}
