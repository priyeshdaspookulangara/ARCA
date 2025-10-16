<?php

namespace Modules\MM\Valuation\Application\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\MM\InventoryManagement\Domain\Models\GoodsIssue;

class GoodsIssued
{
    use Dispatchable, SerializesModels;

    public GoodsIssue $goodsIssue;

    /**
     * Create a new event instance.
     *
     * @param GoodsIssue $goodsIssue
     */
    public function __construct(GoodsIssue $goodsIssue)
    {
        $this->goodsIssue = $goodsIssue;
    }
}