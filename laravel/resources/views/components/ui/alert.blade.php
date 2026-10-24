{{--
  x-ui.alert — AUDrive Alert Component
  Usage: <x-ui.alert type="success" message="Success!" />
--}}
@props([
    'type' => 'info', // success, warning, danger, info
    'message' => null,
    'dismissible' => true,
])


@php
    $types = [
        'success' => ['border' => 'border-verde/30', 'bg' => 'bg-verde/5', 'text' => 'text-verde', 'icon' => '✓'],
        'warning' => ['border' => 'border-arancio/30', 'bg' => 'bg-arancio/5', 'text' => 'text-arancio', 'icon' => '⚠'],
        'danger'  => ['border' => 'border-danger/30', 'bg' => 'bg-danger/5', 'text' => 'text-danger', 'icon' => '✕'],
        'info'    => ['border' => 'border-giallo/30', 'bg' => 'bg-giallo/5', 'text' => 'text-giallo', 'icon' => 'ℹ'],
    ];
    $style = $types[$type] ?? $types['info'];
@endphp


<div x-data="{ show: true }" x-show="show" x-transition
     {{ $attributes->merge(['class' => "border {$style['border']} {$style['bg']} p-4 flex items-start gap-3"]) }}>
    <span class="text-lg shrink-0 {{ $style['text'] }}">{{ $style['icon'] }}</span>
    <div class="flex-1 text-sm {{ $style['text'] }}">
        @if($message)
            {{ $message }}
        @else
            {{ $slot }}
        @endif
    </div>
    @if($dismissible)
        <button @click="show = false" class="shrink-0 opacity-60 hover:opacity-100 transition-premium {{ $style['text'] }}">
            ✕
        </button>
    @endif
</div>



