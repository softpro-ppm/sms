<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ForgotPasswordAllRolesTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_request_password_reset_link(): void
    {
        Notification::fake();

        $student = Student::create([
            'aadhar_number' => '111122223333',
            'full_name' => 'Reset Student',
            'email' => 'reset.student@example.test',
            'phone' => '9876543210',
            'whatsapp_number' => '9876543210',
            'status' => 'approved',
        ]);

        $user = User::factory()->create([
            'name' => $student->full_name,
            'email' => $student->email,
            'role' => 'student',
            'student_id' => $student->id,
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $this->post(route('password.email'), [
            'email' => $student->email,
        ])->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);
    }
}
