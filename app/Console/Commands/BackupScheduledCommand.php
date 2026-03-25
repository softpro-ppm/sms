<?php

namespace App\Console\Commands;

use App\Services\DatabaseBackupService;
use Illuminate\Console\Command;

class BackupScheduledCommand extends Command
{
    protected $signature = 'sms:backup-scheduled';

    protected $description = 'Create database (and optional public storage) backups; optionally upload to BACKUP_REMOTE_DISK';

    public function handle(DatabaseBackupService $backup): int
    {
        $error = null;
        $sqlPath = $backup->createMysqlDump($error);
        if ($sqlPath === null) {
            $this->error($error ?? 'Database backup failed.');

            return self::FAILURE;
        }
        $this->info('Database backup: ' . $sqlPath);

        $remote = config('backup.remote_disk');
        if ($remote) {
            if ($backup->copyToRemoteDisk($sqlPath, $remote)) {
                $this->info('Uploaded SQL to disk: ' . $remote);
            } else {
                $this->warn('Could not upload SQL to remote disk: ' . $remote);
            }
        }

        if (config('backup.include_public_storage')) {
            $zipError = null;
            $zipPath = $backup->zipPublicStorage($zipError);
            if ($zipPath) {
                $this->info('Public storage archive: ' . $zipPath);
                if ($remote && $backup->copyToRemoteDisk($zipPath, $remote)) {
                    $this->info('Uploaded storage zip to disk: ' . $remote);
                }
            } else {
                $this->warn('Public storage zip skipped: ' . ($zipError ?? 'unknown'));
            }
        }

        return self::SUCCESS;
    }
}
