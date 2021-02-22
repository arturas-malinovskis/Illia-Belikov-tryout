<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Exception\DataRetrievalException;
use App\DataHolder\CurrencyHolder;
use App\Exception\NoCurrencyRateException;
use App\Model\Transaction;
use App\Model\User;
use App\Service\FeeCalculator;
use Evp\Component\Money\Money;
use Generator;
use PHPUnit\Framework\TestCase;

class FeeCalculatorTest extends TestCase
{
    /**
     * @var FeeCalculator
     */
    private $calculator;

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
        $holder = new CurrencyHolder($testData);

        $this->calculator = new FeeCalculator($holder);
    }

    /**
     * @param $transaction
     * @param $expectation
     * @throws NoCurrencyRateException
     *
     * @dataProvider transactionProvider
     */
    public function testCalculate($transaction, $expectation)
    {
        $fee = $this->calculator->calculate($transaction);
        $this->assertEquals($expectation, $fee);
    }

    /**
     * @param $amount
     * @param $expectation
     *
     * @dataProvider roundUpAndFormatProvider
     */
    public function testRoundUpAndFormat($amount, $expectation)
    {
        $this->assertSame($expectation, $this->calculator->roundUpAndFormat($amount, $amount->getCurrency()));
    }

    /**
     * @return Generator
     */
    public function transactionProvider(): Generator
    {
        yield [new Transaction('2014-12-31', new User (4, 'private'), 'withdraw', new Money(1200.00, 'EUR')), 0.60];
        yield [new Transaction('2016-01-05',new User (1,'private'),'deposit',new Money(200.00,'EUR')), 0.06];
        yield [new Transaction('2016-01-06',new User (2,'business'),'withdraw',new Money(300.00,'EUR')), 1.5];
        yield [new Transaction('2016-01-10',new User (3,'private'),'withdraw',new Money(1000.00,'EUR')), 0.00];
        yield [new Transaction('2016-01-10',new User (2,'business'),'deposit',new Money(10000.00,'EUR')), 3.00];
        yield [new Transaction('2016-02-19',new User (5,'private'),'withdraw',new Money(3000000,'JPY')), 8612];
    }

    /**
     * @return Generator
     */
    public function roundUpAndFormatProvider(): Generator
    {
        yield [new Money(0.6, 'EUR'), '0.60'];
        yield [new Money(3, 'EUR'), '3.00'];
        yield [new Money(0, 'EUR'), '0.00'];
        yield [new Money(0.06, 'EUR'), '0.06'];
        yield [new Money(1.5, 'EUR'), '1.50'];
        yield [new Money(0, 'JPY'), '0'];
        yield [new Money(0.6948, 'EUR'), '0.70'];
        yield [new Money(0.2999, 'USD'), '0.30'];
        yield [new Money(0.3, 'EUR'), '0.30'];
        yield [new Money(3, 'EUR'), '3.00'];
        yield [new Money(0, 'EUR'), '0.00'];
        yield [new Money(0, 'EUR'), '0.00'];
        yield [new Money(8611.4099, 'JPY'), '8612'];
    }
}
