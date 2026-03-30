<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesByTrainingPartner;
use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Support\Facades\Schema;

class CourseLearningController extends Controller
{
    use ScopesByTrainingPartner;

    public function show(Course $course)
    {
        if (!Schema::hasTable('course_modules')) {
            return redirect()->route('admin.courses.show', $course)
                ->with('error', 'Run migrations to enable course learning (course_modules).');
        }

        $tpId = $this->getTrainingPartnerId();
        if ($tpId !== null) {
            $allowed = Course::query()->visibleToTrainingPartner($tpId)->whereKey($course->id)->exists();
            abort_unless($allowed, 403);
        }

        $modules = $course->learningModules()
            ->with(['lessons' => fn ($q) => $q->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        return view('admin.courses.learning', compact('course', 'modules'));
    }
}
