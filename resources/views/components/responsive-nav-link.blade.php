@props(['href', 'active' => false, 'icon' => null])

@php
    $classes = ($active ?? false)
        ? 'block w-full px-4 py-2 text-left text-primary-600 bg-primary-50 font-medium'
        : 'block w-full px-4 py-2 text-left text-gray-600 hover:text-primary-600 hover:bg-gray-50 font-medium';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        <i class="fas {{ $icon }} mr-3 w-5"></i>
    @endif
    {{ $slot }}
</a>
