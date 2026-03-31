<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseModule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class CourseLearningManageController extends Controller
{
    protected function ensureLearningTables(): void
    {
        if (! Schema::hasTable('course_modules') || ! Schema::hasTable('course_lessons')) {
            abort(503, 'Run migrations to enable course learning.');
        }
    }

    protected function moduleBelongsToCourse(Course $course, CourseModule $module): void
    {
        if ((int) $module->course_id !== (int) $course->id) {
            abort(404);
        }
    }

    protected function lessonBelongsToModule(CourseModule $module, CourseLesson $lesson): void
    {
        if ((int) $lesson->course_module_id !== (int) $module->id) {
            abort(404);
        }
    }

    public function createModule(Course $course): View
    {
        $this->ensureLearningTables();

        $nextOrder = (int) ($course->learningModules()->max('sort_order') ?? -1) + 1;
        $module = new CourseModule([
            'course_id' => $course->id,
            'title' => '',
            'summary' => null,
            'sort_order' => $nextOrder,
            'is_active' => true,
        ]);

        return view('admin.courses.learning.module-form', [
            'course' => $course,
            'module' => $module,
        ]);
    }

    public function storeModule(Request $request, Course $course): RedirectResponse
    {
        $this->ensureLearningTables();

        $data = $this->validatedModule($request);
        $course->learningModules()->create($data);

        return redirect()
            ->route('admin.courses.learning', $course)
            ->with('success', 'Module created.');
    }

    public function editModule(Course $course, CourseModule $module): View
    {
        $this->ensureLearningTables();
        $this->moduleBelongsToCourse($course, $module);

        return view('admin.courses.learning.module-form', [
            'course' => $course,
            'module' => $module,
        ]);
    }

    public function updateModule(Request $request, Course $course, CourseModule $module): RedirectResponse
    {
        $this->ensureLearningTables();
        $this->moduleBelongsToCourse($course, $module);

        $module->update($this->validatedModule($request));

        return redirect()
            ->route('admin.courses.learning', $course)
            ->with('success', 'Module updated.');
    }

    public function destroyModule(Course $course, CourseModule $module): RedirectResponse
    {
        $this->ensureLearningTables();
        $this->moduleBelongsToCourse($course, $module);

        $module->delete();

        return redirect()
            ->route('admin.courses.learning', $course)
            ->with('success', 'Module and its lessons were removed.');
    }

    public function createLesson(Course $course, CourseModule $module): View
    {
        $this->ensureLearningTables();
        $this->moduleBelongsToCourse($course, $module);

        $nextOrder = (int) ($module->lessons()->max('sort_order') ?? -1) + 1;
        $lesson = new CourseLesson([
            'course_module_id' => $module->id,
            'title' => '',
            'lesson_type' => 'article',
            'body' => null,
            'video_url' => null,
            'estimated_minutes' => null,
            'sort_order' => $nextOrder,
            'is_active' => true,
        ]);

        return view('admin.courses.learning.lesson-form', [
            'course' => $course,
            'module' => $module,
            'lesson' => $lesson,
        ]);
    }

    public function storeLesson(Request $request, Course $course, CourseModule $module): RedirectResponse
    {
        $this->ensureLearningTables();
        $this->moduleBelongsToCourse($course, $module);

        $module->lessons()->create($this->validatedLesson($request));

        return redirect()
            ->route('admin.courses.learning', $course)
            ->with('success', 'Lesson created.');
    }

    public function editLesson(Course $course, CourseModule $module, CourseLesson $lesson): View
    {
        $this->ensureLearningTables();
        $this->moduleBelongsToCourse($course, $module);
        $this->lessonBelongsToModule($module, $lesson);

        return view('admin.courses.learning.lesson-form', [
            'course' => $course,
            'module' => $module,
            'lesson' => $lesson,
        ]);
    }

    public function updateLesson(Request $request, Course $course, CourseModule $module, CourseLesson $lesson): RedirectResponse
    {
        $this->ensureLearningTables();
        $this->moduleBelongsToCourse($course, $module);
        $this->lessonBelongsToModule($module, $lesson);

        $lesson->update($this->validatedLesson($request));

        return redirect()
            ->route('admin.courses.learning', $course)
            ->with('success', 'Lesson updated.');
    }

    public function destroyLesson(Course $course, CourseModule $module, CourseLesson $lesson): RedirectResponse
    {
        $this->ensureLearningTables();
        $this->moduleBelongsToCourse($course, $module);
        $this->lessonBelongsToModule($module, $lesson);

        $lesson->delete();

        return redirect()
            ->route('admin.courses.learning', $course)
            ->with('success', 'Lesson removed.');
    }

    /**
     * @return array<string, mixed>
     */
    protected function validatedModule(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validator->validate();

        return [
            'title' => $request->title,
            'summary' => $request->input('summary') ?: null,
            'sort_order' => (int) ($request->input('sort_order') ?? 0),
            'is_active' => $request->boolean('is_active'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function validatedLesson(Request $request): array
    {
        $type = $request->input('lesson_type', 'article');

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'lesson_type' => ['required', 'in:article,video_link'],
            'body' => ['nullable', 'string'],
            'video_url' => ['nullable', 'string', 'max:2048'],
            'estimated_minutes' => ['nullable', 'integer', 'min:0', 'max:2000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];

        if ($type === 'video_link') {
            $rules['video_url'] = ['required', 'string', 'max:2048'];
        }

        $validator = Validator::make($request->all(), $rules);
        $validator->validate();

        return [
            'title' => $request->title,
            'lesson_type' => $type,
            'body' => $request->input('body') ?: null,
            'video_url' => $request->input('video_url') ?: null,
            'estimated_minutes' => $request->filled('estimated_minutes') ? (int) $request->input('estimated_minutes') : null,
            'sort_order' => (int) ($request->input('sort_order') ?? 0),
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
