<?php

namespace Tests\Feature;

use App\Models\NotificationPreference;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentNotificationPreferencesTest extends TestCase
{
    use RefreshDatabase;

    private function makeStudentUser(): User
    {
        $student = Student::query()->create([
            'aadhar_number' => '123456789012',
            'full_name' => 'Notify Student',
            'email' => 'notify-student@example.com',
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

    public function test_student_can_view_notification_preferences(): void
    {
        $user = $this->makeStudentUser();

        $response = $this->actingAs($user)->get(route('student.notifications'));

        $response->assertOk()
            ->assertSee('Manage browser alerts')
            ->assertSee('Payment updates')
            ->assertSee('Fee reminders');
    }

    public function test_student_can_update_push_preferences(): void
    {
        $user = $this->makeStudentUser();

        $this->actingAs($user)->post(route('student.notifications.update'), [
            'push_enabled' => [
                'payment_confirmation' => '1',
                'certificate_issued' => '1',
            ],
        ])->assertRedirect(route('student.notifications'));

        $this->assertDatabaseHas('notification_preferences', [
            'user_id' => $user->id,
            'type' => 'payment_confirmation',
            'push_enabled' => 1,
        ]);

        $this->assertDatabaseHas('notification_preferences', [
            'user_id' => $user->id,
            'type' => 'payment_due',
            'push_enabled' => 0,
        ]);
    }
}
