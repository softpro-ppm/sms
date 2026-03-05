<?php

namespace App\Services;

use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;

class EmailTemplateService
{
    public function getSubject(string $slug, array $data = []): string
    {
        $template = EmailTemplate::where('slug', $slug)->first();
        if (!$template) {
            return '';
        }

        try {
            return trim(Blade::render($template->subject, $data));
        } catch (\Throwable $e) {
            return $template->subject;
        }
    }

    public function getHtml(string $slug, array $data = []): string
    {
        $template = EmailTemplate::where('slug', $slug)->first();
        if (!$template || (!$template->header_html && !$template->body_html)) {
            return '';
        }

        $headerHtml = $template->header_html ?? '';
        $bodyHtml = $template->body_html ?? '';

        try {
            $headerRendered = Blade::render($headerHtml, $data);
            $bodyRendered = Blade::render($bodyHtml, $data);
        } catch (\Throwable $e) {
            $headerRendered = $headerHtml;
            $bodyRendered = $bodyHtml;
        }

        return View::make('emails.layouts.softpro-custom')
            ->with('header', $headerRendered)
            ->with('content', $bodyRendered)
            ->render();
    }

    public function hasCustomTemplate(string $slug): bool
    {
        $template = EmailTemplate::where('slug', $slug)->first();
        return $template && ($template->header_html || $template->body_html);
    }
}
