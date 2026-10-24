{{--
  x-parts.part-compatibility — Part Compatibility List
  Usage: <x-parts.part-compatibility :compatibilities="$part->compatibilities" />
--}}
@props(['compatibilities' => collect()])


@if($compatibilities->count() > 0)
<div {{ $attributes->merge(['class' => 'bg-carbon border border-gunmetal overflow-hidden']) }}>
    <div class="bg-steel/30 px-4 py-3 border-b border-gunmetal">
        <h3 class="font-heading text-xs font-semibold text-silver-bright uppercase tracking-wider">Kompatibel Dengan</h3>
    </div>
    <div class="p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            @foreach($compatibilities as $compat)
                <div class="flex items-center gap-2 bg-steel/20 border border-gunmetal/30 px-3 py-2">
                    <span class="text-giallo text-xs">⚙</span>
                    <span class="font-heading text-xs text-silver-bright">{{ $compat->vehicle_brand }} {{ $compat->vehicle_model }}</span>
                    <span class="font-mono text-[10px] text-silver-dim ml-auto">{{ $compat->year_from }}-{{ $compat->year_to }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif



