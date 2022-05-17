<?php

namespace Tests\Unit;

use App\Models\EmployerTransactions;
use App\Models\WorkerTransaction;
use App\Services\EmployerFinancial\EmployerFinancialImpl;
use App\Services\WorkerFinancial\WorkerFinancialInterface;
use App\Services\WorkerFinancial\Exceptions\InsufficientBalanceException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class WorkerFinancialTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Give worker an amount of credit and worker credit should be increased by that amount.
     */
    public function test_worker_creditor_should_increase_worker_credit()
    {
        /** @var WorkerFinancialInterface $workerFinancial */
        $workerFinancial = app(WorkerFinancialInterface::class);
        $workerFinancial->creditor(123, 23, 200);

        $this->assertDatabaseHas('worker_transactions', [
            'creditor' => 200,
            'worker_id' => 123,
            'current_balance' => 200,
            'transaction_type' => 23,
            'debtor' => 0
        ]);
    }

    /**
     * Reduce worker an amount of credit and worker credit should be decreased by that amount.
     */
    public function test_worker_debtor_should_decrease_worker_credit()
    {
        /** @var WorkerFinancialInterface $workerFinancial */
        $workerFinancial = app(WorkerFinancialInterface::class);
        $workerFinancial->creditor(123, 23, 200);

        $workerFinancial->debtor(123, 32, 100);

        $this->assertDatabaseHas('worker_transactions', [
            'creditor' => 0,
            'worker_id' => 123,
            'current_balance' => 100,
            'transaction_type' => 32,
            'debtor' => 100
        ]);
    }

    /**
     * Reduce from worker wallet more than current balance should throw insufficientBalance exception
     */
    public function test_worker_debtor_should_throw_insufficient_balance_when_worker_have_not_enough_credit_to_reduce()
    {
        /** @var WorkerFinancialInterface $workerFinancial */
        $workerFinancial = app(WorkerFinancialInterface::class);
        $workerFinancial->creditor(123, 23, 200);

        $this->expectException(InsufficientBalanceException::class);
        $workerFinancial->debtor(123, 32, 300);
    }

    /**
     * Get worker balance should return current balance of worker
     */
    public function test_worker_get_balance_should_return_current_balance_of_worker()
    {
        /** @var WorkerFinancialInterface $workerFinancial */
        $workerFinancial = app(WorkerFinancialInterface::class);
        $beforeCreditor = $workerFinancial->getCurrentBalance(123);

        $this->assertEquals(0, $beforeCreditor);

        $workerFinancial->creditor(123, 23, 200);

        $afterCreditor = $workerFinancial->getCurrentBalance(123);

        $this->assertEquals(200, $afterCreditor);
    }

    /**
     * FinancialHistory should return worker financial history
     */
    public function test_worker_get_history_should_return_worker_history()
    {
        /** @var WorkerFinancialInterface $workerFinancial */
        $workerFinancial = app(WorkerFinancialInterface::class);

        $workerFinancial->creditor(123, 23, 600.5);
        $workerFinancial->debtor(123, 23, 245.2);

        $financialHistory = $workerFinancial->getFinancialHistory(123, 1);
        $expected = WorkerTransaction::whereWorkerId(123)->orderBy('id', 'desc')->get();
        $this->assertEquals($expected, $financialHistory);
    }
}
