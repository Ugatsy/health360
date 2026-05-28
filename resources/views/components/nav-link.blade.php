@props(['active' => false, 'href' => '#', 'icon' => null])

@php
    $base = 'inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition';
    $state = $active
        ? 'bg-[var(--h360-primary-50)] text-[var(--h360-primary-600)]'
        : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => "$base $state"]) }}>
    @if($icon)
        <i class="fas {{ $icon }} text-[13px] {{ $active ? 'text-[var(--h360-primary)]' : 'text-slate-400' }}"></i>
    @endif
    {{ $slot }}
</a>
