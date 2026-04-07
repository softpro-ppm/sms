<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseModule;
use App\Models\TrainingPartner;
use Database\Seeders\Data\TallyErp9Curriculum;
use Illuminate\Database\Seeder;

class TallyErp9CourseSeeder extends Seeder
{
    /**
     * Seeds LMS modules/lessons onto the catalogue course "Tally ERP 9 Advanced"
     * (case-insensitive name match). If a plain "Tally ERP 9" course holds modules
     * and Advanced has none, those modules are moved onto Advanced first.
     */
    public function run(): void
    {
        $tp = TrainingPartner::query()->orderBy('id')->first();

        $normalizedAdvanced = 'tally erp 9 advanced';
        $normalizedBasic = 'tally erp 9';

        $course = Course::query()
            ->whereRaw('LOWER(TRIM(name)) = ?', [$normalizedAdvanced])
            ->withCount('enrollments')
            ->orderByDesc('enrollments_count')
            ->orderBy('id')
            ->first();

        if (! $course) {
            $course = Course::create([
                'training_partner_id' => $tp?->id,
                'name' => 'Tally ERP 9 Advanced',
                'description' => 'Tally ERP 9 accounting, GST, inventory, and reports — self-paced LMS lessons for institute students.',
                'course_fee' => 0,
                'registration_fee' => 0,
                'assessment_fee' => 0,
                'duration_days' => 45,
                'is_active' => true,
            ]);
        }

        $basicCourseIds = Course::query()
            ->whereRaw('LOWER(TRIM(name)) = ?', [$normalizedBasic])
            ->where('id', '!=', $course->id)
            ->pluck('id');

        $relocatedModules = false;
        if ($basicCourseIds->isNotEmpty() && ! $course->learningModules()->exists()) {
            $moved = CourseModule::query()->whereIn('course_id', $basicCourseIds)->update(['course_id' => $course->id]);
            if ($moved > 0) {
                $course->unsetRelation('learningModules');
                $relocatedModules = true;
                if ($this->command) {
                    $this->command->info('Moved '.$moved.' LMS module row(s) from basic Tally ERP 9 course(s) onto "'.$course->name.'" (id '.$course->id.').');
                }
            }
        }

        if ($course->learningModules()->exists()) {
            if ($this->command) {
                $moduleCount = $course->learningModules()->count();
                if ($relocatedModules) {
                    $this->command->info('LMS is mapped to "'.$course->name.'" (id '.$course->id.') — '.$moduleCount.' module(s).');
                } else {
                    $this->command->info('Course "'.$course->name.'" (id '.$course->id.') already has learning modules. Skip (delete modules to re-seed).');
                }
            }

            return;
        }

        foreach (TallyErp9Curriculum::modules() as $mIdx => $moduleData) {
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
            $this->command->info('Tally ERP 9 Advanced LMS seeded on course id '.$course->id.' ('.$course->learningModules()->count().' modules).');
        }
    }
}
