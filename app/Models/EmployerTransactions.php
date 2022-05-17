<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EmployerTransactions
 *
 * @package App\Models
 * @property string $id
 * @property string $reference
 * @property int $employer_id
 * @property float $creditor
 * @property float $debtor
 * @property float $current_balance
 * @property int $transaction_type
 * @mixin Model
 * @method static whereEmployerId($value)
 */
class EmployerTransactions extends Model
{
    use HasFactory;

    protected $table = 'employer_transactions';

    protected $fillable = [
        'reference',
        'employer_id',
        'creditor',
        'debtor',
        'current_balance',
        'transaction_type',
    ];
}
