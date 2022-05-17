<?php

namespace App\Http\Controllers;

use App\Http\Requests\CalculateInvoice;
use App\Services\Invoice\InvoiceInterface;
use App\Services\Invoice\Models\ShiftData;
use Illuminate\Http\JsonResponse;

class InvoiceController extends Controller
{
    public function calculateInvoice(CalculateInvoice $request, InvoiceInterface $invoice): JsonResponse
    {
        $shiftData = new ShiftData($request->all());
        $shiftInvoice = $invoice->calculateInvoice($shiftData);
        return response()->json($shiftInvoice);
    }

    public function settleInvoice(CalculateInvoice $request, InvoiceInterface $invoice): JsonResponse
    {
        $shiftData = new ShiftData($request->all());
        $shiftInvoice = $invoice->calculateInvoice($shiftData);
        $invoice->SettleInvoice($shiftInvoice);

        return response()->json(['message' => 'done']);
    }
}
