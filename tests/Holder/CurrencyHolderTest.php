<?php

declare(strict_types=1);

namespace App\Tests\Holder;

use App\Exception\DataRetrievalException;
use App\DataHolder\CurrencyHolder;
use App\DataHolder\CurrencyHolderInterface;
use App\Exception\NoCurrencyRateException;
use Evp\Component\Money\Money;
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
     * @param Money $amount
     * @param Money $expectation
     * @param bool $inBaseCurrency
     * @throws NoCurrencyRateException
     *
     * @dataProvider exchangeProvider
     */
    public function testExchange(Money $amount, Money $expectation, bool $inBaseCurrency)
    {
        $this->assertEquals(
            $expectation->getAmountInCents(),
            $this->holder->exchange($amount, $amount->getCurrency(), $inBaseCurrency)->getAmountInCents()
        );
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
    public function exchangeProvider(): Generator
    {
        yield [new Money('1', 'EUR'), new Money('1', 'EUR'), true];
        yield [new Money('1.1497', 'USD'), new Money('1', 'EUR'), true];
        yield [new Money('129.53', 'JPY'), new Money('1', 'EUR'), true];

        yield [new Money('1', 'EUR'), new Money('1', 'EUR'), false];
        yield [new Money('1', 'USD'), new Money('1.1497', 'EUR'), false];
        yield [new Money('1', 'JPY'), new Money('129.53', 'EUR'), false];
    }
}
