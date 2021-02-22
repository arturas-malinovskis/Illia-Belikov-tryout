<?php
declare(strict_types=1);


namespace App\Service;


interface TransactionManagerInterface
{
    /**
     * Transaction processing
     */
    public function processing();

    /**
     * It is used to replace the current array of transactions for processing
     *
     * @param array $transactionsArray
     */
    public function setTransactionsArray(array $transactionsArray): void;
}
