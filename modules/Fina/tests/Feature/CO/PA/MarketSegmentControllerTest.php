<?php

namespace Modules\Fina\Tests\Feature\CO\PA;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Fina\CO\PA\Domain\Entities\MarketSegment;
use Tests\TestCase;

class MarketSegmentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_market_segments()
    {
        MarketSegment::factory()->count(3)->create();

        $response = $this->getJson('/api/fina/co/pa/market-segments');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_can_create_market_segment()
    {
        $data = [
            'name' => 'Test Segment',
            'description' => 'Test Description',
            'characteristics' => ['region' => 'test'],
        ];

        $response = $this->postJson('/api/fina/co/pa/market-segments', $data);

        $response->assertStatus(201)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('fina_co_pa_market_segments', $data);
    }

    public function test_can_get_market_segment_by_id()
    {
        $marketSegment = MarketSegment::factory()->create();

        $response = $this->getJson("/api/fina/co/pa/market-segments/{$marketSegment->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => $marketSegment->name]);
    }

    public function test_can_update_market_segment()
    {
        $marketSegment = MarketSegment::factory()->create();
        $data = ['name' => 'Updated Name'];

        $response = $this->putJson("/api/fina/co/pa/market-segments/{$marketSegment->id}", $data);

        $response->assertStatus(200)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('fina_co_pa_market_segments', ['id' => $marketSegment->id, 'name' => 'Updated Name']);
    }

    public function test_can_delete_market_segment()
    {
        $marketSegment = MarketSegment::factory()->create();

        $response = $this->deleteJson("/api/fina/co/pa/market-segments/{$marketSegment->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('fina_co_pa_market_segments', ['id' => $marketSegment->id]);
    }
}