<?php

namespace App\Console\Commands;

use App\Models\StudentDocument;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DiagnoseDocumentsCommand extends Command
{
    protected $signature = 'documents:diagnose {--student= : Student ID to check}';

    protected $description = 'Diagnose document storage - check if files exist for student documents';

    public function handle(): int
    {
        $studentId = $this->option('student');
        $query = StudentDocument::query();
        if ($studentId) {
            $query->where('student_id', $studentId);
        }
        $documents = $query->get();

        $this->info('Storage root: ' . Storage::disk('public')->path(''));
        $this->info('');

        if ($documents->isEmpty()) {
            $this->warn('No documents found.');
            return 0;
        }

        $exists = 0;
        $missing = 0;
        $rows = [];

        foreach ($documents as $doc) {
            $viaStorage = Storage::disk('public')->exists($doc->file_path);
            $fallbackPath = storage_path('app/public/' . ltrim($doc->file_path, '/'));
            $viaFallback = file_exists($fallbackPath);

            $found = $viaStorage || $viaFallback;
            if ($found) {
                $exists++;
            } else {
                $missing++;
            }

            $rows[] = [
                $doc->id,
                $doc->student_id,
                $doc->document_type,
                $doc->file_path,
                $found ? '✓' : '✗',
            ];
        }

        $this->table(['ID', 'Student', 'Type', 'Path', 'Exists'], $rows);
        $this->info("");
        $this->info("Found: {$exists} | Missing: {$missing}");

        if ($missing > 0) {
            $this->warn('Missing files may need to be re-uploaded. Check storage/app/public/student-documents/ on server.');
        }

        return 0;
    }
}
