@props(['type' => 'success', 'message' => '', 'autoDismiss' => true, 'duration' => 5000])

@php
$colors = [
    'success' => 'bg-green-500',
    'error' => 'bg-red-500',
    'warning' => 'bg-yellow-500',
    'info' => 'bg-blue-500'
];

$icons = [
    'success' => 'fa-check-circle',
    'error' => 'fa-exclamation-circle',
    'warning' => 'fa-exclamation-triangle',
    'info' => 'fa-info-circle'
];

$color = $colors[$type] ?? $colors['success'];
$icon = $icons[$type] ?? $icons['success'];
@endphp

<div x-data="{ show: true }" 
     x-show="show" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-90"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform scale-100"
     x-transition:leave-end="opacity-0 transform scale-90"
     @if($autoDismiss) x-init="setTimeout(() => show = false, {{ $duration }})" @endif
     class="fixed top-4 right-4 {{ $color }} text-white px-6 py-3 rounded-lg shadow-lg z-50">
    <div class="flex items-center">
        <i class="fas {{ $icon }} mr-2"></i>
        {{ $message }}
        <button @click="show = false" class="ml-4 text-white hover:text-gray-200">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
