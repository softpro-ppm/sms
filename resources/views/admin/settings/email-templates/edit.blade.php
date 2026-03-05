@extends('layouts.admin')

@section('title', 'Edit Template: ' . $emailTemplate->name)
@section('page-title', 'Edit: ' . $emailTemplate->name)

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="{{ route('admin.settings.email-templates.index') }}" class="text-primary-600 hover:text-primary-900 text-sm mb-2 inline-block">
                <i class="fas fa-arrow-left mr-1"></i> Back to templates
            </a>
            <h2 class="text-2xl font-bold text-gray-900">{{ $emailTemplate->name }}</h2>
            <p class="text-gray-600 mt-1">Slug: {{ $emailTemplate->slug }}</p>
        </div>
        <form action="{{ route('admin.settings.email-templates.reset', $emailTemplate) }}" method="POST" class="inline" onsubmit="return confirm('Reset to default? Your customizations will be lost.');">
            @csrf
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-amber-100 text-amber-800 rounded-lg hover:bg-amber-200">
                <i class="fas fa-undo mr-2"></i> Reset to Default
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside">{{ $errors->first() }}</ul>
        </div>
    @endif

    <form action="{{ route('admin.settings.email-templates.update', $emailTemplate) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Email Subject</h3>
                <p class="text-sm text-gray-500 mt-1">Use variables like <code>@verbatim{{ $student->full_name }}@endverbatim</code> where applicable</p>
            </div>
            <div class="p-6">
                <input type="text" name="subject" id="subject" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                       value="{{ old('subject', $emailTemplate->subject) }}"
                       placeholder="e.g. Softpro - Welcome">
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Header (appears in colored banner)</h3>
                    <p class="text-sm text-gray-500 mt-1">Variables: @foreach(($emailTemplate->variables ?? []) as $v) {{ $v }} @endforeach</p>
                </div>
                <span class="text-xs text-gray-400">Leave empty to use default</span>
            </div>
            <div class="p-6">
                <textarea name="header_html" id="header_html" rows="5"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 font-mono text-sm"
                          placeholder="<h1>Your header</h1>">{{ old('header_html', $emailTemplate->header_html ?? $defaultHeader ?? '') }}</textarea>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Body (main content)</h3>
                    <p class="text-sm text-gray-500 mt-1">HTML + Blade syntax supported. Variables: @foreach(($emailTemplate->variables ?? []) as $v) {{ $v }} @endforeach</p>
                </div>
                <span class="text-xs text-gray-400">Leave empty to use default</span>
            </div>
            <div class="p-6">
                <textarea name="body_html" id="body_html" rows="20"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 font-mono text-sm"
                          placeholder="HTML with Blade variables">{{ old('body_html', $emailTemplate->body_html ?? $defaultBody ?? '') }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.settings.email-templates.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                <i class="fas fa-save mr-2"></i> Save Template
            </button>
        </div>
    </form>
</div>
@endsection
