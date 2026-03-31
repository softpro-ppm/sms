@extends('layouts.admin')

@section('title', ($lesson->exists ? 'Edit lesson' : 'New lesson').': '.$course->name)
@section('page-title', $lesson->exists ? 'Edit lesson' : 'Add lesson')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $lesson->exists ? 'Edit lesson' : 'New lesson' }}</h2>
            <p class="text-gray-600 mt-1">
                <span class="font-medium text-gray-800">{{ $course->name }}</span>
                <span class="text-gray-400">·</span>
                Module: <span class="font-medium text-gray-800">{{ $module->title }}</span>
            </p>
        </div>
        <a href="{{ route('admin.courses.learning', $course) }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">
            <i class="fas fa-arrow-left mr-2"></i> Back to outline
        </a>
    </div>

    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <form method="POST"
              action="{{ $lesson->exists ? route('admin.courses.learning.lessons.update', [$course, $module, $lesson]) : route('admin.courses.learning.lessons.store', [$course, $module]) }}"
              class="space-y-5"
              id="lessonForm">
            @csrf
            @if($lesson->exists)
                @method('PUT')
            @endif

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Lesson title <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" required maxlength="255"
                       value="{{ old('title', $lesson->title) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 @error('title') border-red-500 @enderror">
                @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="lesson_type" class="block text-sm font-medium text-gray-700 mb-1">Lesson type</label>
                <select name="lesson_type" id="lesson_type"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 @error('lesson_type') border-red-500 @enderror">
                    <option value="article" @selected(old('lesson_type', $lesson->lesson_type) === 'article')>Article (HTML body)</option>
                    <option value="video_link" @selected(old('lesson_type', $lesson->lesson_type) === 'video_link')>Video (external link)</option>
                </select>
                @error('lesson_type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div id="field-body" class="space-y-1">
                <label for="body" class="block text-sm font-medium text-gray-700 mb-1">Body (HTML)</label>
                <p class="text-xs text-gray-500 mb-1">Paste or write HTML; it is rendered on the student lesson page.</p>
                <textarea name="body" id="body" rows="14"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 font-mono text-sm @error('body') border-red-500 @enderror"
                          placeholder="<p>…</p>">{{ old('body', $lesson->body) }}</textarea>
                @error('body')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div id="field-video" class="space-y-1 hidden">
                <label for="video_url" class="block text-sm font-medium text-gray-700 mb-1">Video URL <span class="text-red-500">*</span></label>
                <input type="text" name="video_url" id="video_url" maxlength="2048"
                       value="{{ old('video_url', $lesson->video_url) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 @error('video_url') border-red-500 @enderror"
                       placeholder="https://…">
                @error('video_url')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="estimated_minutes" class="block text-sm font-medium text-gray-700 mb-1">Estimated minutes</label>
                    <input type="number" name="estimated_minutes" id="estimated_minutes" min="0" max="2000"
                           value="{{ old('estimated_minutes', $lesson->estimated_minutes) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 @error('estimated_minutes') border-red-500 @enderror">
                    @error('estimated_minutes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Sort order</label>
                    <input type="number" name="sort_order" id="sort_order" min="0"
                           value="{{ old('sort_order', $lesson->sort_order) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 @error('sort_order') border-red-500 @enderror">
                    @error('sort_order')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                           {{ old('is_active', $lesson->is_active) ? 'checked' : '' }}>
                    <span class="text-sm text-gray-700">Lesson is active (visible to students)</span>
                </label>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.courses.learning', $course) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm font-medium">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm font-medium">
                    {{ $lesson->exists ? 'Save lesson' : 'Create lesson' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    const sel = document.getElementById('lesson_type');
    const bodyWrap = document.getElementById('field-body');
    const videoWrap = document.getElementById('field-video');
    function sync() {
        const v = sel && sel.value;
        if (v === 'video_link') {
            bodyWrap && bodyWrap.classList.add('hidden');
            videoWrap && videoWrap.classList.remove('hidden');
        } else {
            bodyWrap && bodyWrap.classList.remove('hidden');
            videoWrap && videoWrap.classList.add('hidden');
        }
    }
    sel && sel.addEventListener('change', sync);
    sync();
})();
</script>
@endsection
