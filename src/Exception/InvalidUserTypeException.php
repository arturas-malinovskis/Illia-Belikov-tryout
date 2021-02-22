<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;

class InvalidUserTypeException extends Exception
{
    public const EXIT_CODE = 2001;
}
