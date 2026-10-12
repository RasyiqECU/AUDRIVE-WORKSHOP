{{--
  x-ui.progress — AUDrive Progress Bar
  Usage: <x-ui.progress :value="75" max="100" color="giallo" />
--}}
@props([
    'value' => 0,
    'max' => 100,
    'color' => 'giallo', // giallo, arancio, verde, danger
    'showLabel' => false,
    'size' => 'md', // sm, md, lg
])


@php
    $percentage = min(100, max(0, ($value / ($max ?: 1)) * 100));
    $colors = [
        'giallo' => 'bg-giallo',
        'arancio' => 'bg-arancio',
        'verde' => 'bg-verde',
        'danger' => 'bg-danger',
    ];
    $sizes = [
        'sm' => 'h-1',
        'md' => 'h-2',
        'lg' => 'h-3',
    ];
    $colorClass = $colors[$color] ?? $colors['giallo'];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp


<div {{ $attributes->merge(['class' => 'w-full']) }}>
    @if($showLabel)
        <div class="flex justify-between mb-1">
            <span class="font-mono text-[10px] text-silver-dim">{{ $value }}/{{ $max }}</span>
            <span class="font-mono text-[10px] text-silver-dim">{{ number_format($percentage, 1) }}%</span>
        </div>
    @endif
    <div class="w-full bg-steel/50 {{ $sizeClass }} overflow-hidden">
        <div class="{{ $colorClass }} {{ $sizeClass }} transition-all duration-500" style="width: {{ $percentage }}%"></div>
    </div>
</div>



