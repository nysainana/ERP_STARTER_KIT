<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SingleRoleCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_keeps_the_first_role_only(): void
    {
        $first = Role::create(['name' => 'Premier', 'guard_name' => 'web']);
        $second = Role::create(['name' => 'Second', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->syncRoles([$first, $second]);

        $this->artisan('users:single-role')->assertSuccessful();

        $this->assertSame(['Premier'], $user->fresh()->roles->pluck('name')->all());
    }

    public function test_dry_run_changes_nothing(): void
    {
        $first = Role::create(['name' => 'Premier', 'guard_name' => 'web']);
        $second = Role::create(['name' => 'Second', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->syncRoles([$first, $second]);

        $this->artisan('users:single-role', ['--dry-run' => true])->assertSuccessful();

        $this->assertCount(2, $user->fresh()->roles);
    }

    public function test_users_with_one_role_are_left_untouched(): void
    {
        $role = Role::create(['name' => 'Seul', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole($role);

        $this->artisan('users:single-role')->assertSuccessful();

        $this->assertSame(['Seul'], $user->fresh()->roles->pluck('name')->all());
    }
}
