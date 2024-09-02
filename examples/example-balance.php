<?php

// add your own credentials in this file
require_once __DIR__ . '/credentials.php';

// required to load (only when not using an autoloader)
require_once __DIR__ . '/../vendor/autoload.php';

use JaanBV\SmsBox\SmsBox;

$api = new SmsBox($apiKey);
var_dump($api->balance());
