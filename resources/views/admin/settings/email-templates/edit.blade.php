@extends('layouts.admin')

@section('title', 'Edit Template: ' . $emailTemplate->name)
@section('page-title', 'Edit: ' . $emailTemplate->name)

@section('content')
<div class="space-y-5">
    <section class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="flex flex-col gap-5 px-6 py-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-3xl">
                <a href="{{ route('admin.settings.email-templates.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-primary-700 hover:text-primary-800">
                    <i class="fas fa-arrow-left text-xs"></i> Back to templates
                </a>
                <div class="mt-4 inline-flex items-center gap-2 rounded-full border border-primary-100 bg-primary-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">
                    <i class="fas fa-envelope-open-text text-[10px]"></i>
                    Edit template
                </div>
                <h2 class="mt-3 text-[2rem] font-semibold tracking-tight text-slate-900">{{ $emailTemplate->name }}</h2>
                <p class="mt-2 text-sm text-slate-600">Slug: {{ $emailTemplate->slug }}</p>
            </div>
            <form action="{{ route('admin.settings.email-templates.reset', $emailTemplate) }}" method="POST" class="inline" onsubmit="return confirm('Reset to default? Your customizations will be lost.');">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-2.5 text-sm font-medium text-amber-700 transition hover:border-amber-300 hover:bg-amber-100">
                    <i class="fas fa-undo text-xs"></i> Reset to default
                </button>
            </form>
        </div>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc list-inside">{{ $errors->first() }}</ul>
        </div>
    @endif

    <form action="{{ route('admin.settings.email-templates.update', $emailTemplate) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-5">
                <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Subject</div>
                <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Email subject line</h3>
                <p class="mt-1 text-sm text-slate-500">Use variables like <code>@verbatim{{ $student->full_name }}@endverbatim</code> where applicable.</p>
            </div>
            <div class="p-6">
                <input type="text" name="subject" id="subject" required
                       class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-100"
                       value="{{ old('subject', $emailTemplate->subject) }}"
                       placeholder="e.g. Softpro - Welcome">
            </div>
        </div>

        <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">
                <div>
                    <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Header</div>
                    <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Header banner content</h3>
                    <p class="mt-1 text-sm text-slate-500">Variables: @foreach(($emailTemplate->variables ?? []) as $v) {{ $v }} @endforeach</p>
                </div>
                <span class="text-xs text-slate-400">Leave empty to use default</span>
            </div>
            <div class="p-6">
                <textarea name="header_html" id="header_html" rows="5"
                          class="w-full rounded-2xl border border-slate-200 px-4 py-3 font-mono text-sm text-slate-700 focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-100"
                          placeholder="<h1>Your header</h1>">{{ old('header_html', $emailTemplate->header_html ?? $defaultHeader ?? '') }}</textarea>
            </div>
        </div>

        <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">
                <div>
                    <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Body</div>
                    <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Email body content</h3>
                    <p class="mt-1 text-sm text-slate-500">HTML + Blade syntax supported. Variables: @foreach(($emailTemplate->variables ?? []) as $v) {{ $v }} @endforeach</p>
                </div>
                <span class="text-xs text-slate-400">Leave empty to use default</span>
            </div>
            <div class="p-6">
                <textarea name="body_html" id="body_html" rows="20"
                          class="w-full rounded-2xl border border-slate-200 px-4 py-3 font-mono text-sm text-slate-700 focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-100"
                          placeholder="HTML with Blade variables">{{ old('body_html', $emailTemplate->body_html ?? $defaultBody ?? '') }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.settings.email-templates.index') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:border-slate-300 hover:bg-slate-50">Cancel</a>
            <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                <i class="fas fa-save text-xs"></i> Save template
            </button>
        </div>
    </form>
</div>
@endsection
