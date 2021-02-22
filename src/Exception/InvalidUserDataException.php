<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;

class InvalidUserDataException extends Exception
{
    public const TYPE_ERROR = 2001;
    public const ID_ERROR = 2002;
}
