<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StaffMemberController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'status' => ['nullable', Rule::in(['pending', 'approved', 'rejected', 'inactive'])],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $query = StaffMember::query()
            ->with(['creator', 'approver'])
            ->latest();

        $this->scopeStaffQuery($query);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($inner) use ($search) {
                $inner->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('staff_code', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%");
            });
        }

        $staffMembers = $query->paginate(20)->withQueryString();
        $counts = $this->counts();

        return view('admin.staff-members.index', compact('staffMembers', 'counts'));
    }

    public function create()
    {
        return view('admin.staff-members.create');
    }

    public function store(Request $request)
    {
        $tpId = auth()->user()->training_partner_id;

        $validated = $request->validate([
            'staff_code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('staff_members', 'staff_code')->where(fn ($query) => $query->where('training_partner_id', $tpId)),
            ],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'designation' => ['nullable', 'string', 'max:120'],
            'department' => ['nullable', 'string', 'max:120'],
            'joining_date' => ['nullable', 'date'],
            'face_descriptors' => ['required', 'json'],
            'face_images' => ['required', 'json'],
        ]);

        $descriptors = json_decode($validated['face_descriptors'], true);
        $images = json_decode($validated['face_images'], true);

        if (!is_array($descriptors) || count($descriptors) < 3 || !is_array($images) || count($images) < 3) {
            throw ValidationException::withMessages([
                'face_images' => 'Capture at least 3 valid face samples before submitting.',
            ]);
        }

        $imagePaths = [];
        foreach (array_slice($images, 0, 5) as $index => $image) {
            $imagePaths[] = $this->storeDataImage((string) $image, 'staff-members/enrollment/' . now()->format('Ym'), "sample-{$index}");
        }

        StaffMember::create([
            'training_partner_id' => $tpId,
            'created_by' => auth()->id(),
            'staff_code' => $validated['staff_code'] ?? null,
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'designation' => $validated['designation'] ?? null,
            'department' => $validated['department'] ?? null,
            'joining_date' => $validated['joining_date'] ?? null,
            'status' => 'pending',
            'face_descriptors' => array_slice($descriptors, 0, 5),
            'face_image_paths' => $imagePaths,
            'face_enrolled_at' => now(),
            'is_active' => true,
        ]);

        return redirect()->route('admin.staff-members.index')
            ->with('success', 'Staff profile submitted for admin approval.');
    }

    public function show(StaffMember $staffMember)
    {
        $this->ensureStaffAccess($staffMember);
        $staffMember->load(['creator', 'approver', 'attendances' => fn ($query) => $query->latest('attendance_date')->limit(10)]);

        return view('admin.staff-members.show', compact('staffMember'));
    }

    public function approve(Request $request, StaffMember $staffMember)
    {
        $this->ensureAdmin();
        $this->ensureStaffAccess($staffMember);

        $validated = $request->validate([
            'approval_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $staffMember->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'approval_notes' => $validated['approval_notes'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('success', 'Staff profile approved for attendance.');
    }

    public function reject(Request $request, StaffMember $staffMember)
    {
        $this->ensureAdmin();
        $this->ensureStaffAccess($staffMember);

        $validated = $request->validate([
            'approval_notes' => ['required', 'string', 'max:1000'],
        ]);

        $staffMember->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'approval_notes' => $validated['approval_notes'],
            'is_active' => false,
        ]);

        return back()->with('success', 'Staff profile rejected.');
    }

    protected function counts(): array
    {
        $query = StaffMember::query();
        $this->scopeStaffQuery($query);

        return [
            'total' => (clone $query)->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'approved' => (clone $query)->where('status', 'approved')->where('is_active', true)->count(),
            'rejected' => (clone $query)->where('status', 'rejected')->count(),
        ];
    }

    protected function ensureAdmin(): void
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }
    }

    protected function ensureStaffAccess(StaffMember $staffMember): void
    {
        $tpId = auth()->user()->training_partner_id;
        if ($tpId !== null && $staffMember->training_partner_id !== $tpId) {
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

    protected function storeDataImage(string $image, string $directory, string $prefix): string
    {
        if (!preg_match('/^data:image\/(png|jpeg|jpg|webp);base64,/', $image, $matches)) {
            throw ValidationException::withMessages([
                'face_images' => 'One or more captured face images are invalid.',
            ]);
        }

        $extension = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];
        $data = base64_decode(substr($image, strpos($image, ',') + 1), true);

        if ($data === false || strlen($data) > 5 * 1024 * 1024) {
            throw ValidationException::withMessages([
                'face_images' => 'One or more captured face images are invalid or too large.',
            ]);
        }

        $path = trim($directory, '/') . '/' . $prefix . '-' . bin2hex(random_bytes(5)) . '.' . $extension;
        Storage::disk('public')->put($path, $data);

        return $path;
    }
}
