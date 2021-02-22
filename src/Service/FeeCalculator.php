<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\DataRetrievalException;
use App\DataHolder\CurrencyHolder;
use App\DataHolder\CurrencyHolderInterface;
use App\Model\Transaction;

class FeeCalculator implements TransactionCalculatorInterface
{
    public const DEPOSIT_FEE_RATE = 0.0003;

    public const BUSINESS_WITHDRAW_FREE_RATE = 0.005;

    public const PRIVATE_WITHDRAW_FREE_RATE = 0.003;
    public const PRIVATE_WITHDRAW_FREE_AMOUNT_LIMIT = 1000;
    public const PRIVATE_WITHDRAW_FREE_TRANSACTIONS_LIMIT = 3;

    /**
     * @var CurrencyHolder
     */
    private $currencyHolder;

    /**
     * @var array
     */
    private $transactions = [];

    /**
     * FeeCalculator constructor.
     * @param CurrencyHolderInterface|null $currencyHolder
     */
    public function __construct(CurrencyHolderInterface $currencyHolder = null)
    {
        $this->currencyHolder = $currencyHolder ?? new CurrencyHolder();
    }

    /**
     * @inheritDoc
     */
    public function calculate(Transaction $transaction): string
    {
        $userId = $transaction->getUser()->getId();

        if (empty($this->transactions[$userId])) {
            $this->transactions[$userId]['amount'] = 0;
            $this->transactions[$userId]['prev_tx_count'] = 0;
        }

        $prevTransactionsAmount = $this->transactions[$userId]['amount'];
        $amountInBaseCurrency = $this->currencyHolder
            ->exchangeToBase($transaction->getAmount(), $transaction->getCurrencyCode());
        $amountSum = $amountInBaseCurrency + $prevTransactionsAmount;

        switch (true) {
            case $transaction->isDeposit():
                $sumForFee = $transaction->getAmount();
                $feeRate = FeeCalculator::DEPOSIT_FEE_RATE;
                break;
            case $transaction->getUser()->isBusiness():
                $sumForFee = $transaction->getAmount();
                $feeRate = FeeCalculator::BUSINESS_WITHDRAW_FREE_RATE;
                break;
            case $this->transactions[$userId]['prev_tx_count'] > FeeCalculator::PRIVATE_WITHDRAW_FREE_TRANSACTIONS_LIMIT:
                $sumForFee = $amountInBaseCurrency;
                $feeRate = FeeCalculator::PRIVATE_WITHDRAW_FREE_RATE;
                break;
            case $prevTransactionsAmount < FeeCalculator::PRIVATE_WITHDRAW_FREE_AMOUNT_LIMIT:

                if ($amountSum >= FeeCalculator::PRIVATE_WITHDRAW_FREE_AMOUNT_LIMIT) {
                    $sumForFee = $amountSum - FeeCalculator::PRIVATE_WITHDRAW_FREE_AMOUNT_LIMIT;
                    $feeRate = FeeCalculator::PRIVATE_WITHDRAW_FREE_RATE;
                } else {
                    $sumForFee = 0;
                    $feeRate = 0;
                }

                $this->transactions[$userId]['amount'] += $amountInBaseCurrency;
                $this->transactions[$userId]['prev_tx_count'] += 1;
                break;
            case $amountSum >= FeeCalculator::PRIVATE_WITHDRAW_FREE_AMOUNT_LIMIT:
                $sumForFee = $amountInBaseCurrency;
                $feeRate = FeeCalculator::PRIVATE_WITHDRAW_FREE_RATE;

                $this->transactions[$userId]['amount'] += $amountInBaseCurrency;
                $this->transactions[$userId]['prev_tx_count'] += 1;
                break;
            default:
                $sumForFee = 0;
                $feeRate = 0;
                break;
        }

        $fee = $this->currencyHolder->exchangeFromBase(
            $sumForFee * $feeRate,
            $transaction->getCurrencyCode()
        );

        return $this->roundUpAndFormat($fee, $transaction->getCurrencyCode());
    }

    /**
     * @param float $amount
     * @param string $currency
     * @return string
     */
    public function roundUpAndFormat(float $amount, string $currency): string
    {
        $precision = $this->currencyHolder->getPrecision($currency);
        if ($precision === 0) {
            $amount = ceil($amount);
        } else {
            $ceilPrecision = pow(10, $precision);
            $amount = ceil($amount * $ceilPrecision) / $ceilPrecision;
        }

        return number_format($amount, $precision, '.', '');
    }

    public function clear(): void
    {
        $this->transactions = [];
    }
}
