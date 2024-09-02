<?php

declare(strict_types=1);

namespace JaanBV\SmsBox\Exception;

use Exception;

final class NotEnoughCreditsException extends Exception implements SmsBoxException
{

}