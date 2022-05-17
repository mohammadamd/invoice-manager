<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class WorkerFinancialReport
 *
 * @package App\Models
 * @property int $id
 * @property int $worker_id
 * @property float $income
 * @property float $insurance
 * @property float $tax
 * @property float $fine
 * @property float $bonus
 * @property Carbon $date
 * @mixin Model
 * @method static whereWorkerId($value)
 * @method static whereDate($value)
 */
class WorkerFinancialReport extends Model
{
    use HasFactory;

    protected $table = 'worker_financial_reports';

    protected $fillable = [
        'worker_id',
        'income',
        'insurance',
        'tax',
        'fine',
        'bonus',
        'date',
    ];
}
