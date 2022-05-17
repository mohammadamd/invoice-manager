<?php

namespace Tests\Feature;

use App\Models\WorkerTransaction;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FinancialHistoryTest extends TestCase
{
    use DatabaseTransactions;

    public function test_get_worker_financial_history_should_return_list_of_transactions()
    {
        $workerTransaction = WorkerTransaction::factory()->make();
        $workerTransaction->save();
        $response = $this->get('/api/v1/worker/financial-history', ['user_id' => $workerTransaction->worker_id]);

        $response->assertSimilarJson([[
            "id" => $workerTransaction->id,
            "reference" => $workerTransaction->reference,
            "worker_id" => $workerTransaction->worker_id,
            "creditor" => $workerTransaction->creditor,
            "debtor" => $workerTransaction->debtor,
            "current_balance" => $workerTransaction->current_balance,
            "transaction_type" => $workerTransaction->transaction_type,
            "created_at" => $workerTransaction->created_at->toJSON(),
            "updated_at" => $workerTransaction->updated_at->toJSON(),
        ]]);
    }
}
