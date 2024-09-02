<?php

namespace UnitTest\SentSms;

use JaanBV\SmsBox\SentSms\Result;
use JaanBV\SmsBox\SentSms\Results;
use JaanBV\SmsBox\SmsBox;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Results
 */
final class ResultsTest extends TestCase
{
    /**
     * @covers Results::__construct()
     */
    public function testValidNumbers()
    {
        $resultId1 = 123456;
        $resultId2 = 123457;
        $number1 = SmsBox::SANDBOX_PHONE_NUMBER;
        $number2 = '32497123123';

        $results = new Results([
            [
                Result::KEY_FOR_ID     => $resultId1,
                Result::KEY_FOR_NUMBER => $number1,
            ],
            [
                Result::KEY_FOR_ID     => $resultId2,
                Result::KEY_FOR_NUMBER => $number2,
            ]
        ]);
        $this->assertEquals(2, $results->count());

        $this->assertEquals($resultId1, $results->getArrayCopy()[0]->id());
        $this->assertEquals($number1, $results->getArrayCopy()[0]->number());

        $this->assertEquals($resultId2, $results->getArrayCopy()[1]->id());
        $this->assertEquals($number2, $results->getArrayCopy()[1]->number());
    }
}
