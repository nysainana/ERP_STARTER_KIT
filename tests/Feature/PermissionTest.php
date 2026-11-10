<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\PermissionTree;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_every_permission_from_the_tree(): void
    {
        $this->seed(PermissionSeeder::class);

        foreach (PermissionTree::keys() as $key) {
            $this->assertDatabaseHas('permissions', ['name' => $key, 'guard_name' => 'web']);
        }
    }

    public function test_seeder_is_idempotent(): void
    {
        $this->seed(PermissionSeeder::class);
        $this->seed(PermissionSeeder::class);

        $this->assertSame(
            count(PermissionTree::keys()),
            Permission::count(),
        );
    }

    public function test_seeder_removes_permissions_that_left_the_tree(): void
    {
        $this->seed(PermissionSeeder::class);
        Permission::create(['name' => 'obsolete.permission', 'guard_name' => 'web']);

        $this->seed(PermissionSeeder::class);

        $this->assertDatabaseMissing('permissions', ['name' => 'obsolete.permission']);
    }

    public function test_administrateur_role_receives_every_permission(): void
    {
        $this->seed(PermissionSeeder::class);

        $admin = Role::findByName(PermissionSeeder::ADMIN_ROLE, 'web');

        $this->assertCount(count(PermissionTree::keys()), $admin->permissions);
    }

    public function test_routes_for_are_derived_from_granted_permissions(): void
    {
        $tree = [
            ['key' => 'g', 'label' => 'G', 'is_group' => true, 'children' => [
                ['key' => 'a.view', 'label' => 'A', 'routes' => ['a.index']],
                ['key' => 'b.view', 'label' => 'B', 'routes' => ['b.index']],
            ]],
        ];
        config(['permission-tree' => $tree]);

        $this->assertSame(['a.index'], PermissionTree::routesFor(['a.view']));
        $this->assertSame([], PermissionTree::routesFor(['unknown']));
    }

    public function test_user_granted_through_role_can_access_protected_route(): void
    {
        $this->seed(PermissionSeeder::class);
        $user = User::factory()->create();
        $user->assignRole(PermissionSeeder::ADMIN_ROLE);

        $this->actingAs($user)->get(route('dashboard'))->assertOk();
    }

    public function test_user_without_permission_is_forbidden_from_protected_route(): void
    {
        $this->seed(PermissionSeeder::class);
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('dashboard'))->assertForbidden();
    }

    public function test_permissions_are_shared_with_inertia(): void
    {
        $this->seed(PermissionSeeder::class);
        $user = User::factory()->create();
        $user->givePermissionTo(['dashboard.view', 'users.view']);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertInertia(fn ($page) => $page
                ->where('auth.permissions', ['dashboard.view', 'users.view'])
                ->where('auth.roles', [])
            );
    }

    public function test_middleware_ignores_routes_without_name(): void
    {
        Route::get('/unnamed-permission-probe', fn () => 'ok')->middleware('permission.auto');

        $this->get('/unnamed-permission-probe')->assertOk();
    }
}
