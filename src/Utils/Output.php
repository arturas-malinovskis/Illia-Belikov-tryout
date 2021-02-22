<?php

declare(strict_types=1);

namespace App\Utils;

use Throwable;

class Output
{
    public static function writeException(Throwable $exception)
    {
        self::write($exception->getMessage());
        exit((string)$exception->getCode());
    }

    public static function write($data)
    {
        if (is_countable($data)) {
            foreach ($data as $item) {
                self::writeSingleString($item);
            }
        } else {
            self::writeSingleString($data);
        }
    }

    public static function writeSingleString($data)
    {
        print_r($data . PHP_EOL);
    }


}
