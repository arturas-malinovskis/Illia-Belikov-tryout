<?php

declare(strict_types=1);

namespace App\Service;

use App\DataHolder\CurrencyHolder;
use App\DataHolder\CurrencyHolderInterface;
use App\Exception\NoCurrencyRateException;
use App\Model\Transaction;
use Evp\Component\Money\Money;

class FeeCalculator implements TransactionCalculatorInterface
{
    public const DEPOSIT_FEE_RATE = 0.0003;

    public const BUSINESS_WITHDRAW_FEE_RATE = 0.005;

    public const PRIVATE_WITHDRAW_FEE_RATE = 0.003;
    public const PRIVATE_WITHDRAW_FREE_AMOUNT_LIMIT = '1000';
    public const PRIVATE_WITHDRAW_FREE_TRANSACTIONS_LIMIT = 3;
    public const ALL_OPERATIONS_FREE_TRANSACTIONS_LIMIT = 5;

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
     * @param Transaction $transaction
     * @return string
     * @throws NoCurrencyRateException
     */
    public function calculate(Transaction $transaction): string
    {
        $userId = $transaction->getUser()->getId();

        if (!isset($this->transactions[$userId])) {
            $this->transactions[$userId]['amount'] = new Money('0', $this->currencyHolder->getBaseCurrency());
            $this->transactions[$userId]['prev_tx_count'] = 0;
            $this->transactions[$userId]['all_tx_count'] = 0;
        }

        /** @var Money $prevTransactionsAmount */
        $prevTransactionsAmount = $this->transactions[$userId]['amount'];
        $amountInBaseCurrency = $this->currencyHolder->exchange(
            $transaction->getPayment(),
            $transaction->getPayment()->getCurrency()
        );
        $amountSum = $amountInBaseCurrency->add($prevTransactionsAmount);

        switch (true) {
            case $this->transactions[$userId]['all_tx_count'] < self::ALL_OPERATIONS_FREE_TRANSACTIONS_LIMIT:
                $sumForFee = new Money('0', $this->currencyHolder->getBaseCurrency());
                $feeRate = 0;
                $this->transactions[$userId]['all_tx_count'] += 1;
                break;
            case $transaction->isDeposit():
                $sumForFee = $transaction->getPayment();
                $feeRate = self::DEPOSIT_FEE_RATE;
                break;
            case $transaction->getUser()->isBusiness():
                $sumForFee = $transaction->getPayment();
                $feeRate = self::BUSINESS_WITHDRAW_FEE_RATE;
                break;
            case $this->transactions[$userId]['prev_tx_count'] > self::PRIVATE_WITHDRAW_FREE_TRANSACTIONS_LIMIT:
                $sumForFee = $amountInBaseCurrency;
                $feeRate = self::PRIVATE_WITHDRAW_FEE_RATE;
                break;
            case $prevTransactionsAmount->isLt(
                new Money(self::PRIVATE_WITHDRAW_FREE_AMOUNT_LIMIT, $this->currencyHolder->getBaseCurrency())
            ):

                if ($amountSum >= self::PRIVATE_WITHDRAW_FREE_AMOUNT_LIMIT) {
                    $sumForFee = $amountSum->sub(
                        new Money(self::PRIVATE_WITHDRAW_FREE_AMOUNT_LIMIT, $this->currencyHolder->getBaseCurrency())
                    );
                    $feeRate = self::PRIVATE_WITHDRAW_FEE_RATE;
                } else {
                    $sumForFee = 0;
                    $feeRate = 0;
                }

                $this->transactions[$userId]['amount'] = $prevTransactionsAmount->add($amountInBaseCurrency);
                $this->transactions[$userId]['prev_tx_count'] += 1;
                break;
            case $amountSum->isGte(
                new Money(self::PRIVATE_WITHDRAW_FREE_AMOUNT_LIMIT, $this->currencyHolder->getBaseCurrency())
            ):
                $sumForFee = $amountInBaseCurrency;
                $feeRate = self::PRIVATE_WITHDRAW_FEE_RATE;

                $this->transactions[$userId]['amount'] = $prevTransactionsAmount->add($amountInBaseCurrency);
                $this->transactions[$userId]['prev_tx_count'] += 1;
                break;
            default:
                $sumForFee = new Money();
                $feeRate = 0;
                break;
        }

        $fee = $this->currencyHolder->exchange(
            $sumForFee->mul($feeRate),
            $transaction->getCurrencyCode(),
            false
        );

        return $this->roundUpAndFormat($fee, $transaction->getCurrencyCode());
    }

    public function roundUpAndFormat(Money $fee, string $currency): string
    {
        if ($fee->isNegative()) {
            return '0';
        }
        $precision = Money::getFraction($currency);
        $fee = $fee->ceil($precision);

        return $fee->getAmount();
    }

    public function clear(): void
    {
        foreach ($this->transactions as $key => $item) {
            $this->transactions[$key]['amount'] = new Money('0', $this->currencyHolder->getBaseCurrency());
            $this->transactions[$key]['prev_tx_count'] = 0;
        }
    }
}
