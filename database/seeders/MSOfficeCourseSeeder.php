<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseModule;
use App\Models\TrainingPartner;
use Database\Seeders\Data\MSOfficeCurriculum;
use Illuminate\Database\Seeder;

class MSOfficeCourseSeeder extends Seeder
{
    /**
     * Seeds the full MS Office LMS (modules + HTML lessons) onto the "MS Office" catalogue course.
     * Safe to run once; skips if that course already has modules (delete modules first to re-seed).
     */
    public function run(): void
    {
        $tp = TrainingPartner::query()->orderBy('id')->first();

        $course = Course::query()->where('name', 'MS Office')->first();
        if (!$course) {
            $course = Course::create([
                'training_partner_id' => $tp?->id,
                'name' => 'MS Office',
                'description' => 'Microsoft Word, Excel, PowerPoint, and Outlook — self-paced LMS lessons for institute students.',
                'course_fee' => 0,
                'registration_fee' => 0,
                'assessment_fee' => 0,
                'duration_days' => 45,
                'is_active' => true,
            ]);
        }

        if ($course->learningModules()->exists()) {
            if ($this->command) {
                $this->command->info('Course "MS Office" already has learning modules. Skip (delete modules to re-seed).');
            }

            return;
        }

        foreach (MSOfficeCurriculum::modules() as $mIdx => $moduleData) {
            $module = CourseModule::create([
                'course_id' => $course->id,
                'title' => $moduleData['title'],
                'summary' => $moduleData['summary'],
                'sort_order' => $mIdx,
                'is_active' => true,
            ]);

            foreach ($moduleData['lessons'] as $lIdx => $lessonData) {
                CourseLesson::create([
                    'course_module_id' => $module->id,
                    'title' => $lessonData['title'],
                    'lesson_type' => $lessonData['type'] ?? 'article',
                    'body' => $lessonData['body'],
                    'video_url' => $lessonData['video_url'] ?? null,
                    'estimated_minutes' => $lessonData['minutes'] ?? null,
                    'sort_order' => $lIdx,
                    'is_active' => true,
                ]);
            }
        }

        if ($this->command) {
            $this->command->info('MS Office LMS seeded on course id '.$course->id.' ('.$course->learningModules()->count().' modules).');
        }
    }
}
