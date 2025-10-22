<?php

namespace Modules\POS\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\POS\Tests\TestCase;
use Modules\POS\Models\ProductCache;
use Illuminate\Support\Facades\Http;

class OfflineSaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_sale_offline()
    {
        $product = ProductCache::factory()->create();

        Http::fake([
            config('pos.rth_endpoint') => Http::response(null, 500),
        ]);

        $response = $this->postJson('/api/pos/sales', [
            'product_id' => $product->product_id,
            'user_id' => 'USER-001',
            'shift_id' => 'SHIFT-001',
        ]);

        $response->assertStatus(202)
            ->assertJson([
                'message' => 'Sale stored offline successfully',
            ]);
    }
}
