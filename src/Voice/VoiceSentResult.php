<?php

declare(strict_types=1);

namespace JaanBV\SmsBox\Voice;

use DateTime;

final class VoiceSentResult
{
    public const KEY_FOR_CODE = 'code';
    public const KEY_FOR_MESSAGE = 'message';
    public const KEY_FOR_NUMBER = 'number';
    public const KEY_FOR_DATETIME = 'datetime';

    private function __construct(
        public readonly int $code,
        public readonly string $message,
        public readonly string $number,
        public readonly DateTime $datetime
    ) {
    }

    public static function fromArray(array $array)
    {
        return new self(
            $array[self::KEY_FOR_CODE],
            $array[self::KEY_FOR_MESSAGE],
            $array[self::KEY_FOR_NUMBER],
            new DateTime($array[self::KEY_FOR_DATETIME]),
        );
    }
}
