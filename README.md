# SmsBox PHP API

This library connects to the SmsBox API using PHP.

- https://www.jaan.be/smsbox/sms-gateway-api

## Installation

Integrate this private repository in your composer.json

```json
{
    "require": {
        "jaanbv/smsbox-php-api": "^1.*"
    },
    "repositories": [
        {
            "type": "vcs",
            "url":  "https://github.com/jaanteam/smsbox-php-api.git"
        }
    ]
}
```

## Examples

- [Sending SMS](examples/example-sendSms.php) 
- [Checking balance](examples/example-balance.php) 
- [Checking if phone number is valid](examples/example-isValidPhoneNumber.php) 
- [Retrieving phone number data](examples/example-retrievePhoneNumberData.php) 
- [Send sms with one time password](examples/example-sendSmsWithOneTimePassword.php) 
- [Verify one time password code](examples/example-verifyOneTimePasswordCode.php) 

Fill in your API-key in [examples/credentials.php](examples/example-balance.php), then you can execute the examples:

```bash
# Get remaining credits
php examples/example-balance.php

# Validate phone number 
php examples/example-isValidPhoneNumber.php

# Retrieving phone number data
php examples/example-retrievePhoneNumberData.php 

# Send SMS
php examples/example-sendSms.php 

# Send Voice message
php examples/example-sendVoice.php 

# The following examples can be used to send & verify a one-time-password
php examples/example-sendSmsWithOneTimePassword.php 
php examples/example-verifyOneTimePasswordCode.php 
```

## PHPUnit

Executing all tests:

```bash
./vendor/bin/phpunit tests
```
