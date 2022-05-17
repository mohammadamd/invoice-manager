<?php

namespace App\Services\Invoice;

use App\Models\Invoice;
use App\Services\Invoice\Models\ShiftData;

Interface InvoiceInterface {
    /**
     * Calculate invoice based on received shift data
     *
     * @param ShiftData $shiftData
     * @return Invoice
     */
    public function calculateInvoice(ShiftData $shiftData): Invoice;

    /**
     * Store invoice into database
     *
     * @param Invoice $invoice
     */
    public function SettleInvoice(Invoice $invoice): void;
}
