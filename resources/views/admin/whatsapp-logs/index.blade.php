@extends('layouts.admin')

@section('title', 'WhatsApp Logs')
@section('page-title', 'WhatsApp Logs')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">WhatsApp Logs</h2>
            <p class="text-gray-600 mt-1">View sent WhatsApp notifications and delivery status</p>
        </div>
    </div>

    @if(isset($tableMissing) && $tableMissing)
    <div class="bg-amber-50 border border-amber-200 rounded-lg p-6">
        <p class="text-amber-800">The <code>whatsapp_logs</code> table has not been created yet. Run <code>php artisan migrate</code> to create it.</p>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Template</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $log)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $log->created_at->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $log->type }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $log->phone }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $log->student?->full_name ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $log->status === 'sent' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $log->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $log->template_name }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">No WhatsApp logs yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
