{{--
  x-ui.timeline-item — Timeline Item
  Usage: <x-ui.timeline-item title="Step 1" time="10:00" status="completed">Description</x-ui.timeline-item>
--}}
@props([
    'title' => '',
    'time' => null,
    'status' => 'pending', // completed, active, pending
])


@php
    $statusColors = [
        'completed' => ['dot' => 'bg-verde', 'line' => 'bg-verde/30', 'text' => 'text-verde'],
        'active' => ['dot' => 'bg-giallo animate-pulse-glow', 'line' => 'bg-gunmetal', 'text' => 'text-giallo'],
        'pending' => ['dot' => 'bg-gunmetal', 'line' => 'bg-gunmetal', 'text' => 'text-silver-dim'],
    ];
    $style = $statusColors[$status] ?? $statusColors['pending'];
@endphp


<div class="relative pl-8 pb-6 last:pb-0">
    {{-- Line --}}
    <div class="absolute left-[7px] top-4 bottom-0 w-0.5 {{ $style['line'] }}"></div>


    {{-- Dot --}}
    <div class="absolute left-0 top-1.5 w-4 h-4 rounded-full {{ $style['dot'] }} border-2 border-carbon"></div>


    {{-- Content --}}
    <div>
        <div class="flex items-center gap-2 mb-1">
            <h4 class="font-heading text-sm font-semibold {{ $style['text'] }}">{{ $title }}</h4>
            @if($time)
                <span class="font-mono text-[10px] text-silver-dim">{{ $time }}</span>
            @endif
        </div>
        <div class="text-xs text-silver-dim">
            {{ $slot }}
        </div>
    </div>
</div>



