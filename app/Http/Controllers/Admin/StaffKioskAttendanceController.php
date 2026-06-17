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
    private const MAX_MATCH_DISTANCE = 0.38;
    private const PUNCH_COOLDOWN_SECONDS = 120;

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

        $distance = $this->distanceFromCentre(
            $validated['latitude'] ?? null,
            $validated['longitude'] ?? null,
            $validated['accuracy'] ?? null
        );
        if ($distance['configured'] && !$distance['inside']) {
            throw ValidationException::withMessages([
                'attendance' => 'This device is outside the approved attendance location'
                    . ($distance['meters'] !== null ? ' (' . $distance['meters'] . 'm away' : '')
                    . ($distance['accuracy_meters'] !== null ? ', GPS accuracy ' . $distance['accuracy_meters'] . 'm' : '')
                    . ').',
            ]);
        }

        $attendance = StaffMemberAttendance::where('staff_member_id', $staff->id)
            ->whereDate('attendance_date', $now->toDateString())
            ->first() ?? new StaffMemberAttendance([
                'staff_member_id' => $staff->id,
                'attendance_date' => $now->toDateString(),
            ]);

        if (!$attendance->check_in_at) {
            $isRegistrationDay = $this->isRegistrationDay($staff, $now);
            if (!$isRegistrationDay) {
                $this->ensureWithinWindow($now, 'check_in');
            }

            $attendance->fill([
                'training_partner_id' => $staff->training_partner_id,
                'kiosk_user_id' => auth()->id(),
                'check_in_at' => $now,
                'check_in_status' => $isRegistrationDay ? 'registration_day' : ($now->format('H:i') > self::CHECK_IN_ON_TIME_UNTIL ? 'late' : 'on_time'),
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
                'action' => 'check_in',
                'location' => $distance,
            ]);
        }

        $isRegistrationDay = $this->isRegistrationDay($staff, $now);
        if (!$isRegistrationDay) {
            $this->ensureWithinWindow($now, 'check_out');
        }

        if ($attendance->check_out_at && $attendance->check_out_at->diffInSeconds($now) < self::PUNCH_COOLDOWN_SECONDS) {
            throw ValidationException::withMessages([
                'attendance' => 'Attendance already updated recently. Please wait before punching again.',
            ]);
        }

        $attendance->update([
            'kiosk_user_id' => auth()->id(),
            'check_out_at' => $now,
            'check_out_status' => $isRegistrationDay ? 'registration_day' : ($now->format('H:i') < self::CHECK_OUT_EXPECTED ? 'early' : 'on_time'),
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
            'action' => 'check_out',
            'location' => $distance,
        ]);
    }

    public function updateRecord(Request $request, StaffMemberAttendance $attendance)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $this->ensureAttendanceAccess($attendance);

        $validated = $request->validate([
            'attendance_date' => ['required', 'date'],
            'check_in_time' => ['nullable', 'date_format:H:i'],
            'check_out_time' => ['nullable', 'date_format:H:i'],
            'check_in_status' => ['nullable', 'string', 'max:50'],
            'check_out_status' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $date = Carbon::parse($validated['attendance_date'])->toDateString();
        $attendance->update([
            'attendance_date' => $date,
            'check_in_at' => !empty($validated['check_in_time']) ? Carbon::parse($date . ' ' . $validated['check_in_time']) : null,
            'check_out_at' => !empty($validated['check_out_time']) ? Carbon::parse($date . ' ' . $validated['check_out_time']) : null,
            'check_in_status' => $validated['check_in_status'] ?: null,
            'check_out_status' => $validated['check_out_status'] ?: null,
            'notes' => $validated['notes'] ?: null,
        ]);

        return back()->with('success', 'Attendance record corrected.');
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

        $summaryQuery = clone $query;
        $records = $query->paginate(20)->withQueryString();
        $staffMembers = $this->staffListQuery()->get();
        $trainingPartner = auth()->user()->trainingPartner;
        $geofenceRadius = $trainingPartner?->attendance_radius_meters;
        $summary = [
            'present' => (clone $summaryQuery)->count(),
            'missing_checkout' => (clone $summaryQuery)->whereNotNull('check_in_at')->whereNull('check_out_at')->count(),
            'late' => (clone $summaryQuery)->where('check_in_status', 'late')->count(),
            'outside_location' => $geofenceRadius ? (clone $summaryQuery)
                ->where(function ($inner) use ($geofenceRadius) {
                    $inner->where('check_in_distance_meters', '>', $geofenceRadius)
                        ->orWhere('check_out_distance_meters', '>', $geofenceRadius);
                })->count() : 0,
        ];

        return view('admin.staff-attendance.records', compact('records', 'staffMembers', 'from', 'to', 'trainingPartner', 'summary'));
    }

    public function staffReport(Request $request, StaffMember $staffMember)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $this->ensureStaffAccess($staffMember);

        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = Carbon::parse($request->input('from', now()->startOfMonth()->toDateString()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()->toDateString()))->endOfDay();
        $trainingPartner = auth()->user()->trainingPartner;
        $geofenceRadius = $trainingPartner?->attendance_radius_meters;

        $records = StaffMemberAttendance::where('staff_member_id', $staffMember->id)
            ->whereBetween('attendance_date', [$from->toDateString(), $to->toDateString()])
            ->orderBy('attendance_date')
            ->get();

        $present = $records->whereNotNull('check_in_at')->count();
        $onTime = $records->where('check_in_status', 'on_time')->count();
        $late = $records->where('check_in_status', 'late')->count();
        $missingCheckout = $records->whereNotNull('check_in_at')->whereNull('check_out_at')->count();
        $earlyCheckout = $records->where('check_out_status', 'early')->count();
        $outsideLocation = $geofenceRadius ? $records->filter(fn ($record) => (
            ($record->check_in_distance_meters !== null && $record->check_in_distance_meters > $geofenceRadius)
            || ($record->check_out_distance_meters !== null && $record->check_out_distance_meters > $geofenceRadius)
        ))->count() : 0;

        $totalMinutes = $records->sum(fn ($record) => $record->check_in_at && $record->check_out_at
            ? $record->check_in_at->diffInMinutes($record->check_out_at)
            : 0);

        $metrics = [
            'records' => $records->count(),
            'present' => $present,
            'on_time' => $onTime,
            'late' => $late,
            'missing_checkout' => $missingCheckout,
            'early_checkout' => $earlyCheckout,
            'outside_location' => $outsideLocation,
            'on_time_percent' => $present ? round(($onTime / $present) * 100, 1) : 0,
            'late_percent' => $present ? round(($late / $present) * 100, 1) : 0,
            'missing_checkout_percent' => $present ? round(($missingCheckout / $present) * 100, 1) : 0,
            'total_hours' => round($totalMinutes / 60, 2),
            'average_hours' => $present ? round(($totalMinutes / 60) / $present, 2) : 0,
            'average_check_in_match' => round((float) $records->whereNotNull('check_in_match_distance')->avg('check_in_match_distance'), 3),
            'average_check_out_match' => round((float) $records->whereNotNull('check_out_match_distance')->avg('check_out_match_distance'), 3),
        ];

        $chart = [
            'status_labels' => ['On time', 'Late', 'Missing checkout', 'Early checkout'],
            'status_values' => [$onTime, $late, $missingCheckout, $earlyCheckout],
            'daily_labels' => $records->map(fn ($record) => $record->attendance_date?->format('d M'))->values(),
            'daily_hours' => $records->map(fn ($record) => $record->check_in_at && $record->check_out_at
                ? round($record->check_in_at->diffInMinutes($record->check_out_at) / 60, 2)
                : 0)->values(),
            'daily_late' => $records->map(fn ($record) => $record->check_in_status === 'late' ? 1 : 0)->values(),
        ];

        return view('admin.staff-attendance.staff-report', compact('staffMember', 'records', 'from', 'to', 'trainingPartner', 'metrics', 'chart'));
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

    protected function ensureAttendanceAccess(StaffMemberAttendance $attendance): void
    {
        $tpId = auth()->user()->training_partner_id;
        if ($tpId !== null && $attendance->training_partner_id !== $tpId) {
            abort(404);
        }
    }

    protected function ensureWithinWindow(Carbon $now, string $action): void
    {
        $time = $now->format('H:i');

        if ($action === 'check_in' && ($time < self::CHECK_IN_START || $time > self::CHECK_IN_END)) {
            throw ValidationException::withMessages([
                'attendance' => 'Check-in is allowed only from ' . self::CHECK_IN_START . ' to ' . self::CHECK_IN_END . '.',
            ]);
        }

        if ($action === 'check_out' && ($time < self::CHECK_OUT_START || $time > self::CHECK_OUT_END)) {
            throw ValidationException::withMessages([
                'attendance' => 'Check-out is allowed only from ' . self::CHECK_OUT_START . ' to ' . self::CHECK_OUT_END . '.',
            ]);
        }
    }

    protected function distanceFromCentre($latitude, $longitude, $accuracy = null): array
    {
        $tp = auth()->user()->trainingPartner;
        if (!$tp?->attendance_latitude || !$tp?->attendance_longitude || !$tp?->attendance_radius_meters || !$latitude || !$longitude) {
            return ['configured' => false, 'inside' => true, 'meters' => null, 'accuracy_meters' => null, 'effective_meters' => null];
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
        $accuracyMeters = $accuracy !== null ? (int) round((float) $accuracy) : null;
        $effectiveMeters = max(0, $meters - min($accuracyMeters ?? 0, 5000));

        return [
            'configured' => true,
            'inside' => $effectiveMeters <= (int) $tp->attendance_radius_meters,
            'meters' => $meters,
            'accuracy_meters' => $accuracyMeters,
            'effective_meters' => $effectiveMeters,
        ];
    }

    protected function isRegistrationDay(StaffMember $staff, Carbon $now): bool
    {
        $reference = $staff->face_enrolled_at ?: $staff->created_at;

        return $reference !== null && $reference->isSameDay($now);
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
