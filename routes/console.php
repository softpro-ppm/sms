<?php

use App\Models\Certificate;
use App\Services\CertificateTemplateService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('certificates:refresh-rendered-html', function () {
    $templateService = app(CertificateTemplateService::class);

    $certificates = Certificate::query()
        ->where('is_issued', true)
        ->whereNotNull('certificate_number')
        ->orderBy('id')
        ->get();

    $updated = 0;

    foreach ($certificates as $certificate) {
        $htmlContent = $templateService->generateHtml($certificate);
        $fileName = 'certificate_'.$certificate->certificate_number.'.html';
        $filePath = 'certificates/'.$fileName;

        Storage::put($filePath, $htmlContent);

        if ($certificate->certificate_file_path !== $filePath) {
            $certificate->update(['certificate_file_path' => $filePath]);
        }

        $updated++;
    }

    $this->info("Refreshed {$updated} issued certificate file(s).");
})->purpose('Rebuild stored issued certificate HTML files from the current certificate template');

Schedule::command('sms:backup-scheduled')->dailyAt('03:00');
Schedule::command('student:pwa-reminders')->twiceDaily(9, 18);
