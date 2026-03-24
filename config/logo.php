<?php

/**
 * Logo upload norms for institute/partner logos.
 * Applied to Partner Registration and Super Admin TP create/edit.
 */
return [
    'max_size_kb' => (int) env('LOGO_MAX_SIZE_KB', 512),
    'mimes' => ['jpeg', 'png', 'jpg'],
    'max_dimension' => env('LOGO_MAX_DIMENSION', 1024), // max width or height in pixels
];
