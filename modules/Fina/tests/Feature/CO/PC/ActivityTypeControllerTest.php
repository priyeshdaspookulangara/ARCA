<?php

namespace Modules\Fina\Tests\Feature\CO\PC;

use Modules\Fina\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Fina\CO\PC\Domain\ActivityType;

class ActivityTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_activity_type()
    {
        $data = [
            'name' => 'Test Activity Type',
            'unit' => 'hours',
            'description' => 'This is a test activity type.',
        ];

        $response = $this->postJson('/api/fina/co/pc/activity-types', $data);

        $response->assertStatus(201)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('fina_co_pc_activity_types', $data);
    }

    public function test_can_get_activity_type()
    {
        $activityType = ActivityType::factory()->create();

        $response = $this->getJson("/api/fina/co/pc/activity-types/{$activityType->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => $activityType->name,
                'description' => $activityType->description,
            ]);
    }

    public function test_can_get_all_activity_types()
    {
        ActivityType::factory()->count(3)->create();

        $response = $this->getJson('/api/fina/co/pc/activity-types');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_can_update_activity_type()
    {
        $activityType = ActivityType::factory()->create();

        $data = [
            'name' => 'Updated Activity Type',
            'description' => 'This is an updated activity type.',
        ];

        $response = $this->putJson("/api/fina/co/pc/activity-types/{$activityType->id}", $data);

        $response->assertStatus(200)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('fina_co_pc_activity_types', $data);
    }

    public function test_can_delete_activity_type()
    {
        $activityType = ActivityType::factory()->create();

        $response = $this->deleteJson("/api/fina/co/pc/activity-types/{$activityType->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('fina_co_pc_activity_types', ['id' => $activityType->id]);
    }
}