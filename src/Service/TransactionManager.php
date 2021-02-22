<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\NoCurrencyRateException;
use App\Utils\Output;

class TransactionManager implements TransactionManagerInterface
{
    /**
     * @var array
     */
    private $transactionsArray;

    /**
     * @var TransactionCalculatorInterface
     */
    private $calculator;

    public function __construct(array $transactionsArray = [], TransactionCalculatorInterface $calculator = null)
    {
        $this->transactionsArray = $transactionsArray;
        $this->calculator = $calculator ?? new FeeCalculator();
    }

    /**
     * Transaction processing week by week
     */
    public function processing()
    {
        $fees = [];
        foreach ($this->transactionsArray as $weekArray) {
            foreach ($weekArray as $transaction) {
                try {
                    $fees[] = $this->calculator->calculate($transaction);
                } catch (NoCurrencyRateException $exception) {
                    Output::write($exception->getMessage());
                    exit((string)$exception->getCode());
                }
            }
        }
        $this->calculator->clear();

        Output::write($fees);
    }

    /**
     * @inheritDoc
     */
    public function setTransactionsArray(array $transactionsArray): void
    {
        $this->transactionsArray = $transactionsArray;
    }
}
