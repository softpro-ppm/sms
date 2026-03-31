@extends('layouts.admin')

@section('title', ($module->exists ? 'Edit module' : 'New module').': '.$course->name)
@section('page-title', $module->exists ? 'Edit module' : 'Add module')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $module->exists ? 'Edit module' : 'New module' }}</h2>
            <p class="text-gray-600 mt-1">Course: <span class="font-medium text-gray-800">{{ $course->name }}</span></p>
        </div>
        <a href="{{ route('admin.courses.learning', $course) }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">
            <i class="fas fa-arrow-left mr-2"></i> Back to outline
        </a>
    </div>

    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <form method="POST"
              action="{{ $module->exists ? route('admin.courses.learning.modules.update', [$course, $module]) : route('admin.courses.learning.modules.store', $course) }}"
              class="space-y-5">
            @csrf
            @if($module->exists)
                @method('PUT')
            @endif

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Module title <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" required maxlength="255"
                       value="{{ old('title', $module->title) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 @error('title') border-red-500 @enderror">
                @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="summary" class="block text-sm font-medium text-gray-700 mb-1">Summary</label>
                <textarea name="summary" id="summary" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 @error('summary') border-red-500 @enderror"
                          placeholder="Short description for staff and students (optional)">{{ old('summary', $module->summary) }}</textarea>
                @error('summary')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Sort order</label>
                    <input type="number" name="sort_order" id="sort_order" min="0"
                           value="{{ old('sort_order', $module->sort_order) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 @error('sort_order') border-red-500 @enderror">
                    @error('sort_order')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="flex items-end pb-2">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                               {{ old('is_active', $module->is_active) ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">Module is active (visible to students)</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.courses.learning', $course) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm font-medium">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm font-medium">
                    {{ $module->exists ? 'Save changes' : 'Create module' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
