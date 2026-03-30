@php
    $variant = $variant ?? 'light';
    $isDark = $variant === 'dark';
@endphp
<li class="flex items-start gap-2.5 {{ $isDark ? ($ok ? 'text-emerald-200' : 'text-primary-100/75') : ($ok ? 'text-emerald-800' : 'text-gray-600') }}">
    <span class="mt-0.5 shrink-0 w-5 h-5 rounded-full flex items-center justify-center {{ $isDark ? ($ok ? 'bg-emerald-500/30' : 'bg-white/10') : ($ok ? 'bg-emerald-100' : 'bg-gray-100') }}">
        @if($ok)
            <i class="fas fa-check text-[10px] {{ $isDark ? 'text-emerald-200' : 'text-emerald-700' }}"></i>
        @else
            <i class="fas fa-minus text-[10px] {{ $isDark ? 'text-primary-200/50' : 'text-gray-400' }}"></i>
        @endif
    </span>
    <span>{{ $label }}</span>
</li>
