<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificatesQueuePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_certificates_queue_copy(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $response = $this->actingAs($user)->get(route('admin.certificates.index'));

        $response->assertOk();
        $response->assertSeeText('Certificates Queue');
        $response->assertSeeText('Issue, review, and track certificate records.');
        $response->assertSeeText('Queue Filters');
        $response->assertSeeText('Review pending and issued certificates');
    }
}
