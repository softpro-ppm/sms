<?php

namespace Tests\Feature;

use App\Models\StaffAttendance;
use App\Models\StaffMember;
use App\Models\StaffMemberAttendance;
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

    public function test_reception_can_register_staff_and_admin_can_approve(): void
    {
        Storage::fake('public');

        $partner = TrainingPartner::create([
            'type' => 'STANDARD',
            'name' => 'Attendance Centre',
            'code' => 'ATT',
            'status' => 'active',
        ]);

        $reception = User::factory()->create([
            'role' => 'reception',
            'training_partner_id' => $partner->id,
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
            'training_partner_id' => $partner->id,
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $payload = [
            'staff_code' => 'EMP-001',
            'name' => 'Kiosk Staff',
            'phone' => '9000000001',
            'designation' => 'Trainer',
            'face_descriptors' => json_encode([
                array_fill(0, 128, 0.1),
                array_fill(0, 128, 0.2),
                array_fill(0, 128, 0.3),
            ]),
            'face_images' => json_encode([$this->dataImage(), $this->dataImage(), $this->dataImage()]),
        ];

        $this->actingAs($reception)
            ->post(route('admin.staff-members.store'), $payload)
            ->assertRedirect(route('admin.staff-members.index'));

        $staff = StaffMember::where('staff_code', 'EMP-001')->firstOrFail();
        $this->assertSame('pending', $staff->status);
        $this->assertCount(3, $staff->face_image_paths);

        $this->actingAs($admin)
            ->patch(route('admin.staff-members.approve', $staff))
            ->assertRedirect();

        $staff->refresh();
        $this->assertSame('approved', $staff->status);
        $this->assertNotNull($staff->approved_at);
    }

    public function test_staff_face_images_are_served_through_profile_route(): void
    {
        Storage::fake('public');

        $partner = TrainingPartner::create([
            'type' => 'STANDARD',
            'name' => 'Image Centre',
            'code' => 'IMG',
            'status' => 'active',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
            'training_partner_id' => $partner->id,
            'is_active' => true,
            'must_change_password' => false,
        ]);

        Storage::disk('public')->put('staff-members/enrollment/202606/sample-one.png', base64_decode(substr($this->dataImage(), strpos($this->dataImage(), ',') + 1)));

        $staff = StaffMember::create([
            'training_partner_id' => $partner->id,
            'name' => 'Photo Staff',
            'status' => 'approved',
            'face_image_paths' => ['staff-members/enrollment/202606/sample-one.png'],
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.staff-members.face-image', [$staff, 0]))
            ->assertOk()
            ->assertHeader('content-type', 'image/png');
    }

    public function test_staff_profile_can_be_updated_and_deleted(): void
    {
        Storage::fake('public');

        $partner = TrainingPartner::create([
            'type' => 'STANDARD',
            'name' => 'Edit Centre',
            'code' => 'EDIT',
            'status' => 'active',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
            'training_partner_id' => $partner->id,
            'is_active' => true,
            'must_change_password' => false,
        ]);

        Storage::disk('public')->put('staff-members/enrollment/202606/sample.jpg', 'sample');
        Storage::disk('public')->put('staff-members/attendance/1/check-ins/punch.jpg', 'punch');

        $staff = StaffMember::create([
            'training_partner_id' => $partner->id,
            'staff_code' => 'OLD-001',
            'name' => 'Old Name',
            'phone' => '9000000000',
            'status' => 'approved',
            'face_image_paths' => ['staff-members/enrollment/202606/sample.jpg'],
            'is_active' => true,
        ]);

        StaffMemberAttendance::create([
            'staff_member_id' => $staff->id,
            'training_partner_id' => $partner->id,
            'attendance_date' => now()->toDateString(),
            'check_in_at' => now(),
            'check_in_image_path' => 'staff-members/attendance/1/check-ins/punch.jpg',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.staff-members.update', $staff), [
                'staff_code' => 'NEW-001',
                'name' => 'New Name',
                'phone' => '9111111111',
                'email' => 'new@example.com',
                'designation' => 'Trainer',
                'department' => 'Academics',
                'joining_date' => now()->toDateString(),
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.staff-members.show', $staff));

        $staff->refresh();
        $this->assertSame('NEW-001', $staff->staff_code);
        $this->assertSame('New Name', $staff->name);

        $this->actingAs($admin)
            ->delete(route('admin.staff-members.destroy', $staff))
            ->assertRedirect(route('admin.staff-members.index'));

        $this->assertDatabaseMissing('staff_members', ['id' => $staff->id]);
        Storage::disk('public')->assertMissing('staff-members/enrollment/202606/sample.jpg');
        Storage::disk('public')->assertMissing('staff-members/attendance/1/check-ins/punch.jpg');
    }

    public function test_kiosk_punch_uses_final_time_windows_for_check_in_and_check_out(): void
    {
        Storage::fake('public');

        $partner = TrainingPartner::create([
            'type' => 'STANDARD',
            'name' => 'Kiosk Centre',
            'code' => 'KIOSK',
            'status' => 'active',
        ]);

        $reception = User::factory()->create([
            'role' => 'reception',
            'training_partner_id' => $partner->id,
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $staff = StaffMember::create([
            'training_partner_id' => $partner->id,
            'name' => 'Auto Match Staff',
            'status' => 'approved',
            'face_descriptors' => [array_fill(0, 128, 0.1), array_fill(0, 128, 0.2), array_fill(0, 128, 0.3)],
            'face_image_paths' => ['staff/sample.jpg'],
            'face_enrolled_at' => now(),
            'approved_at' => now(),
            'is_active' => true,
        ]);

        $this->travelTo(now()->setTime(9, 45));

        $this->actingAs($reception)
            ->postJson(route('admin.staff-attendance.kiosk.punch'), [
                'staff_member_id' => $staff->id,
                'face_image' => $this->dataImage(),
                'match_distance' => 0.31,
            ])
            ->assertOk()
            ->assertJsonPath('ok', true);

        $attendance = StaffMemberAttendance::where('staff_member_id', $staff->id)->firstOrFail();
        $this->assertSame('late', $attendance->check_in_status);
        $this->assertNotNull($attendance->check_in_at);
        $this->assertNull($attendance->check_out_at);
        Storage::disk('public')->assertExists($attendance->check_in_image_path);

        $this->travelTo(now()->setTime(16, 29));

        $this->actingAs($reception)
            ->postJson(route('admin.staff-attendance.kiosk.punch'), [
                'staff_member_id' => $staff->id,
                'face_image' => $this->dataImage(),
                'match_distance' => 0.31,
            ])
            ->assertUnprocessable();

        $this->travelTo(now()->setTime(16, 31));

        $this->actingAs($reception)
            ->postJson(route('admin.staff-attendance.kiosk.punch'), [
                'staff_member_id' => $staff->id,
                'face_image' => $this->dataImage(),
                'match_distance' => 0.31,
            ])
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('action', 'check_out');

        $attendance->refresh();
        $this->assertSame('early', $attendance->check_out_status);
        $this->assertSame('16:31', $attendance->check_out_at->format('H:i'));
        Storage::disk('public')->assertExists($attendance->check_out_image_path);

        $this->travelTo(now()->setTime(16, 32));

        $this->actingAs($reception)
            ->postJson(route('admin.staff-attendance.kiosk.punch'), [
                'staff_member_id' => $staff->id,
                'face_image' => $this->dataImage(),
                'match_distance' => 0.29,
            ])
            ->assertUnprocessable();

        $this->travelTo(now()->setTime(18, 35));

        $this->actingAs($reception)
            ->postJson(route('admin.staff-attendance.kiosk.punch'), [
                'staff_member_id' => $staff->id,
                'face_image' => $this->dataImage(),
                'match_distance' => 0.29,
            ])
            ->assertOk()
            ->assertJsonPath('ok', true);

        $attendance->refresh();
        $this->assertSame('18:35', $attendance->check_out_at->format('H:i'));
        $this->assertSame('on_time', $attendance->check_out_status);
        $this->assertSame('0.29000', (string) $attendance->check_out_match_distance);
    }

    public function test_admin_can_correct_staff_attendance_record(): void
    {
        $partner = TrainingPartner::create([
            'type' => 'STANDARD',
            'name' => 'Correction Centre',
            'code' => 'CORR',
            'status' => 'active',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
            'training_partner_id' => $partner->id,
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $staff = StaffMember::create([
            'training_partner_id' => $partner->id,
            'name' => 'Correction Staff',
            'status' => 'approved',
            'is_active' => true,
        ]);

        $attendance = StaffMemberAttendance::create([
            'staff_member_id' => $staff->id,
            'training_partner_id' => $partner->id,
            'attendance_date' => '2026-06-03',
            'check_in_at' => '2026-06-03 09:55:00',
            'check_in_status' => 'late',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.staff-attendance.update-record', $attendance), [
                'attendance_date' => '2026-06-03',
                'check_in_time' => '09:25',
                'check_out_time' => '18:05',
                'check_in_status' => 'manual',
                'check_out_status' => 'manual',
                'notes' => 'Corrected after review',
            ])
            ->assertRedirect();

        $attendance->refresh();
        $this->assertSame('09:25', $attendance->check_in_at->format('H:i'));
        $this->assertSame('18:05', $attendance->check_out_at->format('H:i'));
        $this->assertSame('Corrected after review', $attendance->notes);
    }

    public function test_admin_can_view_individual_staff_attendance_report(): void
    {
        $partner = TrainingPartner::create([
            'type' => 'STANDARD',
            'name' => 'Report Centre',
            'code' => 'RPT',
            'status' => 'active',
            'attendance_radius_meters' => 100,
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
            'training_partner_id' => $partner->id,
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $staff = StaffMember::create([
            'training_partner_id' => $partner->id,
            'staff_code' => 'REP-001',
            'name' => 'Report Staff',
            'designation' => 'Trainer',
            'status' => 'approved',
            'is_active' => true,
        ]);

        StaffMemberAttendance::create([
            'staff_member_id' => $staff->id,
            'training_partner_id' => $partner->id,
            'attendance_date' => '2026-06-01',
            'check_in_at' => '2026-06-01 09:20:00',
            'check_out_at' => '2026-06-01 18:05:00',
            'check_in_status' => 'on_time',
            'check_out_status' => 'on_time',
            'check_in_distance_meters' => 30,
        ]);

        StaffMemberAttendance::create([
            'staff_member_id' => $staff->id,
            'training_partner_id' => $partner->id,
            'attendance_date' => '2026-06-02',
            'check_in_at' => '2026-06-02 09:45:00',
            'check_in_status' => 'late',
            'check_in_distance_meters' => 150,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.staff-attendance.staff-report', [
                'staffMember' => $staff,
                'from' => '2026-06-01',
                'to' => '2026-06-02',
            ]))
            ->assertOk()
            ->assertSeeText('Report Staff')
            ->assertSeeText('On-time punch %')
            ->assertSeeText('50%')
            ->assertSeeText('Late punch %')
            ->assertSeeText('Outside location')
            ->assertSeeText('Status split');
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

        $firstStaff = StaffMember::create([
            'name' => 'Visible Staff',
            'training_partner_id' => $firstPartner->id,
            'status' => 'approved',
            'is_active' => true,
        ]);

        $secondStaff = StaffMember::create([
            'name' => 'Hidden Staff',
            'training_partner_id' => $secondPartner->id,
            'status' => 'approved',
            'is_active' => true,
        ]);

        StaffMemberAttendance::create([
            'staff_member_id' => $firstStaff->id,
            'training_partner_id' => $firstPartner->id,
            'attendance_date' => now()->toDateString(),
            'check_in_at' => now(),
        ]);

        StaffMemberAttendance::create([
            'staff_member_id' => $secondStaff->id,
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
