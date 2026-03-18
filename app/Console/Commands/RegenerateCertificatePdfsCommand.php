<?php

namespace App\Console\Commands;

use App\Models\Certificate;
use App\Services\CertificatePdfService;
use App\Services\CertificateTemplateService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RegenerateCertificatePdfsCommand extends Command
{
    protected $signature = 'certificates:regenerate-pdfs 
        {--certificate= : ID of a specific certificate to regenerate}
        {--all : Regenerate all issued certificates}';

    protected $description = 'Regenerate and store PDFs for certificates (fixes 504 timeout for certificates without stored PDF)';

    public function handle(): int
    {
        $certId = $this->option('certificate');
        $all = $this->option('all');

        if (!$certId && !$all) {
            $this->error('Provide --certificate=ID or --all');
            return 1;
        }

        $query = Certificate::where('is_issued', true)
            ->whereNotNull('certificate_number')
            ->with(['student', 'course', 'batch', 'assessmentResult']);

        if ($certId) {
            $query->where('id', $certId);
        }

        $certificates = $query->get();

        if ($certificates->isEmpty()) {
            $this->warn('No certificates found.');
            return 0;
        }

        $templateService = app(CertificateTemplateService::class);
        $bar = $this->output->createProgressBar($certificates->count());
        $bar->start();

        foreach ($certificates as $certificate) {
            $filePath = $certificate->certificate_file_path;
            if ($filePath && str_ends_with($filePath, '.pdf') && Storage::exists($filePath)) {
                $bar->advance();
                continue;
            }

            try {
                $html = $templateService->generateHtml($certificate);

                $pdf = app('dompdf.wrapper');
                $pdf->getDomPDF()->getOptions()->setDefaultMediaType('print');
                $pdf->loadHTML($html);
                $pdf->setPaper('a4', 'landscape');

                $pdfContent = $pdf->output();
                $pdfContent = CertificatePdfService::keepFirstPageOnly($pdfContent);

                $newPath = 'certificates/certificate_' . $certificate->certificate_number . '.pdf';
                Storage::put($newPath, $pdfContent);
                $certificate->update(['certificate_file_path' => $newPath]);

                $this->line('');
                $this->info("Regenerated: Certificate #{$certificate->id} ({$certificate->certificate_number})");
            } catch (\Throwable $e) {
                $this->line('');
                $this->error("Failed certificate #{$certificate->id}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Done.');

        return 0;
    }
}
