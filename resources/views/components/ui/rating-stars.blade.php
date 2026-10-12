{{--
  x-ui.rating-stars — Star Rating Display
  Usage: <x-ui.rating-stars :rating="4.5" />
--}}
@props([
    'rating' => 0,
    'max' => 5,
    'size' => 'md', // sm, md, lg
    'interactive' => false,
    'name' => 'rating',
])


@php
    $sizes = ['sm' => 'text-xs', 'md' => 'text-sm', 'lg' => 'text-lg'];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp


<div {{ $attributes->merge(['class' => "inline-flex items-center gap-0.5 {$sizeClass}"]) }}>
    @for($i = 1; $i <= $max; $i++)
        @if($interactive)
            <input type="radio" name="{{ $name }}" value="{{ $i }}" id="{{ $name }}-{{ $i }}"
                   class="hidden peer" {{ $rating >= $i ? 'checked' : '' }}>
            <label for="{{ $name }}-{{ $i }}" class="cursor-pointer transition-premium
                {{ $rating >= $i ? 'text-giallo' : 'text-gunmetal hover:text-giallo/50' }}">
                ★
            </label>
        @else
            <span class="{{ $i <= round($rating) ? 'text-giallo' : 'text-gunmetal' }}">★</span>
        @endif
    @endfor
    @if(!$interactive && $rating > 0)
        <span class="font-mono text-[10px] text-silver-dim ml-1">({{ number_format($rating, 1) }})</span>
    @endif
</div>



