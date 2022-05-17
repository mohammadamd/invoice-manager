<?php

namespace Tests\Feature;

use App\Models\WorkerTransaction;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CalculateInvoiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_calculate_invoice_should_return_shift_invoice_with_valid_request()
    {
        $response = $this->post('/api/internal/calculate-invoice', [
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

        $response->assertSimilarJson([
            "net_income" => 275.58,
            "gross_income" => 768.7,
            "commission" => 153.74,
            "insurance" => 20.2,
            "bonus" => 0,
            "employer_id" => 123,
            "worker_id" => 321,
            "employer_payable" => 441.83,
            "temper_payable" => 76.87,
            "tax" => 69.18,
            "fine" => 250,
        ]);
    }
}
