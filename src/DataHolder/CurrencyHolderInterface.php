<?php

namespace App\DataHolder;

use Evp\Component\Money\Money;

interface CurrencyHolderInterface
{
    /**
     * Returns the exchange rate by currency code
     *
     * @param string $currencyCode
     * @return float
     */
    public function getRate(string $currencyCode): ?float;

    /**
     * Returns the equivalent of the amount in base currency
     *
     * @param Money $money
     * @param string $currencyCode
     * @param bool $inBaseCurrency
     * @return Money
     */
    public function exchange(Money $money, string $currencyCode, bool $inBaseCurrency = true): Money;
}
