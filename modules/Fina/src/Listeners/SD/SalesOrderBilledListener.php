<?php

namespace Modules\Fina\Listeners\SD;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Fina\FI\AR\Application\ProcessCustomerInvoiceService;

class SalesOrderBilledListener implements ShouldQueue
{
    private $processCustomerInvoiceService;

    public function __construct(ProcessCustomerInvoiceService $processCustomerInvoiceService)
    {
        $this->processCustomerInvoiceService = $processCustomerInvoiceService;
    }

    public function handle($event)
    {
        // This is a simplified example. A real implementation would have more logic.
        // The event object ($event) would contain the sales order data.
        $data = [
            'company_code_id' => $event->company_code_id,
            'customer_id' => $event->customer_id,
            'invoice_date' => $event->billing_date,
            'due_date' => $event->due_date,
            'gross_amount' => $event->gross_amount,
            'net_amount' => $event->net_amount,
            'tax_amount' => $event->tax_amount,
            'currency' => $event->currency,
            'so_number' => $event->sales_order_number,
            'user_id' => $event->user_id,
            'gl_items' => $event->gl_items,
        ];

        ($this->processCustomerInvoiceService)($data);
    }
}
