<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Student;
use App\Models\TrainingPartner;
use Illuminate\Support\Str;

class CertificateNumberService
{
    public function generateForStudent(Student $student): string
    {
        $student->loadMissing('trainingPartner');

        return $this->generate($student->trainingPartner);
    }

    public function generate(?TrainingPartner $trainingPartner = null): string
    {
        $partnerCode = $this->partnerCode($trainingPartner);
        $period = now()->format('Ym');
        $prefix = "CERT-{$partnerCode}-{$period}-";

        $lastCertificate = Certificate::query()
            ->where('certificate_number', 'like', $prefix.'%')
            ->orderByDesc('certificate_number')
            ->first();

        $nextNumber = $lastCertificate
            ? ((int) substr((string) $lastCertificate->certificate_number, -4)) + 1
            : 1;

        return $prefix.str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    private function partnerCode(?TrainingPartner $trainingPartner): string
    {
        $code = $trainingPartner?->code ?: 'HQ';
        $code = Str::upper(Str::slug($code, ''));

        return $code !== '' ? $code : 'HQ';
    }
}
