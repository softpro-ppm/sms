<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentPwaEndpointsTest extends TestCase
{
    use RefreshDatabase;

    private function makeStudentUser(): User
    {
        $student = Student::query()->create([
            'aadhar_number' => '123456789012',
            'full_name' => 'PWA Student',
            'email' => 'pwa-student@example.com',
            'phone' => '9876543210',
            'whatsapp_number' => '9876543210',
            'status' => 'approved',
            'is_active' => true,
        ]);

        return User::factory()->create([
            'name' => $student->full_name,
            'email' => $student->email,
            'role' => 'student',
            'student_id' => $student->id,
            'is_active' => true,
            'must_change_password' => false,
        ]);
    }

    public function test_student_pwa_config_returns_push_state(): void
    {
        config()->set('services.webpush.public_key', 'public-demo-key');
        config()->set('services.webpush.private_key', 'private-demo-key');
        config()->set('services.webpush.subject', 'mailto:test@example.com');

        $user = $this->makeStudentUser();

        $response = $this->actingAs($user)->getJson(route('student.pwa.config'));

        $response->assertOk()
            ->assertJson([
                'pushEnabled' => true,
                'publicKey' => 'public-demo-key',
                'subscribed' => false,
            ]);
    }

    public function test_student_can_store_push_subscription(): void
    {
        $user = $this->makeStudentUser();

        $payload = [
            'endpoint' => 'https://example.push/123',
            'keys' => [
                'p256dh' => 'demo-public',
                'auth' => 'demo-auth',
            ],
            'contentEncoding' => 'aes128gcm',
        ];

        $this->actingAs($user)
            ->postJson(route('student.pwa.subscriptions.store'), $payload)
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertDatabaseHas('student_push_subscriptions', [
            'student_id' => $user->student_id,
            'endpoint' => 'https://example.push/123',
            'enabled' => 1,
        ]);
    }
}
