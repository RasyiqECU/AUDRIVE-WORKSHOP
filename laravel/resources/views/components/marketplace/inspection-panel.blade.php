{{--
  x-marketplace.inspection-panel — Car Inspection Result Panel
  Usage: <x-marketplace.inspection-panel :inspection="$inspection" />
--}}
@props(['inspection'])


@php
    $resultColors = ['pass' => 'verde', 'fail' => 'danger', 'conditional' => 'arancio'];
    $resultLabels = ['pass' => 'LULUS', 'fail' => 'GAGAL', 'conditional' => 'BERSYARAT'];
    $resultVal = $inspection->result instanceof \BackedEnum ? $inspection->result->value : ($inspection->result ?? 'pass');
    $color = $resultColors[$resultVal] ?? 'silver';
@endphp


<div {{ $attributes->merge(['class' => 'bg-carbon border border-gunmetal overflow-hidden']) }}>
    <div class="bg-steel/30 px-5 py-3 border-b border-gunmetal flex items-center justify-between">
        <h3 class="font-heading text-xs font-semibold text-silver-bright uppercase tracking-wider flex items-center gap-2">
            <span class="text-giallo">🔍</span> Hasil Inspeksi
        </h3>
        <x-ui.badge type="{{ $color === 'verde' ? 'success' : ($color === 'danger' ? 'danger' : 'warning') }}">
            {{ $resultLabels[$resultVal] ?? 'N/A' }}
        </x-ui.badge>
    </div>
    <div class="p-5">
        @if($inspection->checklist)
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-4">
                @foreach($inspection->checklist as $key => $value)
                    <div class="bg-steel/20 border border-gunmetal/30 p-3">
                        <p class="font-heading text-[9px] uppercase tracking-wider text-silver-dim">{{ str_replace('_', ' ', $key) }}</p>
                        <p class="font-heading text-sm text-silver-bright mt-1">{{ ucfirst($value) }}</p>
                    </div>
                @endforeach
            </div>
        @endif
        @if($inspection->notes)
            <div class="mt-3 pt-3 border-t border-gunmetal/30">
                <p class="font-heading text-[9px] uppercase tracking-wider text-silver-dim mb-1">Catatan</p>
                <p class="font-body text-xs text-silver-bright">{{ $inspection->notes }}</p>
            </div>
        @endif
        <div class="mt-3 pt-3 border-t border-gunmetal/30 flex items-center justify-between">
            <span class="font-mono text-[10px] text-silver-dim">Inspektur: {{ $inspection->inspector->name ?? '-' }}</span>
            <span class="font-mono text-[10px] text-silver-dim">{{ $inspection->inspected_at?->format('d M Y H:i') }}</span>
        </div>
    </div>
</div>



