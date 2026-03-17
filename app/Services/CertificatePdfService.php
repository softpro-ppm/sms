<?php

namespace App\Services;

use setasign\Fpdi\Fpdi;

class CertificatePdfService
{
    /**
     * Extract only the first page from a PDF string.
     * DomPDF sometimes adds a blank second page; this ensures single-page output.
     */
    public static function keepFirstPageOnly(string $pdfContent): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'cert_pdf_');
        try {
            file_put_contents($tempFile, $pdfContent);

            $fpdi = new Fpdi();
            $pageCount = $fpdi->setSourceFile($tempFile);

            if ($pageCount <= 1) {
                return $pdfContent;
            }

            $tplId = $fpdi->importPage(1);
            $size = $fpdi->getTemplateSize($tplId);
            $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $fpdi->useTemplate($tplId);

            $output = $fpdi->Output('S');

            return $output;
        } finally {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }
}
