<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CalculateInvoice extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'employer_id' => 'required|numeric',
            'worker_id' => 'required|numeric',
            'insurance' => 'required|numeric',
            'shift_price' => 'required|numeric',
            'temper_promotion' => 'required|numeric',
            'commission_percentage' => 'required|numeric',
            'late_fees_per_min' => 'required|numeric',
            'over_time_per_min' => 'required|numeric',
            'tax_percentage' => 'required|numeric',
            'shift_start_time' => 'required|date',
            'shift_finish_time' => 'required|date',
            'check_in_time' => 'required|date',
            'check_out_time' => 'required|date',
        ];
    }
}
