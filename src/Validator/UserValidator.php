<?php

declare(strict_types=1);

namespace App\Validator;

use App\Exception\InvalidUserDataException;
use App\Model\User;

class UserValidator implements ModelValidatorInterface
{
    /**
     * @param User $user
     * @throws InvalidUserDataException
     */
    public static function validate($user): void
    {
        if (!$user->isBusiness() && !$user->isPrivate()) {
            throw new InvalidUserDataException(
                sprintf("User type is incorrect (%s)", $user->getType()),
                InvalidUserDataException::TYPE_ERROR
            );
        }

        if ($user->getId() <= 0) {
            throw new InvalidUserDataException(
                sprintf("User ID is incorrect (%s)", $user->getId()),
                InvalidUserDataException::ID_ERROR
            );
        }
    }
}
