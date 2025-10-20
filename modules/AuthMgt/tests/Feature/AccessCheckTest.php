<?php

namespace Modules\AuthMgt\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\AuthMgt\Domain\Entities\AuthObject;
use Modules\AuthMgt\Domain\Entities\Role;
use Modules\AuthMgt\Application\Services\AuthServiceInterface;
use Tests\TestCase;

class AccessCheckTest extends TestCase
{
    use RefreshDatabase;

    protected $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = $this->app->make(AuthServiceInterface::class);
    }

    public function test_access_is_granted_for_user_with_correct_permission()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a role and auth object
        $role = Role::create(['name' => 'Editor']);
        $object = AuthObject::create([
            'code' => 'EDIT_POST',
            'module' => 'Blog',
            'actions' => ['create', 'read', 'update'],
        ]);

        // Assign permission to role
        $role->permissions()->attach($object->id, ['actions' => json_encode(['update'])]);

        // Assign role to user
        $this->authService->assignRoleToUser($user->id, $role->id);

        // Check access
        $this->actingAs($user)
             ->postJson('/api/authmgt/permissions/check', [
                 'user_id' => $user->id,
                 'object_code' => 'EDIT_POST',
                 'action' => 'update',
             ])
             ->assertStatus(200)
             ->assertJson(['authorized' => true]);
    }

    public function test_access_is_denied_for_user_without_permission()
    {
        // Create a user
        $user = User::factory()->create();

        // Check access for a permission the user doesn't have
        $this->actingAs($user)
             ->postJson('/api/authmgt/permissions/check', [
                 'user_id' => $user->id,
                 'object_code' => 'EDIT_POST',
                 'action' => 'update',
             ])
             ->assertStatus(200)
             ->assertJson(['authorized' => false]);
    }

    public function test_middleware_denies_access_for_user_without_permission()
    {
        // Create a user
        $user = User::factory()->create();

        // Define a route protected by the middleware
        \Illuminate\Support\Facades\Route::get('/protected-route', function () {
            return response('Success', 200);
        })->middleware('check.auth.permission:EDIT_POST,update');

        // Attempt to access the route
        $this->actingAs($user)
             ->get('/protected-route')
             ->assertStatus(403);
    }

    public function test_middleware_grants_access_for_user_with_permission()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a role and auth object
        $role = Role::create(['name' => 'Editor']);
        $object = AuthObject::create([
            'code' => 'EDIT_POST',
            'module' => 'Blog',
            'actions' => json_encode(['create', 'read', 'update']),
        ]);

        // Assign permission to role
        $role->permissions()->attach($object->id, ['actions' => json_encode(['update'])]);

        // Assign role to user
        $this->authService->assignRoleToUser($user->id, $role->id);

        // Define a route protected by the middleware
        \Illuminate\Support\Facades\Route::get('/protected-route', function () {
            return response('Success', 200);
        })->middleware('check.auth.permission:EDIT_POST,update');

        // Attempt to access the route
        $this->actingAs($user)
             ->get('/protected-route')
             ->assertStatus(200);
    }
}