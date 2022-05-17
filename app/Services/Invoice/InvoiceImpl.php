<?php

namespace App\Services\Invoice;

use App\Enums\EmployerTransactionTypes;
use App\Enums\WorkerTransactionTypes;
use App\Models\EmployerFinancialReport;
use App\Models\Invoice;
use App\Models\WorkerFinancialReport;
use App\Services\EmployerFinancial\EmployerFinancialInterface;
use App\Services\Invoice\Models\ShiftData;
use App\Services\WorkerFinancial\Exceptions\InsufficientBalanceException;
use App\Services\WorkerFinancial\WorkerFinancialInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use JetBrains\PhpStorm\Pure;

class InvoiceImpl implements InvoiceInterface
{
    /**
     * @inheritDoc
     */
    public function calculateInvoice(ShiftData $shiftData): Invoice
    {
        $fine = $this->calculateFine($shiftData);
        $tax = $this->calculateTax($shiftData);
        $overWork = $this->calculateOverWork($shiftData);
        $commission = $this->calculateCommission($shiftData);
        $temperPayable = $this->calculateTemperPromotion($shiftData);
        $workerIncome = round($shiftData->getShiftPrice() - $fine + $overWork - $commission - $tax - $shiftData->getInsurance(), 2);
        $employerPayable = round($shiftData->getShiftPrice() - $fine + $overWork - $temperPayable, 2);

        $invoice = new Invoice();
        $invoice->net_income = $workerIncome;
        $invoice->gross_income = $shiftData->getShiftPrice();
        $invoice->commission = $commission;
        $invoice->insurance = $shiftData->getInsurance();
        $invoice->bonus = $overWork;
        $invoice->employer_id = $shiftData->getEmployerID();
        $invoice->worker_id = $shiftData->getWorkerID();
        $invoice->employer_payable = $employerPayable;
        $invoice->temper_payable = $temperPayable;
        $invoice->tax = $tax;
        $invoice->fine = $fine;

        return $invoice;
    }

    /**
     * @inheritDoc
     */
    public function SettleInvoice(Invoice $invoice): void
    {
        DB::transaction(function () use ($invoice) {
            $this->updateOrCreateWorkerFinancialReport($invoice);
            $this->updateOrCreateEmployerFinancialReport($invoice);
            $this->doWorkerPayments($invoice);
            $this->doEmployerPayments($invoice);
        });
    }

    /**
     * Calculate Temper promotion.
     *
     * @param ShiftData $shiftData
     * @return float
     */
    #[Pure] private function calculateTemperPromotion(ShiftData $shiftData): float
    {
        return round($shiftData->getTemperPromotion() * $shiftData->getShiftPrice() / 100, 2);
    }

    /**
     * Calculate Temper commission.
     *
     * @param ShiftData $shiftData
     * @return float
     */
    #[Pure] private function calculateCommission(ShiftData $shiftData): float
    {
        return round($shiftData->getCommissionPercentage() * $shiftData->getShiftPrice() / 100, 2);
    }

    /**
     * Calculate fine of the shift.
     * because of being late or leaving soon
     *
     * @param ShiftData $shiftData
     * @return float
     */
    private function calculateFine(ShiftData $shiftData): float
    {
        $delay = 0;
        $early = 0;
        if ($shiftData->getShiftStartTime() < $shiftData->getCheckInTime()) {
            $delay = $shiftData->getCheckInTime()->diffInMinutes($shiftData->getShiftStartTime());
        }

        if ($shiftData->getShiftFinishTime() > $shiftData->getCheckOutTime()) {
            $early = $shiftData->getShiftFinishTime()->diffInMinutes($shiftData->getCheckOutTime());
        }

        return round($shiftData->getLateFeesPerMin() * ($delay + $early), 2);
    }

    /**
     * Calculate overwork.
     *
     * @param ShiftData $shiftData
     * @return float
     */
    private function calculateOverWork(ShiftData $shiftData): float
    {
        $shiftDuration = $shiftData->getCheckOutTime()->diffInMinutes($shiftData->getCheckInTime());
        $workedDuration = $shiftData->getShiftFinishTime()->diffInMinutes($shiftData->getShiftStartTime());
        $extra = min(0, $workedDuration - $shiftDuration);

        return round($shiftData->getOverTimePerMin() * $extra, 2);
    }

    #[Pure] private function calculateTax(ShiftData $shiftData): float
    {
        return round($shiftData->getTaxPercentage() * $shiftData->getShiftPrice() / 100, 2);
    }

    /**
     * Calculate and update or create worker financial report.
     *
     * @param Invoice $invoice
     * @return void
     */
    private function updateOrCreateWorkerFinancialReport(Invoice $invoice): void
    {
        $financialReport = WorkerFinancialReport::whereWorkerId($invoice->worker_id)->where('date', Carbon::today()->format('Y-m-d'))->first();
        if (is_null($financialReport)) {
            $financialReport = new WorkerFinancialReport();
        }

        $financialReport->worker_id = $invoice->worker_id;
        $financialReport->income += $invoice->net_income;
        $financialReport->insurance += $invoice->insurance;
        $financialReport->tax += $invoice->tax;
        $financialReport->fine += $invoice->fine;
        $financialReport->bonus += $invoice->bonus;
        $financialReport->date = Carbon::now()->format('Y-m-d');
        $financialReport->save();
    }

    /**
     * Calculate and update or create worker financial report.
     *
     * @param Invoice $invoice
     * @return void
     */
    private function updateOrCreateEmployerFinancialReport(Invoice $invoice): void
    {
        $financialReport = EmployerFinancialReport::whereEmployerId($invoice->worker_id)->where('date', Carbon::today()->format('Y-m-d'))->first();
        if (is_null($financialReport)) {
            $financialReport = new EmployerFinancialReport();
        }

        $financialReport->employer_id = $invoice->employer_id;
        $financialReport->salary_paid = $invoice->employer_payable;
        $financialReport->insurance += $invoice->insurance;
        $financialReport->tax += $invoice->tax;
        $financialReport->bonus += $invoice->bonus;
        $financialReport->date = Carbon::now()->format('Y-m-d');
        $financialReport->save();
    }

    /**
     * Pay worker payments
     *
     * @param Invoice $invoice
     * @throws InsufficientBalanceException
     */
    private function doWorkerPayments(Invoice $invoice): void
    {
        /** @var WorkerFinancialInterface $workerFinancialImpl */
        $workerFinancialImpl = app(WorkerFinancialInterface::class);
        $workerFinancialImpl->creditor($invoice->worker_id, WorkerTransactionTypes::INCOME, $invoice->gross_income);

        if ($invoice->commission != 0) {
            $workerFinancialImpl->debtor($invoice->worker_id, WorkerTransactionTypes::COMMISSION, $invoice->commission);
        }

        if ($invoice->bonus != 0) {
            $workerFinancialImpl->debtor($invoice->worker_id, WorkerTransactionTypes::BONUS, $invoice->bonus);
        }

        if ($invoice->insurance != 0) {
            $workerFinancialImpl->debtor($invoice->worker_id, WorkerTransactionTypes::INSURANCE, $invoice->insurance);
        }

        if ($invoice->tax != 0) {
            $workerFinancialImpl->debtor($invoice->worker_id, WorkerTransactionTypes::TAX, $invoice->tax);
        }

        if ($invoice->fine != 0) {
            $workerFinancialImpl->debtor($invoice->worker_id, WorkerTransactionTypes::FINE, $invoice->fine);
        }
    }

    /**
     * Pay employer Payments
     *
     * @param Invoice $invoice
     * @throws InsufficientBalanceException
     */
    public function doEmployerPayments(Invoice $invoice): void
    {
        /** @var EmployerFinancialInterface $employerFinancialImpl */
        $employerFinancialImpl = app(EmployerFinancialInterface::class);
        $employerFinancialImpl->debtor($invoice->employer_id, EmployerTransactionTypes::SALARY_PAYMENT, $invoice->employer_payable);
    }
}
