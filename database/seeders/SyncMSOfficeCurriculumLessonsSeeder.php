<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseModule;
use Database\Seeders\Data\MSOfficeCurriculum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

/**
 * Pushes lesson titles, bodies, and durations from MSOfficeCurriculum into the DB.
 * Skips Module 1 (index 0) so centre/super-admin manual edits there stay intact.
 */
class SyncMSOfficeCurriculumLessonsSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('course_modules') || ! Schema::hasTable('course_lessons')) {
            $this->command?->warn('course_modules / course_lessons missing — run migrations first.');

            return;
        }

        $normalizedAdvanced = 'ms office advanced';
        $course = Course::query()
            ->whereRaw('LOWER(TRIM(name)) = ?', [$normalizedAdvanced])
            ->orderByDesc('id')
            ->first();

        if (! $course) {
            $this->command?->warn('No course named "MS Office Advanced" found. Skipping sync.');

            return;
        }

        $modules = MSOfficeCurriculum::modules();
        foreach ($modules as $mIdx => $moduleData) {
            if ($mIdx === 0) {
                continue;
            }

            $module = CourseModule::query()
                ->where('course_id', $course->id)
                ->where('title', $moduleData['title'])
                ->first();

            if (! $module) {
                $this->command?->warn('Module not found (skipped): '.$moduleData['title']);

                continue;
            }

            foreach ($moduleData['lessons'] as $lIdx => $lessonData) {
                $lesson = CourseLesson::query()
                    ->where('course_module_id', $module->id)
                    ->where('title', $lessonData['title'])
                    ->first();

                if (! $lesson) {
                    $lesson = CourseLesson::query()
                        ->where('course_module_id', $module->id)
                        ->where('sort_order', $lIdx)
                        ->first();
                }

                if (! $lesson) {
                    $this->command?->warn('Lesson not found (skipped): '.$moduleData['title'].' → '.$lessonData['title']);

                    continue;
                }

                $lesson->update([
                    'title' => $lessonData['title'],
                    'lesson_type' => $lessonData['type'] ?? 'article',
                    'body' => $lessonData['body'],
                    'video_url' => $lessonData['video_url'] ?? null,
                    'estimated_minutes' => $lessonData['minutes'] ?? null,
                ]);
            }
        }

        $this->command?->info('Synced LMS lesson content from MSOfficeCurriculum for "'.$course->name.'" (all modules except Module 1).');
    }
}
