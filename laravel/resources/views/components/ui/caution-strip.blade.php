{{--
  x-ui.caution-strip — Hazard/Caution Stripes
  Usage: <x-ui.caution-strip /> or <x-ui.caution-strip text="WARNING" />
--}}
@props(['text' => null, 'height' => 'sm']) // sm, md, lg


@php
    $heights = ['sm' => 'h-2', 'md' => 'h-4', 'lg' => 'h-6'];
    $h = $heights[$height] ?? $heights['sm'];
@endphp


<div {{ $attributes->merge(['class' => "bg-hazard-stripes {$h} w-full flex items-center justify-center overflow-hidden"]) }}>
    @if($text)
        <span class="font-display text-[8px] text-nero font-bold tracking-widest bg-giallo px-2">{{ $text }}</span>
    @endif
</div>



