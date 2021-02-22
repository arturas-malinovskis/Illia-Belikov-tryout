<?php

declare(strict_types=1);


namespace App\Tests\Service;


use App\DataHolder\CurrencyHolder;
use App\Exception\DataRetrievalException;
use App\Model\Transaction;
use App\Model\User;
use App\Service\FeeCalculator;
use App\Service\TransactionManager;
use App\Service\TransactionManagerInterface;
use Generator;
use PHPUnit\Framework\TestCase;

class TransactionManagerTest extends TestCase
{
    /**
     * @var TransactionManagerInterface
     */
    private $transactionManager;

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

        $calculator = new FeeCalculator($holder);

        $this->transactionManager = new TransactionManager([], $calculator);
    }

    /**
     * @param $txArray
     * @param $expectations
     *
     * @dataProvider proceedProvider
     */
    public function testProceed($txArray, $expectations)
    {
        $this->transactionManager->setTransactionsArray($txArray);

        $this->expectOutputString($expectations);
        $this->transactionManager->processing();
    }

    /**
     * @return Generator
     */
    public function proceedProvider(): Generator
    {
        yield [
            'test 1' => [
                [
                    new Transaction('2014-12-31', new User (4, 'private'), 'withdraw', 1200.00, 'EUR'),
                    new Transaction('2015-01-01', new User (4, 'private'), 'withdraw', 1000.00, 'EUR')
                ]
            ],
            'expectations' => implode(
                    PHP_EOL,
                    ['0.60', '3.00']
                ) . PHP_EOL,
        ];

        yield [
            'test 2' => [
                [
                    new Transaction('2016-01-05', new User (4, 'private'), 'withdraw', 1000.00, 'EUR'),
                    new Transaction('2016-01-05', new User (1, 'private'), 'deposit', 200.00, 'EUR'),
                    new Transaction('2016-01-06', new User (2, 'business'), 'withdraw', 300.00, 'EUR'),
                    new Transaction('2016-01-06', new User (1, 'private'), 'withdraw', 30000, 'JPY'),
                    new Transaction('2016-01-07', new User (1, 'private'), 'withdraw', 1000.00, 'EUR'),
                    new Transaction('2016-01-07', new User (1, 'private'), 'withdraw', 100.00, 'USD'),
                    new Transaction('2016-01-10', new User (1, 'private'), 'withdraw', 100.00, 'EUR'),
                    new Transaction('2016-01-10', new User (2, 'business'), 'deposit', 10000.00, 'EUR'),
                    new Transaction('2016-01-10', new User (3, 'private'), 'withdraw', 1000.00, 'EUR'),
                ]
            ],
            'expectations' => implode(
                    PHP_EOL,
                    ['0.00', '0.06', '1.50', '0', '0.70', '0.30', '0.30', '3.00', '0.00']
                ) . PHP_EOL,

        ];

        yield [
            'test 3' => [
                [
                    new Transaction('2016-02-15', new User (1, 'private'), 'withdraw', 300.00, 'EUR'),
                    new Transaction('2016-02-19', new User (5, 'private'), 'withdraw', 3000000, 'JPY')
                ]
            ],
            'expectations' => implode(
                    PHP_EOL,
                    ['0.00', '8612']
                ) . PHP_EOL,
        ];
    }
}
