<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminUserSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_seeder_creates_verified_administrator_with_admin_role(): void
    {
        $this->seed(PermissionSeeder::class);

        $this->seed(AdminUserSeeder::class);

        $admin = User::where('email', 'nysainana@dna.mg')->firstOrFail();
        $this->assertSame('Ny Sainana', $admin->name);
        $this->assertTrue($admin->hasRole(PermissionSeeder::ADMIN_ROLE));
        $this->assertNotNull($admin->email_verified_at);
        $this->assertTrue(Hash::check('password', $admin->password));
    }

    public function test_admin_seeder_is_idempotent(): void
    {
        $this->seed(PermissionSeeder::class);

        $this->seed(AdminUserSeeder::class);
        $this->seed(AdminUserSeeder::class);

        $this->assertSame(1, User::where('email', 'nysainana@dna.mg')->count());
        $this->assertSame(1, Role::where('name', PermissionSeeder::ADMIN_ROLE)->count());
    }
}
