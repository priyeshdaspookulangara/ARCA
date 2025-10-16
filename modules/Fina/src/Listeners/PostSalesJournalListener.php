<?php

namespace Modules\Fina\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\SD\Events\BillingGeneratedEvent;

class PostSalesJournalListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  BillingGeneratedEvent  $event
     * @return void
     */
    public function handle(BillingGeneratedEvent $event)
    {
        // This is where the integration magic happens.
        // The FINA module listens to an event from the SD module.

        // 1. Get the invoice details from the event payload.
        $invoice = $event->invoice;
        $customer = $invoice->salesOrder->customer;
        $netTotal = $invoice->total_amount; // Assuming this is net for simplicity
        $taxAmount = $netTotal * 0.1; // Example tax calculation
        $grossTotal = $netTotal + $taxAmount;

        // 2. Get the GL account mappings from the customer or a central mapping table.
        // $accountsReceivableAccount = $customer->gl_account;
        // $salesRevenueAccount = config('fina.gl_accounts.sales_revenue');
        // $taxAccount = config('fina.gl_accounts.tax');
        $accountsReceivableAccount = '120000'; // Placeholder
        $salesRevenueAccount = '400000'; // Placeholder
        $taxAccount = '220100'; // Placeholder

        // 3. Create the Journal Entry in the FINA module.
        // JournalEntry::create([
        //     'transaction_date' => $invoice->invoice_date,
        //     'description' => "Sales Invoice #{$invoice->id}",
        //     'entries' => [
        //         ['account_code' => $accountsReceivableAccount, 'debit' => $grossTotal, 'credit' => 0],
        //         ['account_code' => $salesRevenueAccount, 'debit' => 0, 'credit' => $netTotal],
        //         ['account_code' => $taxAccount, 'debit' => 0, 'credit' => $taxAmount],
        //     ]
        // ]);

        // For now, we'll just log that the listener was triggered.
        \Log::info("FINA Listener: BillingGeneratedEvent handled for Invoice #{$invoice->id}. Amount: {$grossTotal}");
    }
}