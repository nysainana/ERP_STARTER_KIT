<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function userWith(string ...$permissions): User
    {
        $user = User::factory()->create();
        $user->givePermissionTo($permissions);

        return $user;
    }

    public function test_user_without_users_view_is_forbidden(): void
    {
        $this->seed(PermissionSeeder::class);

        $this->actingAs(User::factory()->create())
            ->get(route('users.index'))
            ->assertForbidden();
    }

    public function test_user_with_users_view_sees_the_list(): void
    {
        $this->seed(PermissionSeeder::class);
        User::factory()->create(['name' => 'Alice Martin']);

        $this->actingAs($this->userWith('users.view'))
            ->get(route('users.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('users/index')
                ->has('users.data')
                ->has('roles')
            );
    }

    public function test_user_with_users_view_cannot_create_users(): void
    {
        $this->seed(PermissionSeeder::class);

        $this->actingAs($this->userWith('users.view'))
            ->post(route('users.store'), [
                'name' => 'Bob',
                'email' => 'bob@example.com',
                'password' => 'Secret-pass-123',
                'password_confirmation' => 'Secret-pass-123',
                'roles' => [],
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'bob@example.com']);
    }

    public function test_user_can_be_created_with_roles(): void
    {
        $this->seed(PermissionSeeder::class);
        $role = Role::create(['name' => 'Commercial', 'guard_name' => 'web']);

        $this->actingAs($this->userWith('users.view', 'users.manage'))
            ->post(route('users.store'), [
                'name' => 'Bob',
                'email' => 'bob@example.com',
                'password' => 'Secret-pass-123',
                'password_confirmation' => 'Secret-pass-123',
                'role_id' => $role->id,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('users.index'));

        $bob = User::where('email', 'bob@example.com')->firstOrFail();
        $this->assertSame(['Commercial'], $bob->roles->pluck('name')->all());
        $this->assertNotNull($bob->email_verified_at);
    }

    public function test_duplicate_email_is_rejected_on_create(): void
    {
        $this->seed(PermissionSeeder::class);
        User::factory()->create(['email' => 'taken@example.com']);

        $this->actingAs($this->userWith('users.view', 'users.manage'))
            ->post(route('users.store'), [
                'name' => 'Bob',
                'email' => 'taken@example.com',
                'password' => 'Secret-pass-123',
                'password_confirmation' => 'Secret-pass-123',
                'role_id' => Role::create(['name' => 'Commercial', 'guard_name' => 'web'])->id,
            ])
            ->assertSessionHasErrors('email');
    }

    public function test_user_can_be_updated_without_changing_password(): void
    {
        $this->seed(PermissionSeeder::class);
        $target = User::factory()->create(['name' => 'Ancien']);
        $originalHash = $target->password;

        $this->actingAs($this->userWith('users.view', 'users.manage'))
            ->patch(route('users.update', $target), [
                'name' => 'Nouveau',
                'email' => $target->email,
                'password' => '',
                'password_confirmation' => '',
                'role_id' => Role::create(['name' => 'Commercial', 'guard_name' => 'web'])->id,
            ])
            ->assertSessionHasNoErrors();

        $target->refresh();
        $this->assertSame('Nouveau', $target->name);
        $this->assertSame($originalHash, $target->password);
    }

    public function test_updating_role_replaces_the_previous_one(): void
    {
        $this->seed(PermissionSeeder::class);
        $old = Role::create(['name' => 'Ancien', 'guard_name' => 'web']);
        $new = Role::create(['name' => 'Nouveau', 'guard_name' => 'web']);
        $target = User::factory()->create();
        $target->assignRole($old);

        $this->actingAs($this->userWith('users.view', 'users.manage'))
            ->patch(route('users.update', $target), [
                'name' => $target->name,
                'email' => $target->email,
                'password' => '',
                'password_confirmation' => '',
                'role_id' => $new->id,
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(['Nouveau'], $target->fresh()->roles->pluck('name')->all());
    }

    public function test_user_cannot_delete_their_own_account(): void
    {
        $this->seed(PermissionSeeder::class);
        $manager = $this->userWith('users.view', 'users.manage');

        $this->actingAs($manager)
            ->delete(route('users.destroy', $manager))
            ->assertStatus(422);

        $this->assertNotNull($manager->fresh());
    }

    public function test_other_user_can_be_deleted(): void
    {
        $this->seed(PermissionSeeder::class);
        $manager = $this->userWith('users.view', 'users.manage');
        $target = User::factory()->create();

        $this->actingAs($manager)
            ->delete(route('users.destroy', $target))
            ->assertRedirect(route('users.index'));

        $this->assertNull(User::find($target->id));
    }
}
