<?php

namespace Modules\AuthMgt\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Modules\AuthMgt\Application\Events\RoleAssigned;
use Modules\AuthMgt\Application\Events\RoleRevoked;
use Modules\AuthMgt\Domain\Entities\Role;
use Modules\AuthMgt\Domain\Entities\AuthUser;
use Tests\TestCase;

class RoleAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_role_can_be_assigned_to_a_user()
    {
        Event::fake();

        // Create a user
        $user = User::factory()->create();

        // Create a role
        $role = Role::create(['name' => 'Admin', 'description' => 'Administrator Role']);

        // Assign the role to the user
        $this->postJson('/api/authmgt/users/' . $user->id . '/roles', ['role_id' => $role->id])
             ->assertStatus(200)
             ->assertJson(['message' => 'Role assigned successfully.']);

        // Assert that the user has the role
        $authUser = AuthUser::where('user_id', $user->id)->first();
        $this->assertDatabaseHas('auth_user_role', [
            'auth_user_id' => $authUser->id,
            'role_id' => $role->id,
        ]);

        Event::assertDispatched(RoleAssigned::class, function ($event) use ($user, $role) {
            return $event->userId === $user->id && $event->roleId === $role->id;
        });
    }

    public function test_a_role_can_be_revoked_from_a_user()
    {
        Event::fake();

        // Create a user and role
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Admin']);
        $authUser = AuthUser::create(['user_id' => $user->id]);
        $authUser->roles()->attach($role->id);

        // Revoke the role from the user
        $this->deleteJson('/api/authmgt/users/' . $user->id . '/roles/' . $role->id)
             ->assertStatus(200)
             ->assertJson(['message' => 'Role revoked successfully.']);

        // Assert that the user no longer has the role
        $this->assertDatabaseMissing('auth_user_role', [
            'auth_user_id' => $authUser->id,
            'role_id' => $role->id,
        ]);

        Event::assertDispatched(RoleRevoked::class, function ($event) use ($user, $role) {
            return $event->userId === $user->id && $event->roleId === $role->id;
        });
    }
}