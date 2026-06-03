<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffAttendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class StaffAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'user_id' => ['nullable', 'integer'],
        ]);

        $from = Carbon::parse($filters['from'] ?? now()->toDateString())->startOfDay();
        $to = Carbon::parse($filters['to'] ?? now()->toDateString())->endOfDay();
        $staffUsers = $this->staffQuery()->get();

        $recordsQuery = StaffAttendance::query()
            ->with('user')
            ->whereBetween('attendance_date', [$from->toDateString(), $to->toDateString()])
            ->latest('attendance_date')
            ->latest('check_in_at');

        $this->scopeAttendanceQuery($recordsQuery);

        if (!empty($filters['user_id'])) {
            $recordsQuery->where('user_id', $filters['user_id']);
        }

        $records = $recordsQuery->paginate(20)->withQueryString();

        $todayQuery = StaffAttendance::query()->whereDate('attendance_date', now()->toDateString());
        $this->scopeAttendanceQuery($todayQuery);

        $stats = [
            'staff' => $staffUsers->count(),
            'enrolled' => $staffUsers->whereNotNull('face_enrolled_at')->count(),
            'checked_in_today' => (clone $todayQuery)->whereNotNull('check_in_at')->count(),
            'checked_out_today' => (clone $todayQuery)->whereNotNull('check_out_at')->count(),
        ];

        return view('admin.staff-attendance.index', compact('records', 'staffUsers', 'stats', 'from', 'to'));
    }

    public function check()
    {
        $user = auth()->user();
        $todayAttendance = StaffAttendance::where('user_id', $user->id)
            ->whereDate('attendance_date', now()->toDateString())
            ->first();

        return view('admin.staff-attendance.check', compact('user', 'todayAttendance'));
    }

    public function enrollFace(Request $request)
    {
        $validated = $request->validate([
            'face_image' => ['required', 'string'],
        ]);

        $user = auth()->user();
        $path = $this->storeDataImage($validated['face_image'], "staff-faces/{$user->id}");

        $user->update([
            'face_reference_image_path' => $path,
            'face_enrolled_at' => now(),
        ]);

        return back()->with('success', 'Face reference captured successfully.');
    }

    public function checkIn(Request $request)
    {
        $validated = $request->validate([
            'face_image' => ['required', 'string'],
        ]);

        $user = auth()->user();
        if (!$user->face_enrolled_at) {
            throw ValidationException::withMessages([
                'face_image' => 'Please enroll your face reference before check-in.',
            ]);
        }

        $attendance = StaffAttendance::firstOrNew([
            'user_id' => $user->id,
            'attendance_date' => now()->toDateString(),
        ]);

        if ($attendance->check_in_at) {
            return back()->with('success', 'You are already checked in for today.');
        }

        $attendance->fill([
            'training_partner_id' => $user->training_partner_id,
            'check_in_at' => now(),
            'check_in_image_path' => $this->storeDataImage($validated['face_image'], "staff-attendance/{$user->id}/check-ins"),
            'check_in_ip' => $request->ip(),
            'check_in_user_agent' => (string) $request->userAgent(),
            'status' => 'present',
        ])->save();

        return back()->with('success', 'Check-in recorded.');
    }

    public function checkOut(Request $request)
    {
        $validated = $request->validate([
            'face_image' => ['required', 'string'],
        ]);

        $attendance = StaffAttendance::where('user_id', auth()->id())
            ->whereDate('attendance_date', now()->toDateString())
            ->first();

        if (!$attendance?->check_in_at) {
            throw ValidationException::withMessages([
                'face_image' => 'Please check in before check-out.',
            ]);
        }

        if ($attendance->check_out_at) {
            return back()->with('success', 'You are already checked out for today.');
        }

        $attendance->update([
            'check_out_at' => now(),
            'check_out_image_path' => $this->storeDataImage($validated['face_image'], 'staff-attendance/' . auth()->id() . '/check-outs'),
            'check_out_ip' => $request->ip(),
            'check_out_user_agent' => (string) $request->userAgent(),
        ]);

        return back()->with('success', 'Check-out recorded.');
    }

    public function export(Request $request)
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'user_id' => ['nullable', 'integer'],
        ]);

        $from = Carbon::parse($request->input('from', now()->toDateString()))->toDateString();
        $to = Carbon::parse($request->input('to', now()->toDateString()))->toDateString();

        $query = StaffAttendance::with('user')
            ->whereBetween('attendance_date', [$from, $to])
            ->orderBy('attendance_date')
            ->orderBy('check_in_at');

        $this->scopeAttendanceQuery($query);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }

        $rows = collect([['Date', 'Staff', 'Email', 'Role', 'Check In', 'Check Out', 'Hours', 'Status']]);

        $query->chunk(200, function ($records) use ($rows) {
            foreach ($records as $record) {
                $hours = $record->check_in_at && $record->check_out_at
                    ? round($record->check_in_at->diffInMinutes($record->check_out_at) / 60, 2)
                    : '';

                $rows->push([
                    $record->attendance_date?->toDateString(),
                    $record->user?->name,
                    $record->user?->email,
                    $record->user?->role,
                    $record->check_in_at?->format('Y-m-d H:i:s'),
                    $record->check_out_at?->format('Y-m-d H:i:s'),
                    $hours,
                    $record->status,
                ]);
            }
        });

        $csv = $rows->map(fn ($row) => collect($row)->map(fn ($cell) => '"' . str_replace('"', '""', (string) $cell) . '"')->implode(','))->implode("\n");

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="staff-attendance-' . $from . '-to-' . $to . '.csv"',
        ]);
    }

    protected function staffQuery()
    {
        $query = User::whereIn('role', ['admin', 'reception'])
            ->whereNull('student_id')
            ->orderBy('name');

        $tpId = auth()->user()->training_partner_id;
        if ($tpId !== null) {
            $query->where('training_partner_id', $tpId);
        }

        return $query;
    }

    protected function scopeAttendanceQuery($query): void
    {
        $tpId = auth()->user()->training_partner_id;
        if ($tpId !== null) {
            $query->where('training_partner_id', $tpId);
        }
    }

    protected function storeDataImage(string $image, string $directory): string
    {
        if (!preg_match('/^data:image\/(png|jpeg|jpg|webp);base64,/', $image, $matches)) {
            throw ValidationException::withMessages([
                'face_image' => 'Please capture a valid face image from the camera.',
            ]);
        }

        $extension = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];
        $data = base64_decode(substr($image, strpos($image, ',') + 1), true);

        if ($data === false || strlen($data) > 5 * 1024 * 1024) {
            throw ValidationException::withMessages([
                'face_image' => 'The captured image is invalid or too large.',
            ]);
        }

        $path = trim($directory, '/') . '/' . now()->format('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $extension;
        Storage::disk('public')->put($path, $data);

        return $path;
    }
}
