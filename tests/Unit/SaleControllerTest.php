<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Modules\POS\Http\Controllers\SaleController;
use Illuminate\Http\Request;
use Modules\POS\Models\ProductCache;

class SaleControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_store_sale_offline()
    {
        ProductCache::create([
            'product_id' => 'PROD-001',
            'name' => 'Test Product',
            'price' => 100,
            'tax' => 10,
            'last_updated' => now(),
        ]);

        $controller = $this->getMockBuilder(SaleController::class)
            ->onlyMethods(['isNetworkAvailable'])
            ->getMock();

        $controller->method('isNetworkAvailable')->willReturn(false);

        $request = new Request([
            'product_id' => 'PROD-001',
            'user_id' => 'USER-001',
            'shift_id' => 'SHIFT-001',
        ]);

        $response = $controller->store($request);

        $this->assertEquals(202, $response->getStatusCode());
        $this->assertStringContainsString('Sale stored offline successfully', $response->getContent());
    }
}
