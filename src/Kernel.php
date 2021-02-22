<?php

declare(strict_types=1);

namespace App;

use App\Service\TransactionManagerInterface;

class Kernel
{
    private $transactionManager;

    /**
     * Kernel constructor.
     * @param TransactionManagerInterface $transactionManager
     */
    public function __construct(TransactionManagerInterface $transactionManager)
    {
        $this->transactionManager = $transactionManager;
    }

    /**
     * Run this app
     */
    public function run()
    {
        $this->transactionManager->processing();
    }
}
