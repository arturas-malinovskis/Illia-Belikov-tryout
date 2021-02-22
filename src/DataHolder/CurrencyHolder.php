<?php

declare(strict_types=1);

namespace App\DataHolder;

use App\Exception\DataRetrievalException;

class CurrencyHolder implements CurrencyHolderInterface
{
    public const DEFAULT_API_URL = 'https://api.exchangeratesapi.io/latest';
    public const DEFAULT_PRECISION = 2;

    /**
     * @var array
     */
    private $rates;

    /**
     * @var array
     */
    private $precisions;

    /**
     * CurrencyHolder constructor.
     * @param array|null $rates
     * @param array $precisions
     * @throws DataRetrievalException
     */
    public function __construct(array $rates = null, array $precisions = ['JPY' => 0])
    {
        if (empty($rates)) {
            $jsonCurrencyRate = file_get_contents(self::DEFAULT_API_URL);
            $externalData = json_decode($jsonCurrencyRate, true);
            if (null === $externalData) {
                throw new DataRetrievalException('Data retrieval error');
            }
            $rates = $externalData['rates'];
            $rates[$externalData['base']] = 1;
        }

        $this->rates = $rates;
        $this->precisions = $precisions;
    }

    /**
     * @inheritDoc
     */
    public function getRate(string $currencyCode): float
    {
        return $this->rates[$currencyCode] ?? 1;
    }

    /**
     * @inheritDoc
     */
    public function getPrecision(string $currency): int
    {
        return $this->precisions[$currency] ?? self::DEFAULT_PRECISION;
    }

    /**
     * @inheritDoc
     */
    public function exchangeToBase(float $amount, string $currencyCode): float
    {
        $rate = $this->getRate($currencyCode);
        return (float) bcdiv((string) $amount, (string) $rate, 4);
    }

    /**
     * @inheritDoc
     */
    public function exchangeFromBase(float $amount, string $currencyCode): float
    {
        $rate = $this->getRate($currencyCode);
        return (float) bcmul((string) $amount, (string) $rate, 4);
    }
}
