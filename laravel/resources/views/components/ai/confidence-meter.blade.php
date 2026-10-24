{{--
  x-ai.confidence-meter — AI Confidence Meter
  Usage: <x-ai.confidence-meter :confidence="0.87" />
--}}
@props(['confidence' => 0])


@php
    percentage=round(confidence * 100);
    $color = percentage>=80?'verde':(percentage >= 60 ? 'giallo' : 'danger');
@endphp


<div {{ $attributes->merge(['class' => 'flex flex-col items-center']) }}>
    <div class="relative w-16 h-16">
        <svg class="w-16 h-16 transform -rotate-90" viewBox="0 0 36 36">
            <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                  fill="none" stroke="#24272E" stroke-width="3" />
            <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                  fill="none" stroke-width="3" stroke-linecap="round"
                  :class="{
                      'stroke-verde': {{ $percentage >= 80 }},
                      'stroke-giallo': {{ $percentage >= 60 && $percentage < 80 }},
                      'stroke-danger': {{ $percentage < 60 }},
                  }"
                  stroke="currentColor"
                  class="{{ color==='verde'?'text-verde':(color === 'giallo' ? 'text-giallo' : 'text-danger') }}"
                  stroke-dasharray="{{ $percentage }}, 100" />
        </svg>
        <div class="absolute inset-0 flex items-center justify-center">
            <span class="font-mono text-xs font-bold {{ color==='verde'?'text-verde':(color === 'giallo' ? 'text-giallo' : 'text-danger') }}">
                {{ $percentage }}%
            </span>
        </div>
    </div>
    <p class="font-heading text-[9px] uppercase tracking-wider text-silver-dim mt-1">Confidence</p>
</div>



