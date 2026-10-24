{{--
  x-ui.badge — AUDrive Status Badge
  Usage: <x-ui.badge type="success">Active</x-ui.badge>
--}}
@props([
    'type' => 'info', // success, warning, danger, info, neutral
    'dot' => true,
])


@php
    $types = [
        'success' => 'border-verde/40 text-verde bg-verde/5',
        'warning' => 'border-arancio/40 text-arancio bg-arancio/5',
        'danger'  => 'border-danger/40 text-danger bg-danger/5',
        'info'    => 'border-giallo/40 text-giallo bg-giallo/5',
        'neutral' => 'border-silver/20 text-silver-dim bg-steel/30',
    ];
    $dots = [
        'success' => 'bg-verde',
        'warning' => 'bg-arancio',
        'danger'  => 'bg-danger',
        'info'    => 'bg-giallo',
        'neutral' => 'bg-silver-dim',
    ];
    $style = $types[$type] ?? $types['info'];
    $dotColor = $dots[$type] ?? $dots['info'];
@endphp


<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2.5 py-0.5 border font-heading font-semibold text-[10px] uppercase tracking-wider {$style}"]) }}
      style="clip-path: polygon(0 0, calc(100% - 5px) 0, 100% 5px, 100% 100%, 5px 100%, 0 calc(100% - 5px));">
    @if($dot)
        <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }}"></span>
    @endif
    {{ $slot }}
</span>



