<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResultsQueuePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_results_queue_copy(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $response = $this->actingAs($user)->get(route('admin.results.index'));

        $response->assertOk();
        $response->assertSeeText('Results Queue');
        $response->assertSeeText('Review pass, fail, and score trends from one results queue.');
        $response->assertSeeText('Queue Filters');
        $response->assertSeeText('Review completed assessments and pass status');
    }
}
