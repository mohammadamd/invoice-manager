<?php

namespace App\Http\Controllers;

use App\Services\WorkerFinancial\WorkerFinancialInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkerController extends Controller
{
    public function getFinancialHistory(Request $request, WorkerFinancialInterface $workerFinancial): JsonResponse
    {
        $page = $request->get('page');
        return response()->json($workerFinancial->getFinancialHistory($request->User(), $page));
    }

    public function getBalance(Request $request, WorkerFinancialInterface $workerFinancial): JsonResponse
    {
        return response()->json(['balance' => $workerFinancial->getCurrentBalance($request->User())]);
    }
}
