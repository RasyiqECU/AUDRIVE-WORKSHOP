{{--
  x-ui.card — AUDrive Card Component
  Usage: <x-ui.card title="Title" subtitle="Subtitle">Content</x-ui.card>
--}}
@props([
    'title' => null,
    'subtitle' => null,
    'hover' => false,
    'padding' => 'md', // sm, md, lg, none
])


@php
    $paddings = [
        'sm' => 'p-3',
        'md' => 'p-5',
        'lg' => 'p-7',
        'none' => 'p-0',
    ];
    // Perbaikan di baris bawah ini: menambahkan $ pada $paddings dan $padding
    $paddingClass = $paddings[$padding] ?? $paddings['md'];
    $hoverClass = $hover ? 'dashboard-card hover:border-giallo/30 hover:-translate-y-0.5' : '';
@endphp


<div {{ $attributes->merge(['class' => "bg-carbon border border-gunmetal {$paddingClass} {$hoverClass} transition-premium"]) }}>
    @if($title)
        <div class="mb-4">
            <h3 class="font-heading text-sm font-semibold text-silver-bright uppercase tracking-wider">{{ $title }}</h3>
            @if($subtitle)
                <p class="font-body text-xs text-silver-dim mt-1">{{ $subtitle }}</p>
            @endif
        </div>
    @endif
    {{ $slot }}
</div>



