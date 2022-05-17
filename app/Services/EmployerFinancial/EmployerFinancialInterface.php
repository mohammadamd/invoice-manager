<?php

namespace App\Services\EmployerFinancial;

use App\Services\EmployerFinancial\Exceptions\NegativeAmountException;
use App\Services\WorkerFinancial\Exceptions\InsufficientBalanceException;

interface EmployerFinancialInterface {
    /**
     * Gives the mentioned employer credit
     *
     * @param $employerID
     * @param $transactionType
     * @param $amount
     * @throws NegativeAmountException
     */
    public function creditor($employerID, $transactionType, $amount): void;

    /**
     * Reduce credit from mentioned employer
     *
     * @param $employerID
     * @param $transactionType
     * @param $amount
     * @throws InsufficientBalanceException
     * @throws NegativeAmountException
     */
    public function debtor($employerID, $transactionType, $amount): void;

    /**
     * Return current balance of the employer
     *
     * @param $employerID
     * @return float
     */
    public function getCurrentBalance($employerID): float;
}
