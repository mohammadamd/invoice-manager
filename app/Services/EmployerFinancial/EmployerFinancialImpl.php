<?php

namespace App\Services\EmployerFinancial;

use App\Models\EmployerTransactions;
use App\Services\EmployerFinancial\Exceptions\InsufficientBalanceException;
use App\Services\EmployerFinancial\Exceptions\NegativeAmountException;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EmployerFinancialImpl implements EmployerFinancialInterface
{
    /**
     * @inheritDoc
     */
    public function creditor($employerID, $transactionType, $amount): void
    {
        if ($amount < 0) {
            throw new NegativeAmountException();
        }

        DB::transaction(function () use ($employerID, $transactionType, $amount) {
            /** @var EmployerTransactions $lastTransaction */
            $lastTransaction = EmployerTransactions::whereEmployerId($employerID)->lockForUpdate()->latest('id')->first();
            $currentBalance = $lastTransaction->current_balance ?? 0;
            $newEmployerTransactions = new EmployerTransactions();
            $newEmployerTransactions->employer_id = $employerID;
            $newEmployerTransactions->creditor = $amount;
            $newEmployerTransactions->reference = $this->generateReference();
            $newEmployerTransactions->transaction_type = $transactionType;
            $newEmployerTransactions->current_balance = $currentBalance + $amount;
            $newEmployerTransactions->save();
        });
    }

    /**
     * @inheritDoc
     */
    public function debtor($employerID, $transactionType, $amount): void
    {
        if ($amount < 0) {
            throw new NegativeAmountException();
        }

        DB::transaction(function () use ($employerID, $transactionType, $amount) {
            /** @var EmployerTransactions $lastTransaction */
            $lastTransaction = EmployerTransactions::whereEmployerId($employerID)->lockForUpdate()->latest('id')->first();
            $currentBalance = $lastTransaction->current_balance ?? 0;

            if ($currentBalance < $amount) {
                throw new InsufficientBalanceException();
            }


            $newEmployerTransactions = new EmployerTransactions();
            $newEmployerTransactions->employer_id = $employerID;
            $newEmployerTransactions->debtor = $amount;
            $newEmployerTransactions->reference = $this->generateReference();
            $newEmployerTransactions->transaction_type = $transactionType;
            $newEmployerTransactions->current_balance = $currentBalance - $amount;
            $newEmployerTransactions->save();
        });
    }

    /**
     * @inheritDoc
     */
    public function getCurrentBalance($employerID): float
    {
        return EmployerTransactions::whereEmployerId($employerID)->latest()->first()->current_balance ?? 0;
    }

    private function generateReference(): string
    {
        return sprintf('TMP-%s-%d', Carbon::now()->format('Y-md-'), rand(100000, 999999));
    }
}
