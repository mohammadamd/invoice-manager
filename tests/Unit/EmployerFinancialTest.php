<?php

namespace Tests\Unit;

use App\Services\EmployerFinancial\EmployerFinancialInterface;
use App\Services\EmployerFinancial\Exceptions\InsufficientBalanceException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EmployerFinancialTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * Give employer an amount of credit and employer credit should be increased by that amount.
     */
    public function test_employer_creditor_should_increase_employer_credit()
    {
        /** @var EmployerFinancialInterface $employerFinancial */
        $employerFinancial = app(EmployerFinancialInterface::class);
        $employerFinancial->creditor(123, 23, 200);

        $this->assertDatabaseHas('employer_transactions', [
            'creditor' => 200,
            'employer_id' => 123,
            'current_balance' => 200,
            'transaction_type' => 23,
            'debtor' => 0
        ]);
    }

    /**
     * Reduce employer an amount of credit and employer credit should be decreased by that amount.
     */
    public function test_employer_debtor_should_decrease_employer_credit()
    {
        /** @var EmployerFinancialInterface $employerFinancial */
        $employerFinancial = app(EmployerFinancialInterface::class);
        $employerFinancial->creditor(123, 23, 200);

        $employerFinancial->debtor(123, 32, 100);

        $this->assertDatabaseHas('employer_transactions', [
            'creditor' => 0,
            'employer_id' => 123,
            'current_balance' => 100,
            'transaction_type' => 32,
            'debtor' => 100
        ]);
    }

    /**
     * Reduce from employer wallet more than current balance should throw insufficientBalance exception
     */
    public function test_employer_debtor_should_throw_insufficient_balance_when_employer_have_not_enough_credit_to_reduce()
    {
        /** @var EmployerFinancialInterface $employerFinancial */
        $employerFinancial = app(EmployerFinancialInterface::class);
        $employerFinancial->creditor(123, 23, 200);

        $this->expectException(InsufficientBalanceException::class);
        $employerFinancial->debtor(123, 32, 300);
    }

    /**
     * Get employer balance should return current balance of employer
     */
    public function test_employer_get_balance_should_return_current_balance_of_employer()
    {
        /** @var EmployerFinancialInterface $employerFinancial */
        $employerFinancial = app(EmployerFinancialInterface::class);
        $beforeCreditor = $employerFinancial->getCurrentBalance(123);

        $this->assertEquals(0, $beforeCreditor);

        $employerFinancial->creditor(123, 23, 200);

        $afterCreditor = $employerFinancial->getCurrentBalance(123);

        $this->assertEquals(200, $afterCreditor);
    }
}
