{{--
  x-workshop.status-tracker — Work Order Status Tracker
  Usage: <x-workshop.status-tracker :currentStatus="$workOrder->status->value" />
--}}
@props(['currentStatus' => 'booking'])


@php
    $steps = [
        'booking' => ['label' => 'Booking', 'icon' => '📅'],
        'queued' => ['label' => 'Antrian', 'icon' => '⏳'],
        'in_progress' => ['label' => 'Dikerjakan', 'icon' => '🔧'],
        'quality_control' => ['label' => 'QC', 'icon' => '🔍'],
        'completed' => ['label' => 'Selesai', 'icon' => '✓'],
        'delivered' => ['label' => 'Diserahkan', 'icon' => '🏁'],
    ];
    statusOrder=arraykeys(steps);
    currentIndex=arraysearch(currentStatus, $statusOrder);
@endphp


<div {{ $attributes->merge(['class' => 'flex items-center justify-between']) }}>
    @foreach($steps as $key => $step)
        @php
            stepIndex=arraysearch(key, $statusOrder);
            $isCompleted = $stepIndex < $currentIndex;
            $isCurrent = $key === $currentStatus;
        @endphp
        <div class="flex flex-col items-center relative flex-1">
            {{-- Line --}}
            @if(!$loop->first)
                <div class="absolute top-4 right-1/2 w-full h-0.5 {{ $isCompleted || $isCurrent ? 'bg-giallo' : 'bg-gunmetal' }}"></div>
            @endif
            {{-- Dot --}}
            <div class="relative z-10 w-8 h-8 rounded-full flex items-center justify-center text-sm
                {{ $isCompleted ? 'bg-verde text-nero' : ($isCurrent ? 'bg-giallo text-nero animate-pulse-glow' : 'bg-steel border border-gunmetal text-silver-dim') }}">
                {{ $step['icon'] }}
            </div>
            <span class="font-heading text-[9px] uppercase tracking-wider mt-2 text-center
                {{ $isCurrent ? 'text-giallo font-bold' : ($isCompleted ? 'text-verde' : 'text-silver-dim') }}">
                {{ $step['label'] }}
            </span>
        </div>
    @endforeach
</div>



