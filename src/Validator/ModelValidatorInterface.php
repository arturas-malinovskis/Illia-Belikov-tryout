<?php

declare(strict_types=1);

namespace App\Validator;

interface ModelValidatorInterface
{
    public static function validate($model): void;
}
