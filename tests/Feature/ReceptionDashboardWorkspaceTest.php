<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReceptionDashboardWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_reception_user_sees_reception_workspace_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'reception',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSeeText('Reception Workspace V3.0');
        $response->assertSeeText('Front-desk work should feel fast, focused, and clear.');
        $response->assertSeeText('Document completion queue');
        $response->assertSeeText('Register student');
    }

    public function test_admin_user_keeps_existing_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSeeText('Welcome back,');
        $response->assertSeeText('Quick Actions');
        $response->assertDontSeeText('Reception Workspace V3.0');
    }
}
