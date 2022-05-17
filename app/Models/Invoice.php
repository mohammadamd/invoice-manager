<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Invoice
 *
 * @package App\Models
 * @property int $id
 * @property int $worker_id
 * @property int $employer_id
 * @property float $net_income
 * @property float $gross_income
 * @property float $fine
 * @property float $commission
 * @property float $insurance
 * @property float $bonus
 * @property float $tax
 * @property float $employer_payable
 * @property float $temper_payable
 * @mixin Model
 */
class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices';

    protected $fillable = [
        'worker_id',
        'employer_id',
        'net_income',
        'gross_income',
        'fine',
        'commission',
        'insurance',
        'bonus',
        'tax',
        'employer_payable',
        'temper_payable',
    ];
}
