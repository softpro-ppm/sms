<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    public function index()
    {
        // Get system information
        $systemInfo = [
            'app_name' => config('app.name'),
            'app_version' => config('app.version', '1.0.0'),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_time' => now()->format('Y-m-d H:i:s'),
            'timezone' => config('app.timezone'),
            'environment' => config('app.env'),
            'debug_mode' => config('app.debug'),
        ];

        // Get storage information
        $storageInfo = [
            'total_space' => $this->formatBytes(disk_total_space(storage_path())),
            'free_space' => $this->formatBytes(disk_free_space(storage_path())),
            'used_space' => $this->formatBytes(disk_total_space(storage_path()) - disk_free_space(storage_path())),
        ];

        // Get database information
        $databaseInfo = [
            'connection' => config('database.default'),
            'driver' => config('database.connections.' . config('database.default') . '.driver'),
        ];

        // Get cache information
        $cacheInfo = [
            'driver' => config('cache.default'),
            'status' => $this->getCacheStatus(),
        ];

        // Get mail configuration
        $mailInfo = [
            'driver' => config('mail.default'),
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'encryption' => config('mail.mailers.smtp.encryption'),
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
        ];

        // Get application statistics
        $stats = [
            'total_students' => \App\Models\Student::count(),
            'total_courses' => \App\Models\Course::count(),
            'total_batches' => \App\Models\Batch::count(),
            'total_assessments' => \App\Models\Assessment::count(),
            'total_certificates' => \App\Models\Certificate::count(),
            'total_payments' => \App\Models\Payment::count(),
        ];

        return view('admin.settings.index', compact(
            'systemInfo',
            'storageInfo', 
            'databaseInfo',
            'cacheInfo',
            'mailInfo',
            'stats'
        ));
    }

    public function updateGeneral(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'timezone' => 'required|string',
            'debug_mode' => 'boolean',
        ]);

        // Update environment file
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);
        
        $envContent = $this->updateEnvVariable($envContent, 'APP_NAME', $request->app_name);
        $envContent = $this->updateEnvVariable($envContent, 'APP_TIMEZONE', $request->timezone);
        $envContent = $this->updateEnvVariable($envContent, 'APP_DEBUG', $request->debug_mode ? 'true' : 'false');
        
        file_put_contents($envFile, $envContent);

        // Clear config cache
        Artisan::call('config:clear');

        return redirect()->route('admin.settings.index')
            ->with('success', 'General settings updated successfully!');
    }

    public function updateMail(Request $request)
    {
        $request->validate([
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string|in:tls,ssl',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string|max:255',
        ]);

        // Update environment file
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);
        
        $envContent = $this->updateEnvVariable($envContent, 'MAIL_MAILER', 'smtp');
        $envContent = $this->updateEnvVariable($envContent, 'MAIL_HOST', $request->mail_host);
        $envContent = $this->updateEnvVariable($envContent, 'MAIL_PORT', $request->mail_port);
        $envContent = $this->updateEnvVariable($envContent, 'MAIL_USERNAME', $request->mail_username);
        $envContent = $this->updateEnvVariable($envContent, 'MAIL_PASSWORD', $request->mail_password);
        $envContent = $this->updateEnvVariable($envContent, 'MAIL_ENCRYPTION', $request->mail_encryption);
        $envContent = $this->updateEnvVariable($envContent, 'MAIL_FROM_ADDRESS', $request->mail_from_address);
        $envContent = $this->updateEnvVariable($envContent, 'MAIL_FROM_NAME', $request->mail_from_name);
        
        file_put_contents($envFile, $envContent);

        // Clear config cache
        Artisan::call('config:clear');

        return redirect()->route('admin.settings.index')
            ->with('success', 'Mail settings updated successfully!');
    }

    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');
            
            return redirect()->route('admin.settings.index')
                ->with('success', 'All caches cleared successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.settings.index')
                ->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }

    public function clearCacheGet(Request $request)
    {
        // Optional: Add a simple token check for security
        // You can set CACHE_CLEAR_TOKEN in .env file
        // If token is set in .env, it must be provided in URL
        $token = env('CACHE_CLEAR_TOKEN', null);
        $providedToken = $request->get('token', '');
        
        // Only check token if it's set in .env (not required by default)
        if ($token !== null && $providedToken !== $token) {
            return response()->view('admin.settings.cache-result', [
                'success' => false,
                'message' => 'Invalid token. Access denied. Please provide ?token=your_token in the URL.'
            ], 403);
        }
        
        try {
            $results = [];
            
            Artisan::call('cache:clear');
            $results[] = '✓ Application cache cleared';
            
            Artisan::call('config:clear');
            $results[] = '✓ Configuration cache cleared';
            
            Artisan::call('view:clear');
            $results[] = '✓ View cache cleared';
            
            Artisan::call('route:clear');
            $results[] = '✓ Route cache cleared';
            
            $message = '<strong>All caches cleared successfully!</strong><br><br>' . implode('<br>', $results);
            
            return response()->view('admin.settings.cache-result', [
                'success' => true,
                'message' => $message,
                'timestamp' => now()->format('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            return response()->view('admin.settings.cache-result', [
                'success' => false,
                'message' => 'Failed to clear cache: ' . $e->getMessage(),
                'timestamp' => now()->format('Y-m-d H:i:s')
            ], 500);
        }
    }

    public function optimizeApplication()
    {
        try {
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');
            
            return redirect()->route('admin.settings.index')
                ->with('success', 'Application optimized successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.settings.index')
                ->with('error', 'Failed to optimize application: ' . $e->getMessage());
        }
    }

    public function backupDatabase()
    {
        try {
            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $path = storage_path('app/backups/' . $filename);
            
            // Create backups directory if it doesn't exist
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }
            
            // Simple backup command (adjust based on your database driver)
            $database = config('database.connections.' . config('database.default'));
            $command = "mysqldump --user={$database['username']} --password={$database['password']} --host={$database['host']} {$database['database']} > {$path}";
            
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0) {
                return redirect()->route('admin.settings.index')
                    ->with('success', 'Database backup created successfully!');
            } else {
                return redirect()->route('admin.settings.index')
                    ->with('error', 'Failed to create database backup.');
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.settings.index')
                ->with('error', 'Failed to backup database: ' . $e->getMessage());
        }
    }

    public function exportData()
    {
        try {
            $data = [
                'students' => \App\Models\Student::all(),
                'courses' => \App\Models\Course::all(),
                'batches' => \App\Models\Batch::all(),
                'assessments' => \App\Models\Assessment::all(),
                'certificates' => \App\Models\Certificate::all(),
                'payments' => \App\Models\Payment::all(),
            ];

            $filename = 'data_export_' . date('Y-m-d_H-i-s') . '.json';
            $path = storage_path('app/exports/' . $filename);
            
            // Create exports directory if it doesn't exist
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }
            
            file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));
            
            return response()->download($path)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return redirect()->route('admin.settings.index')
                ->with('error', 'Failed to export data: ' . $e->getMessage());
        }
    }

    private function formatBytes($size, $precision = 2)
    {
        $base = log($size, 1024);
        $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');
        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }

    private function getCacheStatus()
    {
        try {
            Cache::put('cache_test', 'test', 60);
            $status = Cache::get('cache_test') === 'test' ? 'Working' : 'Not Working';
            Cache::forget('cache_test');
            return $status;
        } catch (\Exception $e) {
            return 'Error';
        }
    }

    private function updateEnvVariable($content, $key, $value)
    {
        $pattern = "/^{$key}=.*/m";
        $replacement = "{$key}={$value}";
        
        if (preg_match($pattern, $content)) {
            return preg_replace($pattern, $replacement, $content);
        } else {
            return $content . "\n{$replacement}";
        }
    }
}
