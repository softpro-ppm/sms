<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use ZipArchive;

class DatabaseBackupService
{
    /**
     * Create a MySQL dump file under storage/app/backups/.
     *
     * @param  string|null  $error  Populated on failure
     * @return string|null Absolute path to .sql file
     */
    public function createMysqlDump(?string &$error = null): ?string
    {
        $default = config('database.default');
        if ($default !== 'mysql') {
            $error = 'Automated backup supports mysql only; default connection is ' . $default;

            return null;
        }

        $database = config('database.connections.mysql');
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $path = storage_path('app/backups/' . $filename);
        $dir = dirname($path);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $user = $database['username'] ?? '';
        $pass = $database['password'] ?? '';
        $host = $database['host'] ?? '127.0.0.1';
        $port = (string) ($database['port'] ?? 3306);
        $name = $database['database'] ?? '';

        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s --port=%s %s > %s 2>&1',
            escapeshellarg($user),
            escapeshellarg($pass),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($name),
            escapeshellarg($path)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            $error = implode("\n", $output) ?: 'mysqldump exited with code ' . $returnCode;
            if (file_exists($path)) {
                @unlink($path);
            }

            return null;
        }

        return $path;
    }

    /**
     * Zip storage/app/public (uploads, logos) for off-server backup.
     *
     * @param  string|null  $error
     * @return string|null Absolute path to .zip
     */
    public function zipPublicStorage(?string &$error = null): ?string
    {
        $root = storage_path('app/public');
        if (! is_dir($root)) {
            $error = 'Public storage path does not exist.';

            return null;
        }

        $filename = 'public_storage_' . date('Y-m-d_H-i-s') . '.zip';
        $path = storage_path('app/backups/' . $filename);
        $dir = dirname($path);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $zip = new ZipArchive;
        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $error = 'Could not create zip archive.';

            return null;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($root, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            if (! $file->isFile()) {
                continue;
            }
            $full = $file->getRealPath();
            $relative = ltrim(str_replace($root, '', $full), DIRECTORY_SEPARATOR);
            $zip->addFile($full, $relative);
        }

        $zip->close();

        return $path;
    }

    /**
     * Copy a local file to a configured remote disk (e.g. s3).
     */
    public function copyToRemoteDisk(string $localPath, string $diskName): bool
    {
        if (! is_readable($localPath)) {
            return false;
        }

        $basename = basename($localPath);
        $remotePath = 'sms-backups/' . date('Y/m') . '/' . $basename;

        return Storage::disk($diskName)->put($remotePath, file_get_contents($localPath));
    }
}
