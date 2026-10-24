{{--
  x-ai.upload-panel — AI Sound Scan Upload Panel
  Usage: <x-ai.upload-panel action="/workshop/ai-scan" />
--}}
@props(['action' => '#'])


<div x-data="{ recording: false, file: null, preview: null }" {{ $attributes->merge(['class' => 'bg-carbon border border-gunmetal overflow-hidden']) }}>
    <div class="bg-steel/30 px-5 py-3 border-b border-gunmetal">
        <h3 class="font-heading text-xs font-semibold text-silver-bright uppercase tracking-wider flex items-center gap-2">
            <span class="text-giallo">🔊</span> AI Sound Scanning
        </h3>
    </div>
    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="p-5 space-y-4">
        @csrf
        <div class="border-2 border-dashed border-gunmetal hover:border-giallo/40 transition-premium p-8 text-center cursor-pointer"
             @click="$refs.audioInput.click()">
            <input type="file" name="audio" accept="audio/*" x-ref="audioInput" class="hidden"
                   @change="file = $event.target.files[0]; if(file) preview = URL.createObjectURL(file)" />
            <template x-if="!file">
                <div>
                    <span class="text-4xl text-gunmetal block mb-3">🎤</span>
                    <p class="font-heading text-xs text-silver-dim uppercase tracking-wider">Upload file audio atau rekam suara mesin</p>
                    <p class="font-mono text-[10px] text-silver-dim/50 mt-2">Format: WAV, MP3, M4A • Maks 10MB</p>
                </div>
            </template>
            <template x-if="file">
                <div>
                    <span class="text-3xl text-verde block mb-2">✓</span>
                    <p class="font-heading text-xs text-verde uppercase tracking-wider" x-text="file.name"></p>
                    <p class="font-mono text-[10px] text-silver-dim mt-1" x-text="(file.size / 1024 / 1024).toFixed(2) + ' MB'"></p>
                </div>
            </template>
        </div>
        <x-ui.button type="submit" variant="primary" fullWidth :disabled="true" x-bind:disabled="!file">
            Analisis Suara
        </x-ui.button>
    </form>
</div>



