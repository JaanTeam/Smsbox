<?php

// add your own credentials in this file
require_once __DIR__ . '/credentials.php';

// required to load (only when not using an autoloader)
require_once __DIR__ . '/../vendor/autoload.php';

use JaanBV\SmsBox\SmsBox;

$api = new SmsBox($voiceApiKey);
$api->enableDebugging();

// Send voice message (to sandbox phone number)
var_dump($api->sendVoice(SmsBox::SANDBOX_PHONE_NUMBER, 'Test SMS to Sandbox phonenumber'));
