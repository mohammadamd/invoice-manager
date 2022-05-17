<?php

namespace Tests\Unit;

use App\Enums\EmployerTransactionTypes;
use App\Enums\WorkerTransactionTypes;
use App\Models\WorkerTransaction;
use App\Services\EmployerFinancial\EmployerFinancialInterface;
use App\Services\Invoice\InvoiceInterface;
use App\Services\Invoice\Models\ShiftData;
use App\Services\WorkerFinancial\WorkerFinancialInterface;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_invoice_calculation_should_return_valid_values()
    {
        /** @var InvoiceInterface $invoice */
        $invoice = app(InvoiceInterface::class);
        $shiftData = new ShiftData([
            'employer_id' => 123,
            'worker_id' => 321,
            'insurance' => 20.2,
            'shift_price' => 768.7,
            'temper_promotion' => 10,
            'commission_percentage' => 20,
            'late_fees_per_min' => 10,
            'over_time_per_min' => 20,
            'tax_percentage' => 9,
            'shift_start_time' => Carbon::now()->subHours(8),
            'shift_finish_time' => Carbon::now(),
            'check_in_time' => Carbon::now()->subHours(8)->addMinutes(25),
            'check_out_time' => Carbon::now()->addMinutes(10),
        ]);

        $shiftInvoice = $invoice->calculateInvoice($shiftData);
        $this->assertEquals(321, $shiftInvoice->worker_id);
        $this->assertEquals(123, $shiftInvoice->employer_id);
        $this->assertEquals(275.58, $shiftInvoice->net_income);
        $this->assertEquals(768.7, $shiftInvoice->gross_income);
        $this->assertEquals(250, $shiftInvoice->fine);
        $this->assertEquals(153.74, $shiftInvoice->commission);
        $this->assertEquals(20.2, $shiftInvoice->insurance);
        $this->assertEquals(0, $shiftInvoice->bonus);
        $this->assertEquals(69.18, $shiftInvoice->tax);
        $this->assertEquals(441.83, $shiftInvoice->employer_payable);
        $this->assertEquals(76.87, $shiftInvoice->temper_payable);
    }

    public function test_settle_invoice_should_store_data_invoice_and_reports_into_database()
    {
        /** @var EmployerFinancialInterface $employerFinancial */
        $employerFinancial = app(EmployerFinancialInterface::class);
        /** @var WorkerFinancialInterface $workerFinancial */
        $workerFinancial = app(WorkerFinancialInterface::class);
        /** @var InvoiceInterface $invoice */
        $invoice = app(InvoiceInterface::class);
        $shiftData = new ShiftData([
            'employer_id' => 123,
            'worker_id' => 321,
            'insurance' => 20.2,
            'shift_price' => 768.7,
            'temper_promotion' => 10,
            'commission_percentage' => 20,
            'late_fees_per_min' => 10,
            'over_time_per_min' => 20,
            'tax_percentage' => 9,
            'shift_start_time' => Carbon::now()->subHours(8),
            'shift_finish_time' => Carbon::now(),
            'check_in_time' => Carbon::now()->subHours(8)->addMinutes(25),
            'check_out_time' => Carbon::now()->addMinutes(10),
        ]);


        $shiftInvoice = $invoice->calculateInvoice($shiftData);
        $employerFinancial->creditor(123, 34, 800);
        $invoice->SettleInvoice($shiftInvoice);

        $this->assertDatabaseHas('employer_financial_reports', [
            'employer_id' => 123,
            'salary_paid' => 441.83,
            'insurance' => 20.2,
            'tax' => 69.18,
            'bonus' => 0,
            'date' => Carbon::now()->format('Y-m-d'),
        ]);

        $this->assertDatabaseHas('employer_transactions', [
            'employer_id' => 123,
            'creditor' => 0,
            'debtor' => 441.83,
            'current_balance' => 358.17,
            'transaction_type' => EmployerTransactionTypes::SALARY_PAYMENT,
        ]);

        $this->assertDatabaseHas('worker_financial_reports', [
            'worker_id' => 321,
            'income' => 275.58,
            'insurance' => 20.20,
            'tax' => 69.18,
            'fine' => 250,
            'bonus' => 0,
            'date' => Carbon::now()->format('Y-m-d'),
        ]);

        $this->assertDatabaseHas('worker_transactions', [
            'worker_id' => 321,
            'creditor' => 768.7,
            'debtor' => 0,
            'transaction_type' => WorkerTransactionTypes::INCOME,
        ]);

        $this->assertDatabaseHas('worker_transactions', [
            'worker_id' => 321,
            'creditor' => 0,
            'debtor' => 153.74,
            'transaction_type' => WorkerTransactionTypes::COMMISSION,
        ]);

        $this->assertDatabaseHas('worker_transactions', [
            'worker_id' => 321,
            'creditor' => 0,
            'debtor' => 20.2,
            'transaction_type' => WorkerTransactionTypes::INSURANCE,
        ]);

        $this->assertDatabaseHas('worker_transactions', [
            'worker_id' => 321,
            'creditor' => 0,
            'debtor' => 69.18,
            'transaction_type' => WorkerTransactionTypes::TAX,
        ]);

        $this->assertDatabaseHas('worker_transactions', [
            'worker_id' => 321,
            'creditor' => 0,
            'debtor' => 250,
            'transaction_type' => WorkerTransactionTypes::FINE,
        ]);

        self::assertEquals(275.58, $workerFinancial->getCurrentBalance(321));
    }
}
