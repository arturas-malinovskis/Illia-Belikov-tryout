<?php

namespace App\DataHolder;

interface CurrencyHolderInterface
{
    /**
     * Returns the exchange rate by currency code
     *
     * @param string $currencyCode
     * @return float
     */
    public function getRate(string $currencyCode): float;

    /**
     * Returns the precision of rounding by currency code
     *
     * @param string $currency
     * @return int
     */
    public function getPrecision(string $currency): int;

    /**
     * Returns the equivalent of the amount in base currency
     *
     * @param float $amount
     * @param string $currencyCode
     * @return float
     */
    public function exchangeToBase(float $amount, string $currencyCode): float;

    /**
     * Returns the equivalent of the amount in the target (not base) currency.
     *
     * @param float $amount
     * @param string $currencyCode
     * @return float
     */
    public function exchangeFromBase(float $amount, string $currencyCode): float;
}
