<?php

namespace Modules\Fina\Tests\Feature\CO\PC;

use Modules\Fina\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Fina\CO\PC\Domain\CostElement;

class CostElementControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_cost_element()
    {
        $data = [
            'name' => 'Test Cost Element',
            'type' => 'primary',
            'description' => 'This is a test cost element.',
        ];

        $response = $this->postJson('/api/fina/co/pc/cost-elements', $data);

        $response->assertStatus(201)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('fina_co_pc_cost_elements', $data);
    }

    public function test_can_get_cost_element()
    {
        $costElement = CostElement::factory()->create();

        $response = $this->getJson("/api/fina/co/pc/cost-elements/{$costElement->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => $costElement->name,
                'description' => $costElement->description,
            ]);
    }

    public function test_can_get_all_cost_elements()
    {
        CostElement::factory()->count(3)->create();

        $response = $this->getJson('/api/fina/co/pc/cost-elements');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_can_update_cost_element()
    {
        $costElement = CostElement::factory()->create();

        $data = [
            'name' => 'Updated Cost Element',
            'description' => 'This is an updated cost element.',
        ];

        $response = $this->putJson("/api/fina/co/pc/cost-elements/{$costElement->id}", $data);

        $response->assertStatus(200)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('fina_co_pc_cost_elements', $data);
    }

    public function test_can_delete_cost_element()
    {
        $costElement = CostElement::factory()->create();

        $response = $this->deleteJson("/api/fina/co/pc/cost-elements/{$costElement->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('fina_co_pc_cost_elements', ['id' => $costElement->id]);
    }
}