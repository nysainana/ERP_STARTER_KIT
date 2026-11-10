<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\PermissionTree;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    private function userWith(string ...$permissions): User
    {
        $user = User::factory()->create();
        $user->givePermissionTo($permissions);

        return $user;
    }

    public function test_guests_cannot_access_roles(): void
    {
        $this->seed(PermissionSeeder::class);

        $this->get(route('roles.index'))->assertRedirect(route('login'));
    }

    public function test_user_without_roles_permission_is_forbidden(): void
    {
        $this->seed(PermissionSeeder::class);

        $this->actingAs(User::factory()->create())
            ->get(route('roles.index'))
            ->assertForbidden();
    }

    public function test_user_with_roles_view_sees_tree_and_roles(): void
    {
        $this->seed(PermissionSeeder::class);
        $user = $this->userWith('roles.view');

        $this->actingAs($user)
            ->get(route('roles.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('roles/index')
                ->has('tree', count(PermissionTree::tree()))
                ->has('roles')
            );
    }

    public function test_user_with_roles_view_cannot_update_roles(): void
    {
        $this->seed(PermissionSeeder::class);
        $role = Role::create(['name' => 'Commercial', 'guard_name' => 'web']);

        $this->actingAs($this->userWith('roles.view'))
            ->patch(route('roles.update', $role), ['permissions' => ['dashboard.view']])
            ->assertForbidden();
    }

    public function test_user_with_roles_manage_can_update_role_permissions(): void
    {
        $this->seed(PermissionSeeder::class);
        $role = Role::create(['name' => 'Commercial', 'guard_name' => 'web']);

        $this->actingAs($this->userWith('roles.view', 'roles.manage'))
            ->patch(route('roles.update', $role), ['permissions' => ['dashboard.view', 'users.view']])
            ->assertRedirect(route('roles.index'));

        $this->assertEqualsCanonicalizing(
            ['dashboard.view', 'users.view'],
            $role->fresh()->permissions->pluck('name')->all(),
        );
    }

    public function test_unknown_permission_keys_are_rejected(): void
    {
        $this->seed(PermissionSeeder::class);
        $role = Role::create(['name' => 'Commercial', 'guard_name' => 'web']);

        $this->actingAs($this->userWith('roles.view', 'roles.manage'))
            ->patch(route('roles.update', $role), ['permissions' => ['hacked.everything']])
            ->assertSessionHasErrors('permissions.0');

        $this->assertCount(0, $role->fresh()->permissions);
    }

    public function test_permissions_key_is_required(): void
    {
        $this->seed(PermissionSeeder::class);
        $role = Role::create(['name' => 'Commercial', 'guard_name' => 'web']);

        $this->actingAs($this->userWith('roles.view', 'roles.manage'))
            ->patch(route('roles.update', $role), [])
            ->assertSessionHasErrors('permissions');
    }

    public function test_administrateur_cannot_lose_permissions(): void
    {
        $this->seed(PermissionSeeder::class);
        $admin = Role::findByName(PermissionSeeder::ADMIN_ROLE, 'web');

        $this->actingAs($this->userWith('roles.view', 'roles.manage'))
            ->patch(route('roles.update', $admin), ['permissions' => ['dashboard.view']])
            ->assertSessionHasErrors('permissions');

        $this->assertCount(count(PermissionTree::keys()), $admin->fresh()->permissions);
    }

    public function test_user_cannot_remove_roles_manage_from_their_own_role(): void
    {
        $this->seed(PermissionSeeder::class);
        $ownRole = Role::create(['name' => 'Gestionnaire', 'guard_name' => 'web']);
        $ownRole->givePermissionTo(['roles.view', 'roles.manage']);

        $manager = User::factory()->create();
        $manager->assignRole($ownRole);

        $this->actingAs($manager)
            ->patch(route('roles.update', $ownRole), ['permissions' => ['roles.view']])
            ->assertSessionHasErrors('permissions');

        $this->assertTrue($ownRole->fresh()->hasPermissionTo('roles.manage'));
    }

    public function test_user_can_remove_roles_manage_from_another_role(): void
    {
        $this->seed(PermissionSeeder::class);
        $manager = $this->userWith('roles.view', 'roles.manage');
        $other = Role::create(['name' => 'Commercial', 'guard_name' => 'web']);
        $other->givePermissionTo('roles.manage');

        $this->actingAs($manager)
            ->patch(route('roles.update', $other), ['permissions' => ['roles.view']])
            ->assertSessionHasNoErrors();

        $this->assertFalse($other->fresh()->hasPermissionTo('roles.manage'));
    }

    public function test_role_can_be_created_with_permissions(): void
    {
        $this->seed(PermissionSeeder::class);
        $manager = $this->userWith('roles.view', 'roles.manage');

        $this->actingAs($manager)
            ->post(route('roles.store'), [
                'name' => 'Comptable',
                'permissions' => ['roles.view'],
            ])
            ->assertSessionHasNoErrors();

        $role = Role::findByName('Comptable', 'web');
        $this->assertSame(['roles.view'], $role->permissions->pluck('name')->all());
    }

    public function test_administrateur_name_cannot_be_reused_on_create(): void
    {
        $this->seed(PermissionSeeder::class);
        $manager = $this->userWith('roles.view', 'roles.manage');

        $this->actingAs($manager)
            ->post(route('roles.store'), [
                'name' => PermissionSeeder::ADMIN_ROLE,
                'permissions' => [],
            ])
            ->assertSessionHasErrors('name');
    }

    public function test_unused_role_can_be_deleted(): void
    {
        $this->seed(PermissionSeeder::class);
        $manager = $this->userWith('roles.view', 'roles.manage');
        $role = Role::create(['name' => 'Temporaire', 'guard_name' => 'web']);

        $this->actingAs($manager)
            ->delete(route('roles.destroy', $role))
            ->assertSessionHasNoErrors();

        $this->assertNull(Role::where('name', 'Temporaire')->first());
    }

    public function test_role_assigned_to_users_cannot_be_deleted(): void
    {
        $this->seed(PermissionSeeder::class);
        $manager = $this->userWith('roles.view', 'roles.manage');
        $role = Role::create(['name' => 'Utilisé', 'guard_name' => 'web']);
        User::factory()->create()->assignRole($role);

        $this->actingAs($manager)
            ->delete(route('roles.destroy', $role))
            ->assertStatus(422);

        $this->assertNotNull(Role::where('name', 'Utilisé')->first());
    }

    public function test_administrateur_cannot_be_deleted(): void
    {
        $this->seed(PermissionSeeder::class);
        $manager = $this->userWith('roles.view', 'roles.manage');
        $admin = Role::findByName(PermissionSeeder::ADMIN_ROLE, 'web');

        $this->actingAs($manager)
            ->delete(route('roles.destroy', $admin))
            ->assertStatus(422);

        $this->assertNotNull(Role::findByName(PermissionSeeder::ADMIN_ROLE, 'web'));
    }

    public function test_administrateur_can_be_resubmitted_with_all_permissions(): void
    {
        $this->seed(PermissionSeeder::class);
        $admin = Role::findByName(PermissionSeeder::ADMIN_ROLE, 'web');

        $this->actingAs($this->userWith('roles.view', 'roles.manage'))
            ->patch(route('roles.update', $admin), ['permissions' => PermissionTree::keys()])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('roles.index'));
    }
}
