<?php

declare(strict_types=1);

namespace App\Validator;

use App\Exception\InvalidUserTypeException;
use App\Model\User;

class UserValidator implements ModelValidatorInterface
{
    /**
     * @param User $user
     * @throws InvalidUserTypeException
     */
    public static function validate($user): void
    {
        if (!$user->isBusiness() && !$user->isPrivate()) {
            throw new InvalidUserTypeException(sprintf("User type is incorrect (%s)", $user->getType()), InvalidUserTypeException::EXIT_CODE);
        }
    }
}
