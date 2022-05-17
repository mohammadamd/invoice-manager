<?php

namespace App\Services\WorkerFinancial;

use App\Models\WorkerTransaction;
use App\Services\WorkerFinancial\Exceptions\InsufficientBalanceException;
use App\Services\WorkerFinancial\Exceptions\NegativeAmountException;
use Illuminate\Database\Eloquent\Collection;

interface WorkerFinancialInterface
{
    /**
     * Gives the mentioned worker credit
     *
     * @param $workerID
     * @param $transactionType
     * @param $amount
     * @throws NegativeAmountException
     */
    public function creditor($workerID, $transactionType, $amount): void;

    /**
     * Reduce credit from mentioned worker
     *
     * @param $workerID
     * @param $transactionType
     * @param $amount
     * @throws InsufficientBalanceException
     * @throws NegativeAmountException
     */
    public function debtor($workerID, $transactionType, $amount): void;

    /**
     * Return current balance of the worker
     *
     * @param $workerID
     * @return float
     */
    public function getCurrentBalance($workerID): float;

    /**
     * @param $workerID
     * @param $page
     * @return Collection
     */
    public function getFinancialHistory($workerID, $page): Collection;
}
