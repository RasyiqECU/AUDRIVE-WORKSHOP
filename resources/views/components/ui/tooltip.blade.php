{{--
  x-ui.tooltip — AUDrive Tooltip (Alpine.js)
  Usage: <x-ui.tooltip text="Tooltip text">
            <button>Hover me</button>
         </x-ui.tooltip>
--}}
@props(['text' => '', 'position' => 'top']) // top, bottom, left, right


@php
    $positions = [
        'top' => 'bottom-full left-1/2 -translate-x-1/2 mb-2',
        'bottom' => 'top-full left-1/2 -translate-x-1/2 mt-2',
        'left' => 'right-full top-1/2 -translate-y-1/2 mr-2',
        'right' => 'left-full top-1/2 -translate-y-1/2 ml-2',
    ];
    $posClass = $positions[$position] ?? $positions['top'];
@endphp


<div x-data="{ show: false }" class="relative inline-block" @mouseenter="show = true" @mouseleave="show = false">
    {{ $slot }}
    <div x-show="show" x-transition x-cloak
         class="absolute z-50 {{ $posClass }} px-2 py-1 bg-nero border border-gunmetal text-xs text-silver-bright whitespace-nowrap pointer-events-none">
        {{ $text }}
    </div>
</div>



