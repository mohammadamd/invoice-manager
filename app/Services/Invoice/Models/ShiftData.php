<?php

namespace App\Services\Invoice\Models;

use Carbon\Carbon;

class ShiftData
{
    /**
     * ID of the shift worker.
     *
     * @var int $workerID
     */
    private int $workerID;

    /**
     * ID of the shift employer.
     *
     * @var int $employerID
     */
    private int $employerID;

    /**
     * Insurance payable amount for shift.
     *
     * @var float $insurance
     */
    private float $insurance;

    /**
     * Shift gross price.
     *
     * @var float $shiftPrice
     */
    private float $shiftPrice;

    /**
     * Temper promotion given to this shift.
     *
     * @var float $temperPromotion
     */
    private float $temperPromotion;

    /**
     * Temper commission for this shift.
     *
     * @var int $commissionPercentage
     */
    private int $commissionPercentage;

    /**
     * Late fees for the shift per minute.
     *
     * @var float $lateFeesPerMin
     */
    private float $lateFeesPerMin;

    /**
     * Over time fees for the shift per minute.
     *
     * @var float $overTimePerMin
     */
    private float $overTimePerMin;

    /**
     * Tax percentage for the shift.
     *
     * @var int $taxPercentage
     */
    private int $taxPercentage;

    /**
     * Start time of shift.
     *
     * @var Carbon $shiftStartTime
     */
    private Carbon $shiftStartTime;

    /**
     * Finish time of shift.
     *
     * @var Carbon $shiftFinishTime
     */
    private Carbon $shiftFinishTime;

    /**
     * Worker start time.
     *
     * @var Carbon $checkInTime
     */
    private Carbon $checkInTime;

    /**
     * Worker finish time.
     *
     * @var Carbon $checkOutTime
     */
    private Carbon $checkOutTime;

    /**
     * Create ShiftData from array with these keys:
     * float insurance
     * float shift_price
     * float temper_promotion
     * int commission_percentage
     * float late_fees_per_min
     * float over_time_per_min
     * int taxPercentage
     * carbon shift_start_time
     * carbon shift_finish_time
     * carbon check_in_time
     * carbon check_out_time
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->employerID = $data['employer_id'];
        $this->workerID = $data['worker_id'];
        $this->insurance = $data['insurance'];
        $this->shiftPrice = $data['shift_price'];
        $this->temperPromotion = $data['temper_promotion'];
        $this->commissionPercentage = $data['commission_percentage'];
        $this->lateFeesPerMin = $data['late_fees_per_min'];
        $this->overTimePerMin = $data['over_time_per_min'];
        $this->taxPercentage = $data['tax_percentage'];
        $this->shiftStartTime = Carbon::parse($data['shift_start_time']);
        $this->shiftFinishTime = Carbon::parse($data['shift_finish_time']);
        $this->checkInTime = Carbon::parse($data['check_in_time']);
        $this->checkOutTime = Carbon::parse($data['check_out_time']);
    }

    public function getInsurance(): float
    {
        return $this->insurance;
    }

    public function getShiftPrice(): float
    {
        return $this->shiftPrice;
    }

    public function getTemperPromotion(): float
    {
        return $this->temperPromotion;
    }

    public function getCommissionPercentage(): int
    {
        return $this->commissionPercentage;
    }

    public function getLateFeesPerMin(): float
    {
        return $this->lateFeesPerMin;
    }

    public function getOverTimePerMin(): float
    {
        return $this->overTimePerMin;
    }

    public function getShiftStartTime(): Carbon
    {
        return $this->shiftStartTime;
    }

    public function getShiftFinishTime(): Carbon
    {
        return $this->shiftFinishTime;
    }

    public function getCheckInTime(): Carbon
    {
        return $this->checkInTime;
    }

    public function getCheckOutTime(): Carbon
    {
        return $this->checkOutTime;
    }

    public function getTaxPercentage(): int
    {
        return $this->taxPercentage;
    }

    public function getWorkerID(): int
    {
        return $this->workerID;
    }

    public function getEmployerID(): int
    {
        return $this->employerID;
    }
}
