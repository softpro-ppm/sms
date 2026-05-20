<?php

namespace Tests\Feature;

use App\Models\TrainingPartner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminDashboardGovernanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_sees_governance_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        TrainingPartner::create([
            'name' => 'Pending Centre',
            'code' => 'TPCP1',
            'status' => 'pending',
            'type' => 'STANDARD',
            'wallet_balance' => 0,
        ]);

        TrainingPartner::create([
            'name' => 'Low Wallet Centre',
            'code' => 'TPCLW1',
            'status' => 'active',
            'type' => 'STANDARD',
            'wallet_balance' => 100,
        ]);

        $response = $this->actingAs($user)->get(route('admin.super.dashboard'));

        $response->assertOk();
        $response->assertSeeText('Super Admin Dashboard');
        $response->assertSeeText('Manage partner approvals, wallet risk, and platform exceptions.');
        $response->assertSeeText('Governance Queues');
        $response->assertSeeText('Pending Centre Approvals');
        $response->assertSeeText('Low Wallet Centres');
    }
}
