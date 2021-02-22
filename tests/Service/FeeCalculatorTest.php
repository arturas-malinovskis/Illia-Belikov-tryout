<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Exception\DataRetrievalException;
use App\DataHolder\CurrencyHolder;
use App\Model\Transaction;
use App\Model\User;
use App\Service\FeeCalculator;
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
     * @param $currency
     * @param $expectation
     *
     * @dataProvider roundUpAndFormatProvider
     */
    public function testRoundUpAndFormat($amount, $currency, $expectation)
    {
        $this->assertSame($expectation, $this->calculator->roundUpAndFormat($amount, $currency));
    }

    /**
     * @return Generator
     */
    public function transactionProvider(): Generator
    {
        yield [new Transaction('2014-12-31', new User (4, 'private'), 'withdraw', 1200.00, 'EUR'), 0.60];
        yield [new Transaction('2016-01-05',new User (1,'private'),'deposit',200.00,'EUR'), 0.06];
        yield [new Transaction('2016-01-06',new User (2,'business'),'withdraw',300.00,'EUR'), 1.5];
        yield [new Transaction('2016-01-10',new User (3,'private'),'withdraw',1000.00,'EUR'), 0.00];
        yield [new Transaction('2016-01-10',new User (2,'business'),'deposit',10000.00,'EUR'), 3.00];
        yield [new Transaction('2016-02-19',new User (5,'private'),'withdraw',3000000,'JPY'), 8612];
    }

    /**
     * @return Generator
     */
    public function roundUpAndFormatProvider(): Generator
    {
        yield [0.6, 'EUR', '0.60'];
        yield [3, 'EUR', '3.00'];
        yield [0, 'EUR', '0.00'];
        yield [0.06, 'EUR', '0.06'];
        yield [1.5, 'EUR', '1.50'];
        yield [0, 'JPY', '0'];
        yield [0.6948, 'EUR', '0.70'];
        yield [0.2999, 'USD', '0.30'];
        yield [0.3, 'EUR', '0.30'];
        yield [3, 'EUR', '3.00'];
        yield [0, 'EUR', '0.00'];
        yield [0, 'EUR', '0.00'];
        yield [8611.4099, 'JPY', '8612'];
    }
}
