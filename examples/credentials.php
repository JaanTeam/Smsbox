<?php

// define your own credentials
$apiKey = ''; // required

// throw error
if (empty($apiKey)) {
    echo 'Please define your login credentials in ' . __DIR__ . '/credentials.php';
}
