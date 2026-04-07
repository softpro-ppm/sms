<?php

namespace App\Http\Controllers\Admin\Super;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ImpersonationAuditController extends Controller
{
    public function index()
    {
        if (! Schema::hasTable('impersonation_audit_logs')) {
            $logs = new \Illuminate\Pagination\LengthAwarePaginator(collect(), 0, 30);

            return view('admin.super.impersonation-log', compact('logs'));
        }

        $logs = DB::table('impersonation_audit_logs as l')
            ->join('users as su', 'su.id', '=', 'l.super_admin_user_id')
            ->join('users as tu', 'tu.id', '=', 'l.target_user_id')
            ->join('training_partners as tp', 'tp.id', '=', 'l.training_partner_id')
            ->select([
                'l.id',
                'l.started_at',
                'l.ended_at',
                'su.name as super_admin_name',
                'su.email as super_admin_email',
                'tu.name as target_name',
                'tp.name as partner_name',
                'tp.code as partner_code',
            ])
            ->orderByDesc('l.started_at')
            ->paginate(30);

        return view('admin.super.impersonation-log', compact('logs'));
    }
}
