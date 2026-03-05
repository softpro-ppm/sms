<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::orderBy('slug')->get();

        // Ensure all 8 defaults exist
        $defaults = EmailTemplate::getDefaultTemplates();
        foreach (array_keys($defaults) as $slug) {
            if (!$templates->contains('slug', $slug)) {
                $data = $defaults[$slug];
                EmailTemplate::create([
                    'slug' => $slug,
                    'name' => $data['name'],
                    'subject' => $data['subject'],
                    'header_html' => $data['header_html'],
                    'body_html' => $data['body_html'],
                    'variables' => $data['variables'] ?? null,
                ]);
            }
        }

        $templates = EmailTemplate::orderBy('slug')->get();

        return view('admin.settings.email-templates.index', compact('templates'));
    }

    public function edit(EmailTemplate $emailTemplate)
    {
        $defaults = EmailTemplate::getDefaultTemplates();
        $default = $defaults[$emailTemplate->slug] ?? null;

        return view('admin.settings.email-templates.edit', [
            'emailTemplate' => $emailTemplate,
            'defaultHeader' => $emailTemplate->header_html ?? ($default['header_html'] ?? ''),
            'defaultBody' => $emailTemplate->body_html ?? ($default['body_html'] ?? ''),
        ]);
    }

    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'header_html' => 'nullable|string',
            'body_html' => 'nullable|string',
        ]);

        $emailTemplate->update([
            'subject' => $request->subject,
            'header_html' => $request->header_html ?: null,
            'body_html' => $request->body_html ?: null,
        ]);

        return redirect()->route('admin.settings.email-templates.index')
            ->with('success', "Template '{$emailTemplate->name}' updated successfully.");
    }

    public function reset(EmailTemplate $emailTemplate)
    {
        $defaults = EmailTemplate::getDefaultTemplates();
        $slug = $emailTemplate->slug;

        if (!isset($defaults[$slug])) {
            return redirect()->back()->with('error', 'Default template not found.');
        }

        $data = $defaults[$slug];
        $emailTemplate->update([
            'subject' => $data['subject'],
            'header_html' => $data['header_html'],
            'body_html' => $data['body_html'],
        ]);

        return redirect()->route('admin.settings.email-templates.edit', $emailTemplate)
            ->with('success', 'Template reset to default.');
    }
}
