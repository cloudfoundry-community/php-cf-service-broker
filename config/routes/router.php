<?php
// Copyright 1999-2018. Plesk International GmbH. All rights reserved.

if (preg_match('/\.[^\.\s]+$/', $_SERVER["REQUEST_URI"])) {
    return false;
} else {
    require __DIR__ . '/../../app/app.php';
}
