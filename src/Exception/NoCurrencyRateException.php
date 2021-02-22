<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;

class NoCurrencyRateException extends Exception
{
    public const WRONG_CURRENCY_CODE_ERROR = 1001;
}
