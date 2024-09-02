<?php

declare(strict_types=1);

namespace JaanBV\SmsBox\Exception;

use Exception;

final class OnlyOneNumberAllowedException extends Exception implements SmsBoxException
{

}
