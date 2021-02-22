<?php

declare(strict_types=1);

namespace App\Tests\Model;

use App\Model\Transaction;
use App\Model\User;
use DateTimeInterface;
use Evp\Component\Money\Money;
use Generator;
use PHPUnit\Framework\TestCase;
use StdClass;

class TransactionTest extends TestCase
{
    /**
     * @param $date
     * @param $user
     * @param $type
     * @param $payment
     *
     * @dataProvider correctTransactionProvider
     */
    public function testCorrectTransaction($date, $user, $type, $payment)
    {
        $transaction = new Transaction($date, $user, $type, $payment);

        $this->assertTrue($transaction->getDate() instanceof DateTimeInterface);

        $this->assertSame($date, $transaction->getDate()->format('Y-m-d'));
        $this->assertSame($user, $transaction->getUser());
        $this->assertSame($type, $transaction->getType());
        $this->assertSame($payment, $transaction->getPayment());
    }

    /**
     * @param $date
     * @param $user
     * @param $type
     * @param $payment
     *
     * @dataProvider incorrectTransactionProvider
     */
    public function testIncorrectTransaction($date, $user, $type, $payment)
    {
        $this->expectException('TypeError');
        $transaction = new Transaction($date, $user, $type, $payment);
    }

    public function testTransactionType()
    {
        $user = new User(1, User::TYPE_BUSINESS);
        $transaction = new Transaction('2012-10-12', $user, Transaction::TYPE_WITHDRAW, new Money(12.2, 'USD'));
        $this->assertTrue($transaction->isWithdraw());
        $this->assertFalse($transaction->isDeposit());

        $transaction = new Transaction('2012-10-12', $user, Transaction::TYPE_DEPOSIT, new Money(12.2, 'USD'));
        $this->assertTrue($transaction->isDeposit());
        $this->assertFalse($transaction->isWithdraw());
    }

    /**
     * @return Generator
     */
    public function correctTransactionProvider(): Generator
    {
        $user = new User(1, User::TYPE_BUSINESS);

        yield ['2012-10-12', $user, Transaction::TYPE_WITHDRAW, new Money(12.2, 'USD')];
        yield ['2012-12-12', $user, Transaction::TYPE_WITHDRAW, new Money(12.0, 'USD')];
        yield ['2012-01-01', $user, Transaction::TYPE_DEPOSIT, new Money(0.0, 'USD')];
    }

    /**
     * @return Generator
     */
    public function incorrectTransactionProvider(): Generator
    {
        $user = new User(1, User::TYPE_BUSINESS);

        yield [1, $user, Transaction::TYPE_WITHDRAW, new Money(12.2, 'USD')];
        yield [1.2, $user, Transaction::TYPE_WITHDRAW, new Money(12.2, 'USD')];
        yield [true, $user, Transaction::TYPE_WITHDRAW, new Money(12.2, 'USD')];
        yield [[], $user, Transaction::TYPE_WITHDRAW, new Money(12.2, 'USD')];

        yield ['2012-01-01', '', Transaction::TYPE_DEPOSIT, new Money(0.0, 'USD')];
        yield ['2012-01-01', 1, Transaction::TYPE_DEPOSIT, new Money(0.0, 'USD')];
        yield ['2012-01-01', 1.1, Transaction::TYPE_DEPOSIT, new Money(0.0, 'USD')];
        yield ['2012-01-01', new StdClass(), Transaction::TYPE_DEPOSIT, new Money(0.0, 'USD')];
        yield ['2012-01-01', true, Transaction::TYPE_DEPOSIT, new Money(0.0, 'USD')];
        yield ['2012-01-01', [], Transaction::TYPE_DEPOSIT, new Money(0.0, 'USD')];

        yield ['2012-01-01', $user, 1, new Money(0.0, 'USD')];
        yield ['2012-01-01', $user, 1.1, new Money(0.0, 'USD')];
        yield ['2012-01-01', $user, true, new Money(0.0, 'USD')];
        yield ['2012-01-01', $user, [], new Money(0.0, 'USD')];
        yield ['2012-01-01', $user, new StdClass(), new Money(0.0, 'USD')];
    }
}
