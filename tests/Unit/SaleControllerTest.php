<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Modules\POS\Http\Controllers\SaleController;
use Illuminate\Http\Request;

class SaleControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_store_sale_offline()
    {
        $controller = $this->getMockBuilder(SaleController::class)
            ->onlyMethods(['isNetworkAvailable'])
            ->getMock();

        $controller->method('isNetworkAvailable')->willReturn(false);

        $request = new Request([
            'payload' => json_encode(['item' => 'test', 'price' => 100]),
        ]);

        $response = $controller->store($request);

        $this->assertEquals(202, $response->getStatusCode());
        $this->assertStringContainsString('Sale stored offline successfully', $response->getContent());
    }
}
