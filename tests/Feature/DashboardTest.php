<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page()
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_visit_the_dashboard()
    {
        $this->seed(PermissionSeeder::class);
        $user = User::factory()->create();
        $user->givePermissionTo('dashboard.view');
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertOk();
    }

    public function test_users_without_dashboard_permission_get_403()
    {
        $this->seed(PermissionSeeder::class);
        $this->actingAs(User::factory()->create());

        $this->get(route('dashboard'))->assertForbidden();
    }
}
