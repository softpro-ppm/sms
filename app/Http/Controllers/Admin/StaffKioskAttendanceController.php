<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffMember;
use App\Models\StaffMemberAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class StaffKioskAttendanceController extends Controller
{
    private const CHECK_IN_START = '06:00';
    private const CHECK_IN_ON_TIME_UNTIL = '09:30';
    private const CHECK_IN_END = '10:00';
    private const CHECK_OUT_START = '16:30';
    private const CHECK_OUT_EXPECTED = '18:00';
    private const CHECK_OUT_END = '21:00';
    private const MAX_MATCH_DISTANCE = 0.52;

    public function kiosk()
    {
        $staffMembers = $this->approvedStaffQuery()
            ->get()
            ->map(fn (StaffMember $staff) => [
                'id' => $staff->id,
                'name' => $staff->name,
                'staff_code' => $staff->staff_code,
                'designation' => $staff->designation,
                'descriptors' => $staff->face_descriptors ?? [],
            ])
            ->values();

        $trainingPartner = auth()->user()->trainingPartner;
        $settings = [
            'check_in_start' => self::CHECK_IN_START,
            'check_in_on_time_until' => self::CHECK_IN_ON_TIME_UNTIL,
            'check_in_end' => self::CHECK_IN_END,
            'check_out_start' => self::CHECK_OUT_START,
            'check_out_expected' => self::CHECK_OUT_EXPECTED,
            'check_out_end' => self::CHECK_OUT_END,
            'max_match_distance' => self::MAX_MATCH_DISTANCE,
            'geofence' => [
                'latitude' => $trainingPartner?->attendance_latitude,
                'longitude' => $trainingPartner?->attendance_longitude,
                'radius_meters' => $trainingPartner?->attendance_radius_meters,
            ],
        ];

        return view('admin.staff-attendance.kiosk', compact('staffMembers', 'settings'));
    }

    public function punch(Request $request)
    {
        $validated = $request->validate([
            'staff_member_id' => ['required', 'integer', 'exists:staff_members,id'],
            'face_image' => ['required', 'string'],
            'match_distance' => ['required', 'numeric', 'min:0', 'max:2'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'accuracy' => ['nullable', 'numeric', 'min:0', 'max:10000'],
        ]);

        if ((float) $validated['match_distance'] > self::MAX_MATCH_DISTANCE) {
            throw ValidationException::withMessages([
                'attendance' => 'Face match confidence is too low. Please try again.',
            ]);
        }

        $staff = StaffMember::findOrFail($validated['staff_member_id']);
        $this->ensureStaffAccess($staff);

        if (!$staff->is_approved || empty($staff->face_descriptors)) {
            throw ValidationException::withMessages([
                'attendance' => 'This staff profile is not approved for attendance.',
            ]);
        }

        $now = now();

        $distance = $this->distanceFromCentre($validated['latitude'] ?? null, $validated['longitude'] ?? null);
        if ($distance['configured'] && !$distance['inside']) {
            throw ValidationException::withMessages([
                'attendance' => 'This device is outside the approved attendance location.',
            ]);
        }

        $attendance = StaffMemberAttendance::where('staff_member_id', $staff->id)
            ->whereDate('attendance_date', $now->toDateString())
            ->first() ?? new StaffMemberAttendance([
                'staff_member_id' => $staff->id,
                'attendance_date' => $now->toDateString(),
            ]);

        if (!$attendance->check_in_at) {
            $attendance->fill([
                'training_partner_id' => $staff->training_partner_id,
                'kiosk_user_id' => auth()->id(),
                'check_in_at' => $now,
                'check_in_status' => 'test_first_capture',
                'check_in_image_path' => $this->storeDataImage($validated['face_image'], "staff-members/attendance/{$staff->id}/check-ins"),
                'check_in_match_distance' => $validated['match_distance'],
                'check_in_latitude' => $validated['latitude'] ?? null,
                'check_in_longitude' => $validated['longitude'] ?? null,
                'check_in_accuracy_meters' => isset($validated['accuracy']) ? (int) round($validated['accuracy']) : null,
                'check_in_distance_meters' => $distance['meters'],
                'check_in_ip' => $request->ip(),
                'check_in_user_agent' => (string) $request->userAgent(),
            ])->save();

            return response()->json([
                'ok' => true,
                'message' => "{$staff->name} check-in recorded at " . $now->format('h:i A') . '.',
                'staff' => $staff->name,
            ]);
        }

        $attendance->update([
            'kiosk_user_id' => auth()->id(),
            'check_out_at' => $now,
            'check_out_status' => 'test_latest_capture',
            'check_out_image_path' => $this->storeDataImage($validated['face_image'], "staff-members/attendance/{$staff->id}/check-outs"),
            'check_out_match_distance' => $validated['match_distance'],
            'check_out_latitude' => $validated['latitude'] ?? null,
            'check_out_longitude' => $validated['longitude'] ?? null,
            'check_out_accuracy_meters' => isset($validated['accuracy']) ? (int) round($validated['accuracy']) : null,
            'check_out_distance_meters' => $distance['meters'],
            'check_out_ip' => $request->ip(),
            'check_out_user_agent' => (string) $request->userAgent(),
        ]);

        return response()->json([
            'ok' => true,
            'message' => "{$staff->name} check-out updated at " . $now->format('h:i A') . '.',
            'staff' => $staff->name,
        ]);
    }

    public function records(Request $request)
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'staff_member_id' => ['nullable', 'integer'],
        ]);

        $from = Carbon::parse($request->input('from', now()->toDateString()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()->toDateString()))->endOfDay();

        $query = StaffMemberAttendance::with('staffMember')
            ->whereBetween('attendance_date', [$from->toDateString(), $to->toDateString()])
            ->latest('attendance_date')
            ->latest('check_in_at');

        $this->scopeAttendanceQuery($query);

        if ($request->filled('staff_member_id')) {
            $query->where('staff_member_id', $request->integer('staff_member_id'));
        }

        $records = $query->paginate(20)->withQueryString();
        $staffMembers = $this->staffListQuery()->get();
        $trainingPartner = auth()->user()->trainingPartner;

        return view('admin.staff-attendance.records', compact('records', 'staffMembers', 'from', 'to', 'trainingPartner'));
    }

    public function updateSettings(Request $request)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $validated = $request->validate([
            'attendance_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'attendance_longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'attendance_radius_meters' => ['nullable', 'integer', 'min:20', 'max:1000'],
        ]);

        $trainingPartner = auth()->user()->trainingPartner;
        if (!$trainingPartner) {
            return back()->withErrors(['attendance_latitude' => 'Training partner is required to configure attendance location.']);
        }

        $trainingPartner->update($validated);

        return back()->with('success', 'Attendance geofence updated.');
    }

    public function export(Request $request)
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'staff_member_id' => ['nullable', 'integer'],
        ]);

        $from = Carbon::parse($request->input('from', now()->toDateString()))->toDateString();
        $to = Carbon::parse($request->input('to', now()->toDateString()))->toDateString();

        $query = StaffMemberAttendance::with('staffMember')
            ->whereBetween('attendance_date', [$from, $to])
            ->orderBy('attendance_date')
            ->orderBy('check_in_at');

        $this->scopeAttendanceQuery($query);

        if ($request->filled('staff_member_id')) {
            $query->where('staff_member_id', $request->integer('staff_member_id'));
        }

        $rows = collect([['Date', 'Staff Code', 'Staff', 'Designation', 'Check In', 'In Status', 'Check Out', 'Out Status', 'Hours', 'In Match', 'Out Match']]);

        $query->chunk(200, function ($records) use ($rows) {
            foreach ($records as $record) {
                $rows->push([
                    $record->attendance_date?->toDateString(),
                    $record->staffMember?->staff_code,
                    $record->staffMember?->name,
                    $record->staffMember?->designation,
                    $record->check_in_at?->format('Y-m-d H:i:s'),
                    $record->check_in_status,
                    $record->check_out_at?->format('Y-m-d H:i:s'),
                    $record->check_out_status,
                    $record->check_in_at && $record->check_out_at ? round($record->check_in_at->diffInMinutes($record->check_out_at) / 60, 2) : '',
                    $record->check_in_match_distance,
                    $record->check_out_match_distance,
                ]);
            }
        });

        $csv = $rows->map(fn ($row) => collect($row)->map(fn ($cell) => '"' . str_replace('"', '""', (string) $cell) . '"')->implode(','))->implode("\n");

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="staff-attendance-' . $from . '-to-' . $to . '.csv"',
        ]);
    }

    protected function approvedStaffQuery()
    {
        $query = StaffMember::where('status', 'approved')
            ->where('is_active', true)
            ->whereNotNull('face_descriptors')
            ->orderBy('name');

        $this->scopeStaffQuery($query);

        return $query;
    }

    protected function staffListQuery()
    {
        $query = StaffMember::orderBy('name');
        $this->scopeStaffQuery($query);

        return $query;
    }

    protected function ensureStaffAccess(StaffMember $staff): void
    {
        $tpId = auth()->user()->training_partner_id;
        if ($tpId !== null && $staff->training_partner_id !== $tpId) {
            abort(404);
        }
    }

    protected function scopeStaffQuery($query): void
    {
        $tpId = auth()->user()->training_partner_id;
        if ($tpId !== null) {
            $query->where('training_partner_id', $tpId);
        }
    }

    protected function scopeAttendanceQuery($query): void
    {
        $tpId = auth()->user()->training_partner_id;
        if ($tpId !== null) {
            $query->where('training_partner_id', $tpId);
        }
    }

    protected function distanceFromCentre($latitude, $longitude): array
    {
        $tp = auth()->user()->trainingPartner;
        if (!$tp?->attendance_latitude || !$tp?->attendance_longitude || !$tp?->attendance_radius_meters || !$latitude || !$longitude) {
            return ['configured' => false, 'inside' => true, 'meters' => null];
        }

        $earthRadius = 6371000;
        $latFrom = deg2rad((float) $tp->attendance_latitude);
        $lonFrom = deg2rad((float) $tp->attendance_longitude);
        $latTo = deg2rad((float) $latitude);
        $lonTo = deg2rad((float) $longitude);
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        $meters = (int) round($angle * $earthRadius);

        return [
            'configured' => true,
            'inside' => $meters <= (int) $tp->attendance_radius_meters,
            'meters' => $meters,
        ];
    }

    protected function storeDataImage(string $image, string $directory): string
    {
        if (!preg_match('/^data:image\/(png|jpeg|jpg|webp);base64,/', $image, $matches)) {
            throw ValidationException::withMessages([
                'attendance' => 'Please capture a valid face image from the camera.',
            ]);
        }

        $extension = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];
        $data = base64_decode(substr($image, strpos($image, ',') + 1), true);

        if ($data === false || strlen($data) > 5 * 1024 * 1024) {
            throw ValidationException::withMessages([
                'attendance' => 'The captured image is invalid or too large.',
            ]);
        }

        $path = trim($directory, '/') . '/' . now()->format('YmdHis') . '-' . bin2hex(random_bytes(5)) . '.' . $extension;
        Storage::disk('public')->put($path, $data);

        return $path;
    }
}
