<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CourseLesson;
use App\Models\Enrollment;
use App\Models\EnrollmentLessonCompletion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class StudentCourseLearningController extends Controller
{
    public function outline(Enrollment $enrollment)
    {
        if (! Schema::hasTable('course_modules')) {
            abort(404, 'Learning content is not available yet.');
        }

        $student = Auth::user()->student;
        abort_unless($student && (int) $enrollment->student_id === (int) $student->id, 403);

        $course = $enrollment->course;
        abort_unless($course, 404);

        $modules = $course->learningModules()
            ->where('is_active', true)
            ->with(['lessons' => function ($q) {
                $q->where('is_active', true)->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        abort_if($modules->isEmpty(), 404);

        $progress = $enrollment->lmsProgressForCourse($course);
        $checklist = $enrollment->exam_eligibility_checklist;
        $completedLessonIds = $enrollment->lessonCompletions()->pluck('course_lesson_id')->flip();

        return view('student.learn.outline', compact(
            'enrollment',
            'course',
            'modules',
            'progress',
            'checklist',
            'completedLessonIds'
        ));
    }

    public function lesson(Enrollment $enrollment, CourseLesson $lesson)
    {
        if (! Schema::hasTable('course_lessons')) {
            abort(404);
        }

        $student = Auth::user()->student;
        abort_unless($student && (int) $enrollment->student_id === (int) $student->id, 403);

        $course = $enrollment->course;
        abort_unless($course, 404);

        abort_unless((int) $lesson->module->course_id === (int) $course->id, 404);
        abort_unless($lesson->is_active && $lesson->module->is_active, 404);

        $modules = $course->learningModules()
            ->where('is_active', true)
            ->with(['lessons' => function ($q) {
                $q->where('is_active', true)->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

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

        $course = $enrollment->course;
        abort_unless($course, 404);

        abort_unless((int) $lesson->module->course_id === (int) $course->id, 404);
        abort_unless($lesson->is_active && $lesson->module->is_active, 404);

        EnrollmentLessonCompletion::firstOrCreate([
            'enrollment_id' => $enrollment->id,
            'course_lesson_id' => $lesson->id,
        ]);

        return redirect()
            ->route('student.learn.lesson', [$enrollment, $lesson])
            ->with('success', 'Lesson marked complete.');
    }
}
