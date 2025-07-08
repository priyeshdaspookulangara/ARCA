<?php

namespace Modules\HR\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\HR\TimeManagement\Domain\Entities\LeaveType;
use Tests\TestCase; // Adjust if your base TestCase is different

class LeaveTypeApiTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_can_get_all_leave_types()
    {
        LeaveType::factory()->count(3)->create();
        $response = $this->getJson('/api/hr/leave-types');
        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    public function test_can_get_only_active_leave_types()
    {
        LeaveType::factory()->active()->count(2)->create();
        LeaveType::factory()->inactive()->count(1)->create();

        $response = $this->getJson('/api/hr/leave-types?is_active=true');
        $response->assertStatus(200)
                 ->assertJsonCount(2)
                 ->assertJsonFragment(['is_active' => true])
                 ->assertJsonMissing(['is_active' => false]);
    }


    public function test_can_create_leave_type()
    {
        $data = [
            'name' => $this->faker->unique()->word . ' Leave',
            'description' => $this->faker->sentence,
            'is_paid' => $this->faker->boolean,
            'default_entitlement_days' => $this->faker->optional()->numberBetween(5, 25),
            'is_active' => true,
        ];

        $response = $this->postJson('/api/hr/leave-types', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => $data['name']]);
        $this->assertDatabaseHas('hr_leave_types', ['name' => $data['name']]);
    }

    public function test_create_leave_type_fails_with_duplicate_name()
    {
        $existing = LeaveType::factory()->create();
        $data = ['name' => $existing->name];
        $response = $this->postJson('/api/hr/leave-types', $data);
        $response->assertStatus(422)->assertJsonValidationErrors(['name']);
    }

    public function test_can_get_a_specific_leave_type()
    {
        $leaveType = LeaveType::factory()->create();
        $response = $this->getJson("/api/hr/leave-types/{$leaveType->id}");
        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => $leaveType->name]);
    }

    public function test_can_update_leave_type()
    {
        $leaveType = LeaveType::factory()->create();
        $updatedData = [
            'name' => 'Updated ' . $leaveType->name,
            'description' => 'Updated description.',
            'is_paid' => !$leaveType->is_paid,
            'is_active' => false,
        ];

        $response = $this->putJson("/api/hr/leave-types/{$leaveType->id}", $updatedData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => $updatedData['name'], 'is_active' => false]);
        $this->assertDatabaseHas('hr_leave_types', ['id' => $leaveType->id, 'name' => $updatedData['name']]);
    }

    public function test_can_delete_leave_type()
    {
        $leaveType = LeaveType::factory()->create();
        $response = $this->deleteJson("/api/hr/leave-types/{$leaveType->id}");
        $response->assertStatus(204);
        $this->assertSoftDeleted('hr_leave_types', ['id' => $leaveType->id]);
    }

    // Add test for attempting to delete a leave type that is in use by leave requests
    // This test would need the LeaveRequest model and factory, and the relationship defined.
    // For now, it's commented out.
    /*
    public function test_cannot_delete_leave_type_if_in_use()
    {
        $leaveType = LeaveType::factory()->create();
        // \Modules\HR\TimeManagement\Domain\Entities\LeaveRequest::factory()->ofType($leaveType)->create();

        $response = $this->deleteJson("/api/hr/leave-types/{$leaveType->id}");

        // Assuming the controller implements this check and returns 422
        // $response->assertStatus(422)
        //          ->assertJsonFragment(['error' => 'Cannot delete leave type with associated leave requests. Consider deactivating it instead.']);
        $this->markTestIncomplete('LeaveRequest model and its usage check in controller destroy method needs to be implemented.');
    }
    */
}
