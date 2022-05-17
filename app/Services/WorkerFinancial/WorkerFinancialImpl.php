<?php

namespace App\Services\WorkerFinancial;

use App\Models\WorkerTransaction;
use App\Services\WorkerFinancial\Exceptions\InsufficientBalanceException;
use App\Services\WorkerFinancial\Exceptions\NegativeAmountException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class WorkerFinancialImpl implements WorkerFinancialInterface
{
    /**
     * @inheritDoc
     */
    public function creditor($workerID, $transactionType, $amount): void
    {
        if ($amount < 0) {
            throw new NegativeAmountException();
        }

        DB::transaction(function () use ($workerID, $transactionType, $amount) {
            /** @var WorkerTransaction $lastTransaction */
            $lastTransaction = WorkerTransaction::whereWorkerId($workerID)->lockForUpdate()->latest('id')->first();
            $currentBalance = $lastTransaction->current_balance ?? 0;

            $newWorkerTransaction = new WorkerTransaction();
            $newWorkerTransaction->worker_id = $workerID;
            $newWorkerTransaction->creditor = $amount;
            $newWorkerTransaction->reference = $this->generateReference();
            $newWorkerTransaction->transaction_type = $transactionType;
            $newWorkerTransaction->current_balance = $currentBalance + $amount;
            $newWorkerTransaction->save();
        });
    }

    /**
     * @inheritDoc
     */
    public function debtor($workerID, $transactionType, $amount): void
    {
        if ($amount < 0) {
            throw new NegativeAmountException();
        }

        DB::transaction(function () use ($workerID, $transactionType, $amount) {
            /** @var WorkerTransaction $lastTransaction */
            $lastTransaction = WorkerTransaction::whereWorkerId($workerID)->lockForUpdate()->latest('id')->first();
            $currentBalance = $lastTransaction->current_balance ?? 0;

            if ($currentBalance < $amount) {
                throw new InsufficientBalanceException();
            }

            $newWorkerTransaction = new WorkerTransaction();
            $newWorkerTransaction->worker_id = $workerID;
            $newWorkerTransaction->debtor = $amount;
            $newWorkerTransaction->reference = $this->generateReference();
            $newWorkerTransaction->transaction_type = $transactionType;
            $newWorkerTransaction->current_balance = $currentBalance - $amount;
            $newWorkerTransaction->save();
        });
    }

    /**
     * @inheritDoc
     */
    public function getCurrentBalance($workerID): float
    {
        return WorkerTransaction::whereWorkerId($workerID)->latest()->first()->current_balance ?? 0;
    }

    /**
     * @inheritDoc
     */
    public function getFinancialHistory($workerID, $page): Collection
    {
        return WorkerTransaction::whereWorkerId($workerID)->orderBy('id', 'desc')->skip(($page - 1) * 15)->take(15)->get();
    }

    private function generateReference(): string
    {
        return sprintf('TMP-%s-%d', Carbon::now()->format('Y-md'), rand(100000, 999999));
    }
}
