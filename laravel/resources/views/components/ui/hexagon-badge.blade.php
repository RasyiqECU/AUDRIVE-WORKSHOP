{{--
  x-ui.hexagon-badge — Hexagonal Badge Icon
  Usage: <x-ui.hexagon-badge color="giallo" size="md">🏎</x-ui.hexagon-badge>
--}}
@props([
    'color' => 'giallo', // giallo, arancio, verde, silver
    'size' => 'md', // sm, md, lg
])


@php
    $colors = [
        'giallo' => 'bg-giallo text-nero',
        'arancio' => 'bg-arancio text-nero',
        'verde' => 'bg-verde text-nero',
        'silver' => 'bg-steel text-silver-bright border border-gunmetal',
    ];
    $sizes = [
        'sm' => 'w-8 h-8 text-sm',
        'md' => 'w-10 h-10 text-lg',
        'lg' => 'w-14 h-14 text-2xl',
    ];
    $colorClass = $colors[$color] ?? $colors['giallo'];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp


<div {{ $attributes->merge(['class' => "clip-hexagon {$colorClass} {$sizeClass} flex items-center justify-center font-bold"]) }}>
    {{ $slot }}
</div>



