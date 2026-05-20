<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'ams' => [
        'api_url' => env('AMS_API_URL', 'https://ams.softpromis.com/api/income/from-sms'),
        'api_key' => env('AMS_API_KEY'),
        // Softpro HO project, Student (Income) category, Student Fees subcategory
        'project_id' => env('AMS_PROJECT_ID', 1),
        'category_id' => env('AMS_CATEGORY_ID', 13),   // Student income
        'subcategory_id' => env('AMS_SUBCATEGORY_ID', 62), // Student Fees (id=62)
        'user_id' => env('AMS_USER_ID', 2),
    ],

    'whatsapp' => [
        'api_url' => env('WHATSAPP_API_URL', 'https://graph.facebook.com/v17.0'),
        'access_token' => env('WHATSAPP_ACCESS_TOKEN'),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
        /** App Secret from Meta app (Basic settings); used for X-Hub-Signature-256 on inbound webhooks. */
        'app_secret' => env('WHATSAPP_APP_SECRET'),
        'webhook_verify_token' => env('WHATSAPP_WEBHOOK_VERIFY_TOKEN'),
        /** When an inbound number does not match a student, tag the thread to this TP so centre staff can see it. */
        'inbox_default_training_partner_id' => env('WHATSAPP_INBOX_DEFAULT_TRAINING_PARTNER_ID'),
        /** Free-text replies allowed only within this many hours after the customer’s last inbound message (WhatsApp policy). */
        'inbox_freeform_reply_hours' => env('WHATSAPP_INBOX_FREEFORM_REPLY_HOURS', 24),
        'template_language' => env('WHATSAPP_TEMPLATE_LANGUAGE', 'en_US'),
        // When true: pass empty string for button URL (template has full URL as base, {{1}} is suffix)
        // When false: pass full URL (template has {{1}} as whole URL)
        'button_url_empty_suffix' => env('WHATSAPP_BUTTON_URL_EMPTY_SUFFIX', true),
        // When false: send params without parameter_name (for templates using {{1}},{{2}})
        'use_parameter_names' => env('WHATSAPP_USE_PARAMETER_NAMES', true),
    ],

    'webpush' => [
        'subject' => env('WEBPUSH_VAPID_SUBJECT'),
        'public_key' => env('WEBPUSH_VAPID_PUBLIC_KEY'),
        'private_key' => env('WEBPUSH_VAPID_PRIVATE_KEY'),
    ],

];
