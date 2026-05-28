@props([
    'title',
    'value',
    'icon' => 'fa-chart-simple',
    'color' => 'teal',
    'change' => null,
    'subtitle' => null,
    'unit' => null,
])

@php
    $palette = [
        'teal'   => ['bg' => 'bg-teal-50',   'text' => 'text-teal-700',   'ring' => 'ring-teal-100'],
        'blue'   => ['bg' => 'bg-sky-50',    'text' => 'text-sky-700',    'ring' => 'ring-sky-100'],
        'red'    => ['bg' => 'bg-rose-50',   'text' => 'text-rose-700',   'ring' => 'ring-rose-100'],
        'orange' => ['bg' => 'bg-amber-50',  'text' => 'text-amber-700',  'ring' => 'ring-amber-100'],
        'green'  => ['bg' => 'bg-emerald-50','text' => 'text-emerald-700','ring' => 'ring-emerald-100'],
        'purple' => ['bg' => 'bg-violet-50', 'text' => 'text-violet-700', 'ring' => 'ring-violet-100'],
    ];
    $c = $palette[$color] ?? $palette['teal'];
    $isPositive = $change && str_starts_with(trim($change), '+');
@endphp

<div class="h360-card h360-shadow p-5 group hover:-translate-y-0.5 transition">
    <div class="flex items-start justify-between">
        <div class="space-y-1">
            <p class="text-xs font-medium uppercase tracking-wider text-slate-500">{{ $title }}</p>
            <div class="flex items-baseline gap-1">
                <span class="text-3xl font-semibold tracking-tight text-slate-900">{{ $value }}</span>
                @if($unit)<span class="text-sm text-slate-400 font-medium">{{ $unit }}</span>@endif
            </div>
        </div>
        <div class="w-10 h-10 rounded-xl {{ $c['bg'] }} {{ $c['text'] }} ring-1 {{ $c['ring'] }} flex items-center justify-center">
            <i class="fas {{ $icon }}"></i>
        </div>
    </div>

    @if($change)
        <div class="mt-3 flex items-center gap-1 text-xs font-medium {{ $isPositive ? 'text-emerald-600' : 'text-rose-600' }}">
            <i class="fas {{ $isPositive ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }}"></i>
            <span>{{ $change }}</span>
        </div>
    @elseif($subtitle)
        <p class="mt-3 text-xs text-slate-500">{{ $subtitle }}</p>
    @endif
</div>
