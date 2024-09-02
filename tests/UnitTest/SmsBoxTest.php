<?php

namespace UnitTest;

use JaanBV\SmsBox\Exception\ApikeyNotValidException;
use JaanBV\SmsBox\SmsBox;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass SmsBox
 */
final class SmsBoxTest extends TestCase
{
    /**
     * @covers SmsBox::sendSms
     * @expectedException JaanBV\SmsBox\Exception\ApikeyNotValidException
     */
    public function testInvalidApiKey()
    {
        $api = new SmsBox('fakeapikey');
        $api->sendSms([SmsBox::SANDBOX_PHONE_NUMBER], 'test message');
        $this->expectException(ApikeyNotValidException::class);
    }
}
