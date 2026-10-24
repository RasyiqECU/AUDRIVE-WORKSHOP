{{--
  x-ui.stat-card — AUDrive Stat Card
  Usage: <x-ui.stat-card label="Revenue" value="Rp 4.2M" trend="+12%" icon="💰" />
--}}
@props([
    'label' => '',
    'value' => '',
    'trend' => null,
    'icon' => null,
    'color' => 'giallo', // giallo, arancio, verde, silver
])


@php
    $colors = [
        'giallo' => 'text-giallo',
        'arancio' => 'text-arancio',
        'verde' => 'text-verde',
        'silver' => 'text-silver-bright',
    ];
    $colorClass = $colors[$color] ?? $colors['giallo'];
@endphp


<div {{ $attributes->merge(['class' => 'bg-steel/30 border border-gunmetal/50 p-4 dashboard-card']) }}>
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <p class="font-display text-2xl md:text-3xl font-bold {{ $colorClass }}">{{ $value }}</p>
            <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim mt-1">{{ $label }}</p>
            @if($trend)
                <p class="font-mono text-[10px] text-verde mt-1">{{ $trend }}</p>
            @endif
        </div>
        @if($icon)
            <span class="text-2xl opacity-40">{{ $icon }}</span>
        @endif
    </div>
</div>



