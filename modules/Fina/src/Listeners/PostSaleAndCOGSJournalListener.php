<?php

namespace Modules\Fina\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\POS\Events\SaleCompletedEvent;

class PostSaleAndCOGSJournalListener implements ShouldQueue
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
     * @param  SaleCompletedEvent  $event
     * @return void
     */
    public function handle(SaleCompletedEvent $event)
    {
        // This is where the integration magic happens.
        // The FINA module listens to an event from the POS module.

        // 1. Get the sale details from the event payload.
        $sale = $event->sale;
        $payment = $sale->payments->first(); // Assuming single payment for simplicity
        $netTotal = $sale->total_amount - $sale->tax_amount;
        $taxAmount = $sale->tax_amount;
        $costOfGoods = 0; // This would be calculated based on the cost of the items sold

        // 2. Get the GL account mappings from a central mapping table.
        // $cashAccount = config('fina.gl_accounts.cash');
        // $bankAccount = config('fina.gl_accounts.bank');
        // $salesRevenueAccount = config('fina.gl_accounts.sales_revenue');
        // $taxAccount = config('fina.gl_accounts.tax');
        // $cogsAccount = config('fina.gl_accounts.cogs');
        // $inventoryAccount = config('fina.gl_accounts.inventory');
        $cashAccount = '100000'; // Placeholder
        $bankAccount = '101000'; // Placeholder
        $salesRevenueAccount = '400000'; // Placeholder
        $taxAccount = '220100'; // Placeholder
        $cogsAccount = '500000'; // Placeholder
        $inventoryAccount = '140100'; // Placeholder

        // 3. Create the Journal Entry for the sale in the FINA module.
        // $debitAccount = $payment->payment_mode == 'Cash' ? $cashAccount : $bankAccount;
        // JournalEntry::create([
        //     'transaction_date' => $sale->created_at,
        //     'description' => "POS Sale #{$sale->id}",
        //     'entries' => [
        //         ['account_code' => $debitAccount, 'debit' => $sale->total_amount, 'credit' => 0],
        //         ['account_code' => $salesRevenueAccount, 'debit' => 0, 'credit' => $netTotal],
        //         ['account_code' => $taxAccount, 'debit' => 0, 'credit' => $taxAmount],
        //     ]
        // ]);

        // 4. Create the Journal Entry for the Cost of Goods Sold.
        // JournalEntry::create([
        //     'transaction_date' => $sale->created_at,
        //     'description' => "COGS for POS Sale #{$sale->id}",
        //     'entries' => [
        //         ['account_code' => $cogsAccount, 'debit' => $costOfGoods, 'credit' => 0],
        //         ['account_code' => $inventoryAccount, 'debit' => 0, 'credit' => $costOfGoods],
        //     ]
        // ]);


        // For now, we'll just log that the listener was triggered.
        \Log::info("FINA Listener: SaleCompletedEvent handled for Sale #{$sale->id}.");
    }
}