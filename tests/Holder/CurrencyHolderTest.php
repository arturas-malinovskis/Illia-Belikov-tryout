<?php

declare(strict_types=1);

namespace App\Tests\Holder;

use App\Exception\DataRetrievalException;
use App\DataHolder\CurrencyHolder;
use App\DataHolder\CurrencyHolderInterface;
use Generator;
use PHPUnit\Framework\TestCase;

class CurrencyHolderTest extends TestCase
{
    /**
     * @var CurrencyHolderInterface
     */
    private $holder;

    /**
     * @throws DataRetrievalException
     */
    public function setUp()
    {
        $testData = [
            'EUR' => 1,
            'USD' => 1.1497,
            'JPY' => 129.53
        ];
        $this->holder = new CurrencyHolder($testData);
    }

    /**
     * @param $currency
     * @param $rate
     *
     * @dataProvider currencyRateProvider
     */
    public function testGetRate($currency, $rate)
    {
        $this->assertEquals($rate, $this->holder->getRate($currency));
    }

    /**
     * @param $currency
     * @param $precision
     *
     * @dataProvider getPrecisionProvider
     */
    public function testGetPrecision($currency, $precision)
    {
        $this->assertEquals($precision, $this->holder->getPrecision($currency));
    }

    /**
     * @param $amount
     * @param $currency
     * @param $expectation
     *
     * @dataProvider exchangeToBaseProvider
     */
    public function testExchangeToBase($currency, $amount, $expectation)
    {
        $this->assertEquals($expectation, $this->holder->exchangeToBase($amount, $currency));
    }

    /**
     * @param $amount
     * @param $currency
     * @param $expectation
     *
     * @dataProvider exchangeFromBaseProvider
     */
    public function testExchangeFromBase($currency, $amount, $expectation)
    {
        $this->assertEquals($expectation, $this->holder->exchangeFromBase($amount, $currency));
    }

    /**
     * @return Generator
     */
    public function currencyRateProvider(): Generator
    {
        yield ['EUR', 1];
        yield ['USD', 1.1497];
        yield ['JPY', 129.53];
    }

    /**
     * @return Generator
     */
    public function getPrecisionProvider(): Generator
    {
        yield ['EUR', 2];
        yield ['USD', 2];
        yield ['JPY', 0];
    }

    /**
     * @return Generator
     */
    public function exchangeToBaseProvider(): Generator
    {
        yield ['EUR', 1, 1];
        yield ['USD', 1.1497, 1];
        yield ['JPY', 129.53, 1];
    }

    /**
     * @return Generator
     */
    public function exchangeFromBaseProvider(): Generator
    {
        yield ['EUR', 1, 1];
        yield ['USD', 1, 1.1497];
        yield ['JPY', 1, 129.53];
    }
}
