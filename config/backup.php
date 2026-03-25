<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Remote disk for scheduled backups
    |--------------------------------------------------------------------------
    |
    | After a local dump, the file is copied to this Laravel filesystem disk
    | (e.g. "s3" from config/filesystems.php). Leave null to keep local only.
    |
    */
    'remote_disk' => env('BACKUP_REMOTE_DISK'),

    'include_public_storage' => filter_var(env('BACKUP_INCLUDE_PUBLIC_STORAGE', false), FILTER_VALIDATE_BOOLEAN),

];
