<?php

declare(strict_types=1);

namespace App\DataHolder;

use App\Exception\DataRetrievalException;
use App\Exception\NoCurrencyRateException;
use Evp\Component\Money\Money;

class CurrencyHolder implements CurrencyHolderInterface
{
    public const DEFAULT_API_URL = 'https://api.exchangeratesapi.io/latest';

    /**
     * @var array
     */
    private $rates;

    /**
     * @var string
     */
    private $baseCurrency;

    /**
     * CurrencyHolder constructor.
     * @param array|null $rates
     * @throws DataRetrievalException
     */
    public function __construct(array $rates = null)
    {
        $this->baseCurrency = 'EUR';

        if (empty($rates)) {
            $jsonCurrencyRate = file_get_contents(self::DEFAULT_API_URL);
            $externalData = json_decode($jsonCurrencyRate, true);
            if (null === $externalData) {
                throw new DataRetrievalException('Data retrieval error');
            }
            $rates = $externalData['rates'];
            $rates[$externalData['base']] = 1;
            $this->baseCurrency = $externalData['base'];
        }

        $this->rates = $rates;
    }

    public function getRate(string $currencyCode): ?float
    {
        if (isset($this->rates[$currencyCode])) {
            return $this->rates[$currencyCode];
        }

        throw new NoCurrencyRateException(
            sprintf("No currency rate for %s", $currencyCode),
            NoCurrencyRateException::WRONG_CURRENCY_CODE_ERROR
        );
    }

    /**
     * @return string
     */
    public function getBaseCurrency(): string
    {
        return $this->baseCurrency;
    }

    /**
     * @param Money $money
     * @param string $currencyCode
     * @param bool $inBaseCurrency
     * @return Money
     *
     * @throws NoCurrencyRateException
     */
    public function exchange(Money $money, string $currencyCode, bool $inBaseCurrency = true): Money
    {
        $rate = $this->getRate($currencyCode);
        if ($inBaseCurrency) {
            return $money->div($rate)->setCurrency($this->getBaseCurrency());
        }

        return $money->mul($rate)->setCurrency($currencyCode);
    }
}
