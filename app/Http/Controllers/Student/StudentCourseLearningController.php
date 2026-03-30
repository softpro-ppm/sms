<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CourseLesson;
use App\Models\Enrollment;
use App\Models\EnrollmentLessonCompletion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class StudentCourseLearningController extends Controller
{
    public function resume(Enrollment $enrollment)
    {
        if (! Schema::hasTable('course_modules')) {
            abort(404, 'Learning content is not available yet.');
        }

        $student = Auth::user()->student;
        abort_unless($student && (int) $enrollment->student_id === (int) $student->id, 403);

        [$course, $modules] = $this->courseAndModules($enrollment);
        abort_if($modules->isEmpty(), 404);

        $flat = $this->flattenLessons($modules);
        $lastId = $enrollment->last_accessed_course_lesson_id;
        if ($lastId && Schema::hasColumn('enrollments', 'last_accessed_course_lesson_id')) {
            $last = CourseLesson::with('module')->find($lastId);
            if ($last && $last->is_active && $last->module?->is_active
                && (int) $last->module->course_id === (int) $course->id
                && $flat->contains(fn ($l) => (int) $l->id === (int) $last->id)) {
                return redirect()->route('student.learn.lesson', [$enrollment, $last]);
            }

            $enrollment->update(['last_accessed_course_lesson_id' => null]);
        }

        return redirect()->route('student.learn.outline', $enrollment);
    }

    public function outline(Enrollment $enrollment)
    {
        if (! Schema::hasTable('course_modules')) {
            abort(404, 'Learning content is not available yet.');
        }

        $student = Auth::user()->student;
        abort_unless($student && (int) $enrollment->student_id === (int) $student->id, 403);

        [$course, $modules] = $this->courseAndModules($enrollment);
        abort_if($modules->isEmpty(), 404);

        $progress = $enrollment->lmsProgressForCourse($course);
        $checklist = $enrollment->exam_eligibility_checklist;
        $completedLessonIds = $enrollment->lessonCompletions()->pluck('course_lesson_id')->flip();

        $resumeLesson = null;
        if (Schema::hasColumn('enrollments', 'last_accessed_course_lesson_id') && $enrollment->last_accessed_course_lesson_id) {
            $candidate = CourseLesson::with('module')->find($enrollment->last_accessed_course_lesson_id);
            $flat = $this->flattenLessons($modules);
            if ($candidate && $candidate->is_active && $candidate->module?->is_active
                && (int) $candidate->module->course_id === (int) $course->id
                && $flat->contains(fn ($l) => (int) $l->id === (int) $candidate->id)) {
                $resumeLesson = $candidate;
            } else {
                $enrollment->update(['last_accessed_course_lesson_id' => null]);
            }
        }

        return view('student.learn.outline', compact(
            'enrollment',
            'course',
            'modules',
            'progress',
            'checklist',
            'completedLessonIds',
            'resumeLesson'
        ));
    }

    public function lesson(Enrollment $enrollment, CourseLesson $lesson)
    {
        if (! Schema::hasTable('course_lessons')) {
            abort(404);
        }

        $student = Auth::user()->student;
        abort_unless($student && (int) $enrollment->student_id === (int) $student->id, 403);

        [$course, $modules] = $this->courseAndModules($enrollment);
        $this->assertLessonBelongsToEnrollmentCourse($lesson, $course);

        if (Schema::hasColumn('enrollments', 'last_accessed_course_lesson_id')) {
            $enrollment->update(['last_accessed_course_lesson_id' => $lesson->id]);
        }

        $progress = $enrollment->lmsProgressForCourse($course);
        $checklist = $enrollment->exam_eligibility_checklist;
        $isLessonComplete = $enrollment->lessonCompletions()
            ->where('course_lesson_id', $lesson->id)
            ->exists();

        return view('student.learn.lesson', compact(
            'enrollment',
            'course',
            'modules',
            'lesson',
            'progress',
            'checklist',
            'isLessonComplete'
        ));
    }

    public function completeLesson(Enrollment $enrollment, CourseLesson $lesson)
    {
        if (! Schema::hasTable('enrollment_lesson_completions')) {
            abort(404);
        }

        $student = Auth::user()->student;
        abort_unless($student && (int) $enrollment->student_id === (int) $student->id, 403);

        [$course] = $this->courseAndModules($enrollment);
        $this->assertLessonBelongsToEnrollmentCourse($lesson, $course);

        EnrollmentLessonCompletion::firstOrCreate([
            'enrollment_id' => $enrollment->id,
            'course_lesson_id' => $lesson->id,
        ]);

        return redirect()
            ->route('student.learn.lesson', [$enrollment, $lesson])
            ->with('success', 'Lesson marked complete.');
    }

    /**
     * Marks the current lesson complete and opens the next one (progress is persisted).
     */
    public function completeAndGoToNext(Enrollment $enrollment, CourseLesson $lesson, CourseLesson $next)
    {
        if (! Schema::hasTable('enrollment_lesson_completions')) {
            abort(404);
        }

        $student = Auth::user()->student;
        abort_unless($student && (int) $enrollment->student_id === (int) $student->id, 403);

        [$course, $modules] = $this->courseAndModules($enrollment);
        $this->assertLessonBelongsToEnrollmentCourse($lesson, $course);
        $this->assertLessonBelongsToEnrollmentCourse($next, $course);

        $flat = $this->flattenLessons($modules);
        $idx = $flat->search(fn ($l) => (int) $l->id === (int) $lesson->id);
        abort_unless($idx !== false, 404);
        $expectedNext = $flat->get($idx + 1);
        abort_unless($expectedNext && (int) $expectedNext->id === (int) $next->id, 404);

        EnrollmentLessonCompletion::firstOrCreate([
            'enrollment_id' => $enrollment->id,
            'course_lesson_id' => $lesson->id,
        ]);

        if (Schema::hasColumn('enrollments', 'last_accessed_course_lesson_id')) {
            $enrollment->update(['last_accessed_course_lesson_id' => $next->id]);
        }

        return redirect()->route('student.learn.lesson', [$enrollment, $next]);
    }

    /**
     * @return array{0: \App\Models\Course, 1: Collection}
     */
    private function courseAndModules(Enrollment $enrollment): array
    {
        $course = $enrollment->course;
        abort_unless($course, 404);

        $modules = $course->learningModules()
            ->where('is_active', true)
            ->with(['lessons' => function ($q) {
                $q->where('is_active', true)->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        return [$course, $modules];
    }

    private function flattenLessons(Collection $modules): Collection
    {
        $flat = collect();
        foreach ($modules as $module) {
            foreach ($module->lessons as $l) {
                $flat->push($l);
            }
        }

        return $flat;
    }

    private function assertLessonBelongsToEnrollmentCourse(CourseLesson $lesson, $course): void
    {
        $lesson->loadMissing('module');
        abort_unless($lesson->module, 404);
        abort_unless((int) $lesson->module->course_id === (int) $course->id, 404);
        abort_unless($lesson->is_active && $lesson->module->is_active, 404);
    }
}
