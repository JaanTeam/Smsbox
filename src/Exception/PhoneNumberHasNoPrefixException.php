<?php

declare(strict_types=1);

namespace JaanBV\SmsBox\Exception;

use Exception;

final class PhoneNumberHasNoPrefixException extends Exception implements SmsBoxException
{

}