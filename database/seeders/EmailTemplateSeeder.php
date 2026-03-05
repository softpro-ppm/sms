<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        foreach (EmailTemplate::getDefaultTemplates() as $slug => $data) {
            EmailTemplate::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $data['name'],
                    'subject' => $data['subject'],
                    'header_html' => $data['header_html'],
                    'body_html' => $data['body_html'],
                    'variables' => $data['variables'] ?? null,
                ]
            );
        }
    }
}
