@extends('layouts.customer')


@section('title', 'AI Sound Scan')


@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div>
        <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">
            AI Sound Scan
        </h1>
        <p class="text-sm text-silver-dim mt-2">
            Upload rekaman suara mesin untuk analisis kerusakan otomatis menggunakan AI
        </p>
    </div>


    {{-- Upload Form --}}
    <div class="panel border border-gunmetal overflow-hidden">
        <div class="bg-steel/30 px-5 py-3 border-b border-gunmetal">
            <h2 class="font-heading text-xs uppercase tracking-wider text-silver-bright flex items-center gap-2">
                <span class="text-giallo">🎤</span> Upload Audio
            </h2>
        </div>


        <form action="{{ route('customer.ai-scan.store') }}" method="POST" enctype="multipart/form-data" class="p-5 space-y-4">
            @csrf


            {{-- Vehicle Selection --}}
            <div>
                <label class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">
                    Kendaraan (Opsional)
                </label>
                <select name="customer_vehicle_id" class="w-full bg-nero border border-gunmetal px-4 py-2.5 text-silver-bright text-sm focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium">
                    <option value="">Pilih kendaraan...</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}">
                            {{ $vehicle->brand }} {{ $vehicle->model }} - {{ $vehicle->plate_number }}
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-silver-dim mt-1">Pilih kendaraan untuk hasil analisis yang lebih akurat</p>
            </div>


            {{-- Audio Upload --}}
            <div x-data="{ fileName: '', fileSize: '' }">
                <label class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">
                    File Audio <span class="text-danger">*</span>
                </label>


                <div class="border-2 border-dashed border-gunmetal hover:border-giallo/40 transition-premium p-8 text-center cursor-pointer"
                     @click="$refs.audioInput.click()"
                     @dragover.prevent="$el.classList.add('border-giallo')"
                     @dragleave.prevent="$el.classList.remove('border-giallo')"
                     @drop.prevent="
                         $refs.audioInput.files = $event.dataTransfer.files;
                         fileName = $event.dataTransfer.files[0].name;
                         fileSize = ($event.dataTransfer.files[0].size / 1024 / 1024).toFixed(2) + ' MB';
                     ">


                    <input type="file"
                           name="audio"
                           accept=".wav,.mp3,.m4a"
                           x-ref="audioInput"
                           class="hidden"
                           @change="
                               fileName = $event.target.files[0].name;
                               fileSize = ($event.target.files[0].size / 1024 / 1024).toFixed(2) + ' MB';
                           "
                           required>


                    <template x-if="!fileName">
                        <div>
                            <span class="text-4xl text-gunmetal block mb-3">🎵</span>
                            <p class="font-heading text-xs text-silver-dim uppercase tracking-wider">
                                Klik atau drag file audio ke sini
                            </p>
                            <p class="font-mono text-[10px] text-silver-dim/50 mt-2">
                                Format: WAV, MP3, M4A • Maks 20MB
                            </p>
                        </div>
                    </template>


                    <template x-if="fileName">
                        <div>
                            <span class="text-3xl text-verde block mb-2">✓</span>
                            <p class="font-heading text-xs text-verde uppercase tracking-wider" x-text="fileName"></p>
                            <p class="font-mono text-[10px] text-silver-dim mt-1" x-text="fileSize"></p>
                        </div>
                    </template>
                </div>


                @error('audio')
                    <p class="text-xs text-danger mt-1">{{ $message }}</p>
                @enderror
            </div>


            {{-- Tips --}}
            <div class="bg-steel/20 border border-gunmetal/30 p-4">
                <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">
                    💡 Tips Rekaman
                </h3>
                <ul class="text-xs text-silver-dim space-y-1">
                    <li>• Rekam suara mesin dalam kondisi idle (mesin menyala tapi tidak digas)</li>
                    <li>• Pastikan rekaman minimal 5 detik</li>
                    <li>• Hindari noise background berlebih</li>
                    <li>• Dekatkan microphone ke mesin (30-50 cm)</li>
                </ul>
            </div>


            {{-- Submit Button --}}
            <button type="submit" class="w-full px-5 py-3 bg-giallo text-nero font-heading font-bold text-xs uppercase tracking-wider hover:bg-arancio transition-premium clip-angular-button">
                Mulai Analisis
            </button>
        </form>
    </div>


    {{-- Recent Scans --}}
    @if($recentScans->count() > 0)
        <div class="panel border border-gunmetal overflow-hidden">
            <div class="bg-steel/30 px-5 py-3 border-b border-gunmetal flex items-center justify-between">
                <h2 class="font-heading text-xs uppercase tracking-wider text-silver-bright">
                    Scan Terbaru
                </h2>
                <a href="{{ route('customer.ai-scan.index') }}" class="font-heading text-[10px] text-giallo uppercase tracking-wider hover:text-arancio transition-premium">
                    Lihat Semua →
                </a>
            </div>


            <div class="divide-y divide-gunmetal/30">
                @foreach($recentScans as $scan)
                    <a href="{{ route('customer.ai-scan.show', $scan) }}" class="block p-4 hover:bg-steel/20 transition-premium">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">🔊</span>
                                <div>
                                    <p class="font-heading text-sm text-silver-bright">
                                        {{ $scan->vehicle ? $scan->vehicle->brand . ' ' . $scan->vehicle->model : 'Tanpa kendaraan' }}
                                    </p>
                                    <p class="font-mono text-[10px] text-silver-dim">
                                        {{ $scan->created_at->format('d M Y, H:i') }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-0.5 text-[9px] font-heading uppercase tracking-wider border {{ $scan->getStatusColor() }} bg-steel/30">
                                    {{ $scan->getStatusLabel() }}
                                </span>
                                @if($scan->predicted_class)
                                    <p class="font-mono text-[10px] text-silver-dim mt-1">
                                        {{ str_replace('_', ' ', ucfirst($scan->predicted_class)) }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection



