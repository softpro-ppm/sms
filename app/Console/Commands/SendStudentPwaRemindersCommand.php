<?php

namespace App\Console\Commands;

use App\Models\Enrollment;
use App\Services\StudentPushNotificationService;
use Illuminate\Console\Command;

class SendStudentPwaRemindersCommand extends Command
{
    protected $signature = 'student:pwa-reminders';

    protected $description = 'Send fee due and exam ready reminders to subscribed student PWAs';

    public function handle(StudentPushNotificationService $pushNotifications): int
    {
        $enrollments = Enrollment::query()
            ->with(['student.user', 'student.pushSubscriptions', 'batch.course', 'legacyLinkCourse'])
            ->where('status', 'active')
            ->get();

        $feeDueCount = 0;
        $examReadyCount = 0;

        foreach ($enrollments as $enrollment) {
            if (! $enrollment->student?->user?->is_active) {
                continue;
            }

            if ((float) ($enrollment->outstanding_amount ?? 0) > 0) {
                $pushNotifications->sendFeeDueReminder($enrollment);
                $feeDueCount++;
            }

            if ($enrollment->can_take_assessment) {
                $pushNotifications->sendExamReadyReminder($enrollment);
                $examReadyCount++;
            }
        }

        $this->info("Processed student PWA reminders. Fee due: {$feeDueCount}, exam ready: {$examReadyCount}");

        return self::SUCCESS;
    }
}
