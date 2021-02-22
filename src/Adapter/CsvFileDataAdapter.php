<?php

declare(strict_types=1);

namespace App\Adapter;

use App\Exception\DateFormatException;
use App\Exception\InvalidTransactionException;
use App\Exception\InvalidUserTypeException;
use App\Exception\OpeningFileException;
use App\Model\Transaction;
use App\Model\User;
use App\Utils\Output;
use App\Validator\DateFormatValidator;
use App\Validator\TransactionValidator;
use App\Validator\UserValidator;
use DateTime;

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
     * @param string $filePath path to a *.scv file
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
     * @return array
     */
    public function convert(): array
    {
        $transactionsArray = [];

        while (($data = fgetcsv($this->source)) !== false) {
            $transactionUser = $this->createUser((int)$data[1], $data[2]);
            $transaction = $this->createTransaction($data);
            $transaction->setUser($transactionUser);

            $weekKey = $transaction->getDate()->format('o-W');
            $transactionsArray[$weekKey][] = $transaction;
        }

        fclose($this->source);

        return $transactionsArray;
    }

    private function createUser(int $id, string $type): User
    {
        $transactionUser = new User($id, $type);

        try {
            UserValidator::validate($transactionUser);
        } catch (InvalidUserTypeException $exception) {
            Output::write($exception->getMessage());
            exit((string)$exception->getCode());
        }

        return $transactionUser;
    }

    private function createTransaction(array $data): Transaction
    {
        $dateString = $data[0];

        try {
            DateFormatValidator::validate($dateString, 'Y-m-d');
        } catch (DateFormatException $exception) {
            Output::write($exception->getMessage());
            exit((string)$exception->getCode());
        }

        $transaction = new Transaction();
        $transaction->setDate(DateTime::createFromFormat('Y-m-d', $dateString));
        $transaction->setType($data[3]);
        $transaction->setAmount((float)$data[4]);
        $transaction->setCurrencyCode($data[5]);

        try {
            TransactionValidator::validate($transaction);
        } catch (InvalidTransactionException $exception) {
            Output::write($exception->getMessage());
            exit((string)$exception->getCode());
        }

        return $transaction;
    }
}
