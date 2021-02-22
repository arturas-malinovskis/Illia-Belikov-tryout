<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;

class InvalidTransactionException extends Exception
{
    public const TYPE_ERROR = 3001;
    public const AMOUNT_ERROR = 3002;
}
