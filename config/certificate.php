<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Certificate ISO Text
    |--------------------------------------------------------------------------
    |
    | Text displayed on certificate footer (e.g. "AN ISO 9001:2015 CERTIFIED ORGANIZATION").
    | Set via CERTIFICATE_ISO_TEXT in .env or leave default.
    |
    */

    'iso_text' => env('CERTIFICATE_ISO_TEXT', 'AN ISO 9001:2015 CERTIFIED ORGANIZATION'),

    /*
    |--------------------------------------------------------------------------
    | Certificate Title
    |--------------------------------------------------------------------------
    |
    | Main title on certificate (e.g. "CERTIFICATE OF COMPLETION").
    |
    */

    'title' => env('CERTIFICATE_TITLE', 'CERTIFICATE OF COMPLETION'),

    /*
    |--------------------------------------------------------------------------
    | Show QR Code
    |--------------------------------------------------------------------------
    |
    | Whether to show QR code for verification on certificate.
    |
    */

    'show_qr_code' => env('CERTIFICATE_SHOW_QR', true),

];
