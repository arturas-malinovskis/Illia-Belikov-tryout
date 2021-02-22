<?php

declare(strict_types=1);

namespace App\Validator;

use App\Exception\InvalidTransactionException;
use App\Model\Transaction;

class TransactionValidator implements ModelValidatorInterface
{
    /**
     * @param Transaction $transaction
     * @throws InvalidTransactionException
     */
    public static function validate($transaction): void
    {
        if (!$transaction->isDeposit() && !$transaction->isWithdraw()) {
            throw new InvalidTransactionException(
                sprintf("Transaction type is incorrect (%s)", $transaction->getType()), InvalidTransactionException::TYPE_ERROR);
        }

        if ($transaction->getPayment()->getAmountInMinorUnits() <= 0) {
            throw new InvalidTransactionException(
                sprintf("Transaction amount is incorrect (%s)", $transaction->getPayment()->getAmount()), InvalidTransactionException::AMOUNT_ERROR);
        }
    }
}
