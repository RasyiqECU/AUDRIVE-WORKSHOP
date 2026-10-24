{{--
  x-ui.price — Formatted Price Display
  Usage: <x-ui.price :amount="500000000" size="lg" />
--}}
@props([
    'amount' => 0,
    'size' => 'md', // sm, md, lg
    'currency' => 'Rp',
    'strikethrough' => false,
])


@php
    $sizes = [
        'sm' => 'font-mono text-xs',
        'md' => 'font-mono text-sm',
        'lg' => 'font-display text-xl md:text-2xl font-bold',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $formatted = $currency . ' ' . number_format($amount, 0, ',', '.');
@endphp


<span {{ $attributes->merge(['class' => "{$sizeClass} text-giallo" . ($strikethrough ? ' line-through text-silver-dim' : '')]) }}>
    {{ $formatted }}
</span>



