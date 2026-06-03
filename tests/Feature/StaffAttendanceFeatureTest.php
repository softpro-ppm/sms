<?php

namespace Tests\Feature;

use App\Models\StaffAttendance;
use App\Models\TrainingPartner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StaffAttendanceFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_enroll_face_and_record_attendance(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'role' => 'reception',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $image = $this->dataImage();

        $this->actingAs($user)
            ->post(route('admin.staff-attendance.enroll-face'), ['face_image' => $image])
            ->assertRedirect();

        $user->refresh();
        $this->assertNotNull($user->face_enrolled_at);
        Storage::disk('public')->assertExists($user->face_reference_image_path);

        $this->actingAs($user)
            ->post(route('admin.staff-attendance.check-in'), ['face_image' => $image])
            ->assertRedirect();

        $attendance = StaffAttendance::where('user_id', $user->id)->first();
        $this->assertNotNull($attendance);
        $this->assertNotNull($attendance->check_in_at);
        Storage::disk('public')->assertExists($attendance->check_in_image_path);

        $this->actingAs($user)
            ->post(route('admin.staff-attendance.check-out'), ['face_image' => $image])
            ->assertRedirect();

        $attendance->refresh();
        $this->assertNotNull($attendance->check_out_at);
        Storage::disk('public')->assertExists($attendance->check_out_image_path);
    }

    public function test_admin_attendance_index_is_scoped_to_training_partner(): void
    {
        $firstPartner = TrainingPartner::create([
            'type' => 'STANDARD',
            'name' => 'First Centre',
            'code' => 'FIRST',
            'status' => 'active',
        ]);

        $secondPartner = TrainingPartner::create([
            'type' => 'STANDARD',
            'name' => 'Second Centre',
            'code' => 'SECOND',
            'status' => 'active',
        ]);

        $admin = User::factory()->create([
            'name' => 'First Admin',
            'role' => 'admin',
            'training_partner_id' => $firstPartner->id,
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $firstStaff = User::factory()->create([
            'name' => 'Visible Staff',
            'role' => 'reception',
            'training_partner_id' => $firstPartner->id,
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $secondStaff = User::factory()->create([
            'name' => 'Hidden Staff',
            'role' => 'reception',
            'training_partner_id' => $secondPartner->id,
            'is_active' => true,
            'must_change_password' => false,
        ]);

        StaffAttendance::create([
            'user_id' => $firstStaff->id,
            'training_partner_id' => $firstPartner->id,
            'attendance_date' => now()->toDateString(),
            'check_in_at' => now(),
        ]);

        StaffAttendance::create([
            'user_id' => $secondStaff->id,
            'training_partner_id' => $secondPartner->id,
            'attendance_date' => now()->toDateString(),
            'check_in_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.staff-attendance.index'))
            ->assertOk()
            ->assertSeeText('Visible Staff')
            ->assertDontSeeText('Hidden Staff');
    }

    private function dataImage(): string
    {
        return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII=';
    }
}
