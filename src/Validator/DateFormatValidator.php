<?php

declare(strict_types=1);

namespace App\Validator;

use App\Exception\DateFormatException;

class DateFormatValidator
{
    public static function validate(string $inputDate, $format = 'Y-m-d')
    {
        $dateFromFormat = \DateTime::createFromFormat($format, $inputDate);
        if ($dateFromFormat === false || $dateFromFormat->format($format) !== $inputDate) {
            throw new \InvalidArgumentException(sprintf("Input date is invalid (%s)", $inputDate), DateFormatException::DATE_FORMAT_ERROR);
        }
    }
}
