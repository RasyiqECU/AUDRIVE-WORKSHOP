{{--
  x-ui.dropdown — AUDrive Dropdown (Alpine.js)
  Usage: <x-ui.dropdown>
            <x-slot name="trigger"><x-ui.button>Menu</x-ui.button></x-slot>
            <a href="#">Item 1</a>
         </x-ui.dropdown>
--}}
@props(['align' => 'right']) // left, right


@php
    $alignment = $align === 'left' ? 'left-0' : 'right-0';
@endphp


<div x-data="{ open: false }" @click.outside="open = false" class="relative inline-block">
    {{-- Trigger --}}
    <div @click="open = !open">
        {{ $trigger }}
    </div>


    {{-- Dropdown Menu --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak
         class="absolute z-50 mt-2 {{ $alignment }} min-w-48 bg-carbon border border-gunmetal shadow-panel py-1"
         style="clip-path: polygon(0 0, calc(100% - 8px) 0, 100% 8px, 100% 100%, 8px 100%, 0 calc(100% - 8px));">
        {{ $slot }}
    </div>
</div>


{{-- Dropdown Item --}}
@push('components')
@endpush



