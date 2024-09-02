<?php

// add your own credentials in this file
require_once __DIR__ . '/credentials.php';

// required to load (only when not using an autoloader)
require_once __DIR__ . '/../vendor/autoload.php';

use JaanBV\SmsBox\SmsBox;

$api = new SmsBox($apiKey);

// Send regular SMS (to sandbox phone number)
var_dump($api->sendSms([SmsBox::SANDBOX_PHONE_NUMBER], 'Test SMS to Sandbox phonenumber'));

// Send text to speech (to sandbox phone number)
var_dump(
    $api->sendSms(
        [SmsBox::SANDBOX_PHONE_NUMBER],
        'I love developers',
        true,
        null,
        true,
        SmsBox::TEXT_TO_SPEECH_EN
    )
);
