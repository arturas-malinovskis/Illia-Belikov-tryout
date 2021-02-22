<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;

class DateFormatException extends Exception
{
    public const DATE_FORMAT_ERROR = 4001;
}
