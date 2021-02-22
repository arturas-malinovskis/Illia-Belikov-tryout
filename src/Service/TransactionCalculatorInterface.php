<?php

namespace App\Service;

use App\Model\Transaction;

interface TransactionCalculatorInterface
{
    /**
     * Calculates the transaction fee and returns it as a string
     * @param Transaction $transaction
     * @return mixed
     */
    public function calculate(Transaction $transaction);
}
