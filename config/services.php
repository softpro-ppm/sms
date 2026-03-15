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
        'category_id' => env('AMS_CATEGORY_ID', 2),   // Parent of Student Fees
        'subcategory_id' => env('AMS_SUBCATEGORY_ID', 13), // Student Fees
        'user_id' => env('AMS_USER_ID', 2),
    ],

    'whatsapp' => [
        'api_url' => env('WHATSAPP_API_URL', 'https://graph.facebook.com/v17.0'),
        'access_token' => env('WHATSAPP_ACCESS_TOKEN'),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
        'template_language' => env('WHATSAPP_TEMPLATE_LANGUAGE', 'en_US'),
        // When true: pass empty string for button URL (template has full URL as base, {{1}} is suffix)
        // When false: pass full URL (template has {{1}} as whole URL)
        'button_url_empty_suffix' => env('WHATSAPP_BUTTON_URL_EMPTY_SUFFIX', true),
        // When false: send params without parameter_name (for templates using {{1}},{{2}})
        'use_parameter_names' => env('WHATSAPP_USE_PARAMETER_NAMES', true),
    ],

];
