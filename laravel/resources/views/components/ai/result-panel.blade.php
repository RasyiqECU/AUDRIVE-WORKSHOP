{{--
  x-ai.result-panel — AI Scan Result Panel
  Usage: <x-ai.result-panel :scan="$scan" />
--}}
@props(['scan'])


<div {{ $attributes->merge(['class' => 'bg-carbon border border-gunmetal overflow-hidden']) }}>
    <div class="bg-steel/30 px-5 py-3 border-b border-gunmetal flex items-center justify-between">
        <h3 class="font-heading text-xs font-semibold text-silver-bright uppercase tracking-wider flex items-center gap-2">
            <span class="text-giallo">🔊</span> Hasil Analisis AI
        </h3>
        <x-ui.badge type="{{ scan->status->value==='completed'?'success':(scan->status->value === 'failed' ? 'danger' : 'warning') }}">
            {{ $scan->status->label() }}
        </x-ui.badge>
    </div>
    <div class="p-5 space-y-4">
        @if($scan->predicted_class)
            <div class="flex items-center gap-4">
                <div class="flex-1">
                    <p class="font-heading text-[9px] uppercase tracking-wider text-silver-dim">Prediksi</p>
                    <p class="font-heading text-lg text-silver-bright mt-1">{{ str_replace('_', ' ', ucfirst($scan->predicted_class)) }}</p>
                </div>
                <x-ai.confidence-meter :confidence="$scan->confidence" />
            </div>
        @endif


        @if($scan->spectrogram_path)
            <div>
                <p class="font-heading text-[9px] uppercase tracking-wider text-silver-dim mb-2">Spektogram</p>
                <img src="{{ asset('storage/' . $scan->spectrogram_path) }}" alt="Spectrogram" class="w-full border border-gunmetal" />
            </div>
        @endif


        @if($scan->recommendations)
            <x-ai.recommendation-list :recommendations="$scan->recommendations" />
        @endif


        @if($scan->technician_review)
            <div class="pt-3 border-t border-gunmetal/30">
                <p class="font-heading text-[9px] uppercase tracking-wider text-silver-dim mb-1">Review Teknisi</p>
                <p class="font-body text-xs text-silver-bright">{{ $scan->technician_review }}</p>
                @if($scan->reviewed_by)
                    <p class="font-mono text-[10px] text-silver-dim mt-1">oleh {{ $scan->reviewer->name ?? '-' }} • {{ $scan->reviewed_at?->format('d M Y H:i') }}</p>
                @endif
            </div>
        @endif
    </div>
</div>



