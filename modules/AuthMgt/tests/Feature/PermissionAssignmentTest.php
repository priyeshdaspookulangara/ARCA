<?php

namespace Modules\AuthMgt\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Modules\AuthMgt\Application\Events\PermissionUpdated;
use Modules\AuthMgt\Domain\Entities\Role;
use Modules\AuthMgt\Domain\Entities\AuthObject;
use Tests\TestCase;

class PermissionAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_permission_can_be_assigned_to_a_role()
    {
        Event::fake();

        // Create a role and auth object
        $role = Role::create(['name' => 'Editor']);
        $object = AuthObject::create([
            'code' => 'EDIT_POST',
            'module' => 'Blog',
            'actions' => json_encode(['create', 'read', 'update']),
        ]);

        // Assign the permission to the role
        $this->postJson('/api/authmgt/permissions/assign', [
            'role_id' => $role->id,
            'auth_object_id' => $object->id,
            'actions' => ['update'],
        ])
             ->assertStatus(200)
             ->assertJson(['message' => 'Permission assigned successfully.']);

        $this->assertDatabaseHas('permissions', [
            'role_id' => $role->id,
            'auth_object_id' => $object->id,
        ]);

        Event::assertDispatched(PermissionUpdated::class, function ($event) use ($role) {
            return $event->roleId === $role->id;
        });
    }

    public function test_a_permission_can_be_revoked_from_a_role()
    {
        Event::fake();

        // Create a role and auth object, and assign the permission
        $role = Role::create(['name' => 'Editor']);
        $object = AuthObject::create([
            'code' => 'EDIT_POST',
            'module' => 'Blog',
            'actions' => json_encode(['create', 'read', 'update']),
        ]);
        $role->permissions()->attach($object->id, ['actions' => json_encode(['update'])]);

        // Revoke the permission
        $this->postJson('/api/authmgt/permissions/revoke', [
            'role_id' => $role->id,
            'auth_object_id' => $object->id,
        ])
             ->assertStatus(200)
             ->assertJson(['message' => 'Permission revoked successfully.']);

        $this->assertDatabaseMissing('permissions', [
            'role_id' => $role->id,
            'auth_object_id' => $object->id,
        ]);

        Event::assertDispatched(PermissionUpdated::class, function ($event) use ($role) {
            return $event->roleId === $role->id;
        });
    }
}