<?php

namespace Modules\MM\Valuation\Application\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
// Assuming an Invoice model will be created later in the Procurement module
// For now, we can pass the relevant data directly.
class SupplierInvoiceApproved
{
    use Dispatchable, SerializesModels;

    public int $purchaseOrderId;
    public float $invoiceAmount;

    /**
     * Create a new event instance.
     *
     * @param int $purchaseOrderId
     * @param float $invoiceAmount
     */
    public function __construct(int $purchaseOrderId, float $invoiceAmount)
    {
        $this->purchaseOrderId = $purchaseOrderId;
        $this->invoiceAmount = $invoiceAmount;
    }
}