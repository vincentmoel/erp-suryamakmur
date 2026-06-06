<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\ReceiptDetail;

class InvoicePaymentService
{
    /**
     * Recalculate paid_amount and status for one invoice.
     *
     * Sums all receipt_details from non-deleted receipts, then derives
     * the correct InvoiceStatus. Safe to call after any receipt mutation.
     * Skips DRAFT and CANCELLED invoices — they are never in a payable state.
     */
    public static function recalculate(int $invoiceId): void
    {
        $invoice = Invoice::lockForUpdate()->findOrFail($invoiceId);

        if (in_array($invoice->status, [InvoiceStatus::DRAFT, InvoiceStatus::CANCELLED])) {
            return;
        }

        $paidAmount = (int) ReceiptDetail::whereHas('receipt', fn($q) => $q->whereNull('deleted_at'))
            ->where('invoice_id', $invoiceId)
            ->sum('amount');

        $grandTotal = $invoice->amount;

        $newStatus = match (true) {
            $paidAmount >= $grandTotal && $grandTotal > 0 => InvoiceStatus::PAID,
            $paidAmount > 0                               => InvoiceStatus::PARTIALLY_PAID,
            default                                       => InvoiceStatus::WAITING_FOR_PAYMENT,
        };

        $invoice->update([
            'paid_amount' => $paidAmount,
            'status'      => $newStatus,
        ]);
    }

    /**
     * Recalculate multiple invoices in one call.
     */
    public static function recalculateMany(array $invoiceIds): void
    {
        foreach (array_unique($invoiceIds) as $invoiceId) {
            static::recalculate($invoiceId);
        }
    }
}
