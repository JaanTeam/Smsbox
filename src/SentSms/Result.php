<?php

declare(strict_types=1);

namespace JaanBV\SmsBox\SentSms;

final class Result
{
    public const KEY_FOR_NUMBER = 'number';
    public const KEY_FOR_ID = 'msgid';

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $number;

    private function __construct(
        int $id,
        string $number
    ) {
        $this->id = $id;
        $this->number = $number;
    }

    public static function fromArray(array $array)
    {
        return new self(
            $array[self::KEY_FOR_ID],
            $array[self::KEY_FOR_NUMBER],
        );
    }

    public function id() : int
    {
        return $this->id;
    }

    public function number() : string
    {
        return $this->number;
    }
}