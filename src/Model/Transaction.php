<?php

declare(strict_types=1);

namespace App\Model;

use DateTime;
use DateTimeInterface;
use Evp\Component\Money\Money;

class Transaction
{
    public const TYPE_DEPOSIT = 'deposit';
    public const TYPE_WITHDRAW = 'withdraw';

    /**
     * @var DateTimeInterface
     */
    protected $date;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var Money
     */
    protected $payment;

    public function __construct(
        string $date = null,
        ?User $user = null,
        string $type = null,
        Money $payment = null
    ) {
        $this->date = DateTime::createFromFormat('Y-m-d', $date ?? '');
        $this->user = $user;
        $this->type = $type;
        $this->payment = $payment;
    }

    /**
     * @return DateTimeInterface
     */
    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    /**
     * @param DateTimeInterface $date
     */
    public function setDate(DateTimeInterface $date)
    {
        $this->date = $date;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return Money
     */
    public function getPayment(): Money
    {
        return $this->payment;
    }

    /**
     * @param Money $payment
     */
    public function setPayment(Money $payment): void
    {
        $this->payment = $payment;
    }

    public function getAmount()
    {
        return $this->payment->getAmount();
    }

    /**
     * @return string
     */
    public function getCurrencyCode(): string
    {
        return $this->payment->getCurrency();
    }

    public function isDeposit(): bool
    {
        return $this->type === self::TYPE_DEPOSIT;
    }

    public function isWithdraw(): bool
    {
        return $this->type === self::TYPE_WITHDRAW;
    }
}
