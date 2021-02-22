<?php

declare(strict_types=1);

namespace App\Adapter;

use App\Exception\OpeningFileException;
use App\Model\Transaction;
use App\Model\User;

/**
 * Class CsvFileDataAdapter
 *
 * @package App\Adapter
 */
class CsvFileDataAdapter implements DataAdapterInterface
{
    /**
     * @var resource
     */
    protected $source;

    /**
     * CsvFileDataAdapter constructor.
     * @param string $filePath      path to a *.scv file
     * @throws OpeningFileException
     */
    public function __construct(string $filePath)
    {
        $this->source = fopen($filePath, 'r');
        if ($this->source === false) {
            throw new OpeningFileException('Could not open file');
        }
    }

    /**
     * @inheritDoc
     */
    public function convert(): array
    {
        $transactionsArray = [];

        while (($data = fgetcsv($this->source)) !== false) {

            $transactionUser = new User((int)$data[1], $data[2]);

            $transaction = new Transaction();
            $transaction->setDate(\DateTime::createFromFormat('Y-m-d', $data[0]));
            $transaction->setUser($transactionUser);
            $transaction->setType($data[3]);
            $transaction->setAmount((float)$data[4]);
            $transaction->setCurrencyCode($data[5]);

            $weekKey = $transaction->getDate()->format('o-W');
            $transactionsArray[$weekKey][] = $transaction;
        }

        fclose($this->source);

        return $transactionsArray;
    }
}
