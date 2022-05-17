<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class WorkerTransaction
 *
 * @package App\Models
 * @property int $id
 * @property string $reference
 * @property int $worker_id
 * @property float $creditor
 * @property float $debtor
 * @property float $current_balance
 * @property int $transaction_type
 * @mixin Model
 * @method static whereWorkerId($value)
 */
class WorkerTransaction extends Model
{
    use HasFactory;

    protected $table = 'worker_transactions';

    protected $fillable = [
        'reference',
        'worker_id',
        'creditor',
        'debtor',
        'current_balance',
        'transaction_type',
    ];
}
