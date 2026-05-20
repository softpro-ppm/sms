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
        $response->assertSeeText('Reception Dashboard');
        $response->assertSeeText('Manage student intake and payment entries.');
        $response->assertSeeText('Document Completion Queue');
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
        $response->assertSeeText('Admin Dashboard');
        $response->assertSeeText('Manage approvals, enrollments, and daily operations.');
        $response->assertSeeText('Action Queues');
        $response->assertDontSeeText('Reception Workspace V3.0');
    }
}
