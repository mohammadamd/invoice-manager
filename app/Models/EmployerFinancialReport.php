<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EmployerFinancialReport
 *
 * @package App\Models
 * @property int $id
 * @property int $employer_id
 * @property int $salary_paid
 * @property float $insurance
 * @property float $tax
 * @property float $bonus
 * @property Carbon $date
 * @mixin Model
 * @method static whereEmployerId($value)
 * @method static whereDate($value)
 */
class EmployerFinancialReport extends Model
{
    use HasFactory;

    protected $table = 'employer_financial_reports';

    protected $fillable = [
        'employer_id',
        'salary_paid',
        'insurance',
        'tax',
        'bonus',
        'date',
    ];
}
