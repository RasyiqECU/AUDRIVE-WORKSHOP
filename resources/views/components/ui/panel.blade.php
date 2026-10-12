{{--
  x-ui.panel — AUDrive Panel Component (with header)
  Usage: <x-ui.panel title="Title">Content</x-ui.panel>
--}}
@props([
    'title' => null,
    'subtitle' => null,
    'chamfered' => false,
])


@php
    $wrapperClass = $chamfered ? 'panel-chamfered' : 'bg-carbon border border-gunmetal';
@endphp


<div {{ $attributes->merge(['class' => "{$wrapperClass} shadow-panel overflow-hidden"]) }}>
    @if(title||isset(header))
        <div class="bg-steel/30 px-5 py-3 border-b border-gunmetal flex items-center justify-between">
            <div>
                @if($title)
                    <h3 class="font-heading text-sm font-semibold text-silver-bright uppercase tracking-wider">{{ $title }}</h3>
                @endif
                @if($subtitle)
                    <p class="font-body text-xs text-silver-dim mt-0.5">{{ $subtitle }}</p>
                @endif
                @isset($header){{ $header }}@endisset
            </div>
            @isset($actions)
                <div class="flex items-center gap-2">{{ $actions }}</div>
            @endisset
        </div>
    @endif
    <div {{ $content->attributes->merge(['class' => 'p-5']) }}>
        {{ $slot }}
    </div>
    @isset($footer)
        <div class="px-5 py-3 border-t border-gunmetal bg-steel/20">
            {{ $footer }}
        </div>
    @endisset
</div>



