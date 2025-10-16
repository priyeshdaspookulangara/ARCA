<?php

namespace Modules\Fina\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\MM\Valuation\Application\Events\GoodsReceived;

class PostGoodsReceivedJournal implements ShouldQueue
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
     * @param  GoodsReceived  $event
     * @return void
     */
    public function handle(GoodsReceived $event)
    {
        // This is where the integration magic happens.
        // The FINA module listens to an event from the MM module.

        // 1. Get the goods receipt details from the event payload.
        $goodsReceipt = $event->goodsReceipt;
        $totalValue = 0;

        // 2. Calculate the total value of the received goods.
        // This requires accessing the cost from the purchase order or material master.
        // For this example, we'll assume a placeholder value.
        foreach ($goodsReceipt->items as $item) {
            // In a real scenario, you'd fetch the purchase price for each material.
            // $cost = $item->material->purchase_price;
            // $totalValue += $item->quantity_received * $cost;
        }
        $totalValue = 1000; // Placeholder value

        // 3. Get the GL account mappings from the material or a central mapping table.
        // $inventoryAccount = $goodsReceipt->items->first()->material->inventory_account;
        // $grniAccount = config('fina.gl_accounts.grni');
        $inventoryAccount = '140100'; // Placeholder
        $grniAccount = '210500'; // Placeholder

        // 4. Create the Journal Entry in the FINA module.
        // JournalEntry::create([
        //     'transaction_date' => $goodsReceipt->receipt_date,
        //     'description' => "Goods Receipt #{$goodsReceipt->id}",
        //     'entries' => [
        //         ['account_code' => $inventoryAccount, 'debit' => $totalValue, 'credit' => 0],
        //         ['account_code' => $grniAccount, 'debit' => 0, 'credit' => $totalValue],
        //     ]
        // ]);

        // For now, we'll just log that the listener was triggered.
        \Log::info("FINA Listener: GoodsReceived event handled for GR #{$goodsReceipt->id}. Value: {$totalValue}. Accounts: {$inventoryAccount}/{$grniAccount}");
    }
}