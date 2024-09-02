<?php

declare(strict_types=1);

namespace JaanBV\SmsBox\SentSms;

use ArrayObject;

final class Results extends ArrayObject
{
    public function __construct(array $results)
    {
        parent::__construct();

        foreach ($results as $result) {
            $this->append(Result::fromArray($result));
        }
    }
}