{{--
  x-ui.racing-divider — Racing Stripe Divider
  Usage: <x-ui.racing-divider />
--}}
@props(['color' => 'giallo'])


@php
    $colors = ['giallo' => 'from-giallo', 'arancio' => 'from-arancio', 'verde' => 'from-verde'];
    $c = $colors[$color] ?? $colors['giallo'];
@endphp


<div {{ $attributes->merge(['class' => 'flex items-center gap-3 my-4']) }}>
    <div class="h-px flex-1 bg-gradient-to-r {{ $c }} to-transparent opacity-40"></div>
    <div class="w-2 h-2 clip-hexagon bg-giallo"></div>
    <div class="h-px flex-1 bg-gradient-to-l {{ $c }} to-transparent opacity-40"></div>
</div>



