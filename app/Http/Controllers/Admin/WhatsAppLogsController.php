<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class WhatsAppLogsController extends Controller
{
    public function index(Request $request)
    {
        if (!Schema::hasTable('whatsapp_logs')) {
            return view('admin.whatsapp-logs.index', [
                'logs' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20),
                'tableMissing' => true,
            ]);
        }

        $perPage = min(max((int) $request->get('per_page', 20), 1), 100);

        $logs = WhatsAppLog::with('student:id,full_name,whatsapp_number')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends($request->query());

        return view('admin.whatsapp-logs.index', compact('logs'));
    }
}
