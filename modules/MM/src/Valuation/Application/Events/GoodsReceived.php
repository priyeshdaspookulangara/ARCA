<?php

namespace Modules\MM\Valuation\Application\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\MM\InventoryManagement\Domain\Models\GoodsReceipt;

class GoodsReceived
{
    use Dispatchable, SerializesModels;

    public GoodsReceipt $goodsReceipt;

    /**
     * Create a new event instance.
     *
     * @param GoodsReceipt $goodsReceipt
     */
    public function __construct(GoodsReceipt $goodsReceipt)
    {
        $this->goodsReceipt = $goodsReceipt;
    }
}