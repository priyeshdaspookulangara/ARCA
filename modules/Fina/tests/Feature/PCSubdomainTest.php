<?php

namespace Modules\Fina\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Fina\Tests\TestCase;

class PCSubdomainTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_material_cost()
    {
        $response = $this->postJson('/api/fina/pc/material-costs', [
            'material_id' => 1,
            'costing_variant' => 'test',
            'cost' => 100,
            'currency' => 'USD',
        ]);

        $response->assertStatus(201);
    }

    public function test_create_inventory_valuation()
    {
        $response = $this->postJson('/api/fina/pc/inventory-valuations', [
            'material_id' => 1,
            'plant_id' => 1,
            'quantity' => 10,
            'value' => 1000,
            'currency' => 'USD',
        ]);

        $response->assertStatus(201);
    }

    public function test_create_cost_object_controlling()
    {
        $response = $this->postJson('/api/fina/pc/cost-object-controlling', [
            'cost_object' => 'test',
            'cost_object_type' => 'test',
            'planned_costs' => 1000,
            'actual_costs' => 1200,
            'variance' => 200,
            'currency' => 'USD',
        ]);

        $response->assertStatus(201);
    }
}
