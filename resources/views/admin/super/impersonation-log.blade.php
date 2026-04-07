@extends('layouts.admin')

@section('title', 'Impersonation log')
@section('page-title', 'View as centre — audit log')

@section('content')
<div class="p-6">
    <p class="text-gray-600 mb-6 max-w-3xl">
        When a Super Admin uses <strong>View as centre</strong>, a row is recorded here with start and end time. Use this for internal accountability.
    </p>

    @if($logs->total() === 0 && !\Illuminate\Support\Facades\Schema::hasTable('impersonation_audit_logs'))
    <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-amber-900 text-sm mb-6">
        Run <code class="bg-amber-100 px-1 rounded">php artisan migrate</code> to enable the impersonation audit table.
    </div>
    @endif

    <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Started</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Ended</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Super Admin</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Centre</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Logged in as</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-gray-800">{{ \Carbon\Carbon::parse($log->started_at)->format('M d, Y H:i') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-600">{{ $log->ended_at ? \Carbon\Carbon::parse($log->ended_at)->format('M d, Y H:i') : '—' }}</td>
                        <td class="px-4 py-3 text-gray-800">{{ $log->super_admin_name }}<br><span class="text-xs text-gray-500">{{ $log->super_admin_email }}</span></td>
                        <td class="px-4 py-3 text-gray-800">{{ $log->partner_name }} <span class="text-gray-500">({{ $log->partner_code }})</span></td>
                        <td class="px-4 py-3 text-gray-800">{{ $log->target_name }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">No impersonation records yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $logs->links() }}</div>
        @endif
    </div>
</div>
@endsection
