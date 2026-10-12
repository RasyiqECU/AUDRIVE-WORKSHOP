{{--
  x-ui.button — AUDrive Button Component
  Usage: <x-ui.button variant="primary" size="md" :href="route('...')">Label</x-ui.button>
--}}
@props([
    'variant' => 'primary',    // primary, secondary, ghost, danger, success
    'size' => 'md',            // sm, md, lg
    'type' => 'button',
    'href' => null,
    'loading' => false,
    'disabled' => false,
    'fullWidth' => false,
    'icon' => null,
])


@php
    $baseClasses = 'inline-flex items-center justify-center gap-2 font-heading font-bold uppercase tracking-wider transition-premium focus:outline-none focus:ring-2 focus:ring-giallo/30 disabled:opacity-50 disabled:cursor-not-allowed';


    $variants = [
        'primary'   => 'bg-giallo text-nero hover:bg-arancio hover:text-nero',
        'secondary' => 'border border-giallo text-giallo hover:bg-giallo hover:text-nero',
        'ghost'     => 'border border-gunmetal text-silver hover:border-silver-dim hover:text-silver-bright bg-transparent',
        'danger'    => 'bg-danger/10 border border-danger text-danger hover:bg-danger hover:text-nero',
        'success'   => 'bg-verde/10 border border-verde text-verde hover:bg-verde hover:text-nero',
    ];


    $sizes = [
        'sm' => 'text-[10px] px-3 py-1.5',
        'md' => 'text-xs px-5 py-2.5',
        'lg' => 'text-sm px-7 py-3',
    ];


    $clipClass = 'clip-angular-button';
    $widthClass = $fullWidth ? 'w-full' : '';


    $variantStyle = $variants[$variant] ?? $variants['primary'];
    $sizeStyle = $sizes[$size] ?? $sizes['md'];
    $classes = "{$baseClasses} {$variantStyle} {$sizeStyle} {$clipClass} {$widthClass}";
@endphp


@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)<span>{{ $icon }}</span>@endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
        @if($loading)
            <span class="inline-block w-3.5 h-3.5 border-2 border-current border-t-transparent rounded-full animate-spin"></span>
        @elseif($icon)
            <span>{{ $icon }}</span>
        @endif
        {{ $slot }}
    </button>
@endif



