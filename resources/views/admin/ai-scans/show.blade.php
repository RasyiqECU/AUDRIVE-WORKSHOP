@extends('layouts.admin')


@section('title', 'AI Scan Review')


@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">
                Review AI Scan
            </h1>
            <p class="text-sm text-silver-dim mt-2">
                Scan ID: #{{ $aiScan->id }} • {{ $aiScan->created_at->format('d M Y, H:i') }}
            </p>
        </div>
        <a href="{{ route('admin.ai-scans.index') }}" class="px-4 py-2 border border-gunmetal text-silver-dim font-heading text-xs uppercase tracking-wider hover:border-silver-dim transition-premium">
            ← Kembali
        </a>
    </div>


    {{-- Customer & Vehicle Info --}}
    <div class="grid md:grid-cols-2 gap-4">
        <div class="panel border border-gunmetal p-5">
            <h2 class="font-heading text-xs uppercase tracking-wider text-silver-bright mb-3">
                👤 Customer
            </h2>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="font-body text-xs text-silver-dim">Nama:</span>
                    <span class="font-heading text-xs text-silver-bright">{{ $aiScan->customer->user->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-body text-xs text-silver-dim">Email:</span>
                    <span class="font-heading text-xs text-silver-bright">{{ $aiScan->customer->user->email }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-body text-xs text-silver-dim">Customer ID:</span>
                    <span class="font-mono text-xs text-silver-bright">{{ $aiScan->customer->code }}</span>
                </div>
            </div>
        </div>


        @if($aiScan->vehicle)
            <div class="panel border border-gunmetal p-5">
                <h2 class="font-heading text-xs uppercase tracking-wider text-silver-bright mb-3">
                    🚗 Kendaraan
                </h2>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="font-body text-xs text-silver-dim">Unit:</span>
                        <span class="font-heading text-xs text-silver-bright">
                            {{ $aiScan->vehicle->brand }} {{ $aiScan->vehicle->model }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-body text-xs text-silver-dim">Plat:</span>
                        <span class="font-mono text-xs text-silver-bright">{{ $aiScan->vehicle->plate_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-body text-xs text-silver-dim">Tahun:</span>
                        <span class="font-heading text-xs text-silver-bright">{{ $aiScan->vehicle->year }}</span>
                    </div>
                </div>
            </div>
        @endif
    </div>


    {{-- AI Results --}}
    <div class="panel border border-gunmetal overflow-hidden">
        <div class="bg-steel/30 px-5 py-3 border-b border-gunmetal">
            <h2 class="font-heading text-xs uppercase tracking-wider text-silver-bright flex items-center gap-2">
                <span class="text-giallo">🔍</span> Hasil AI
            </h2>
        </div>


        <div class="p-5 space-y-4">
            {{-- Status --}}
            <div class="flex items-center gap-3">
                <span class="font-heading text-xs uppercase tracking-wider text-silver-dim">Status:</span>
                <span class="px-2 py-0.5 text-[9px] font-heading uppercase tracking-wider border {{ $aiScan->getStatusColor() }} bg-steel/30">
                    {{ $aiScan->getStatusLabel() }}
                </span>
            </div>


            {{-- Prediction --}}
            @if($aiScan->predicted_class)
                <div class="flex items-center gap-6">
                    <div class="flex-1">
                        <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1">
                            Prediksi AI
                        </p>
                        <p class="font-display text-xl text-silver-bright">
                            {{ str_replace('_', ' ', ucfirst($aiScan->predicted_class)) }}
                        </p>
                    </div>


                    {{-- Confidence --}}
                    <div class="text-center">
                        <div class="relative w-16 h-16">
                            <svg class="w-16 h-16 transform -rotate-90" viewBox="0 0 36 36">
                                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                      fill="none" stroke="#24272E" stroke-width="3" />
                                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                      fill="none" stroke-width="3" stroke-linecap="round"
                                      class="{{ aiScan->confidence>=0.8?'text-verde':(aiScan->confidence >= 0.6 ? 'text-giallo' : 'text-danger') }}"
                                      stroke="currentColor"
                                      stroke-dasharray="{{ $aiScan->confidence * 100 }}, 100" />
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="font-mono text-xs font-bold {{ aiScan->confidence>=0.8?'text-verde':(aiScan->confidence >= 0.6 ? 'text-giallo' : 'text-danger') }}">
                                    {{ round($aiScan->confidence * 100) }}%
                                </span>
                            </div>
                        </div>
                        <p class="font-heading text-[9px] uppercase tracking-wider text-silver-dim mt-1">
                            Confidence
                        </p>
                    </div>
                </div>
            @endif


            {{-- Recommendations --}}
            @if(aiScan->recommendations&&count(aiScan->recommendations) > 0)
                <div>
                    <p class="font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">
                        Rekomendasi AI
                    </p>
                    <div class="space-y-1">
                        @foreach($aiScan->recommendations as $rec)
                            <p class="font-body text-xs text-silver-dim">• {{ $rec }}</p>
                        @endforeach
                    </div>
                </div>
            @endif


            {{-- Spectrogram --}}
            @if($aiScan->spectrogram_path)
                <div>
                    <p class="font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">
                        Spektogram
                    </p>
                    <img src="{{ asset('storage/' . $aiScan->spectrogram_path) }}"
                         alt="Spectrogram"
                         class="w-full max-w-2xl border border-gunmetal">
                </div>
            @endif
        </div>
    </div>


    {{-- Audio Player --}}
    <div class="panel border border-gunmetal p-5">
        <h2 class="font-heading text-xs uppercase tracking-wider text-silver-bright mb-3">
            🎵 Audio Rekaman
        </h2>
        <audio controls class="w-full">
            <source src="{{ asset('storage/' . $aiScan->audio_path) }}" type="audio/mpeg">
            Browser Anda tidak mendukung pemutar audio.
        </audio>
    </div>


    {{-- Technician Review Form --}}
    <div class="panel border border-gunmetal overflow-hidden">
        <div class="bg-steel/30 px-5 py-3 border-b border-gunmetal">
            <h2 class="font-heading text-xs uppercase tracking-wider text-silver-bright flex items-center gap-2">
                <span class="text-giallo">📝</span> Review Teknisi
            </h2>
        </div>


        <form action="{{ route('admin.ai-scans.review', $aiScan) }}" method="POST" class="p-5 space-y-4">
            @csrf


            {{-- Correct Prediction --}}
            <div>
                <label class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">
                    Koreksi Prediksi (Opsional)
                </label>
                <select name="predicted_class" class="w-full bg-nero border border-gunmetal px-4 py-2.5 text-silver-bright text-sm focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium">
                    <option value="">Biarkan prediksi AI</option>
                    <option value="engine_knocking" {{ $aiScan->predicted_class === 'engine_knocking' ? 'selected' : '' }}>Engine Knocking</option>
                    <option value="bearing_abnormal" {{ $aiScan->predicted_class === 'bearing_abnormal' ? 'selected' : '' }}>Bearing Abnormal</option>
                    <option value="belt_roller_worn" {{ $aiScan->predicted_class === 'belt_roller_worn' ? 'selected' : '' }}>Belt/Roller Worn</option>
                    <option value="exhaust_leak" {{ $aiScan->predicted_class === 'exhaust_leak' ? 'selected' : '' }}>Exhaust Leak</option>
                    <option value="idle_unstable" {{ $aiScan->predicted_class === 'idle_unstable' ? 'selected' : '' }}>Idle Unstable</option>
                    <option value="transmission_noise" {{ $aiScan->predicted_class === 'transmission_noise' ? 'selected' : '' }}>Transmission Noise</option>
                </select>
            </div>


            {{-- Technician Review --}}
            <div>
                <label class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">
                    Review Teknisi <span class="text-danger">*</span>
                </label>
                <textarea name="technician_review" rows="4" required
                    class="w-full bg-nero border border-gunmetal px-4 py-2.5 text-silver-bright text-sm focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium resize-none"
                    placeholder="Tulis review dan rekomendasi Anda...">{{ old('technician_review', $aiScan->technician_review) }}</textarea>
                @error('technician_review')
                    <p class="text-xs text-danger mt-1">{{ $message }}</p>
                @enderror
            </div>


            {{-- Submit --}}
            <button type="submit" class="w-full px-5 py-3 bg-giallo text-nero font-heading font-bold text-xs uppercase tracking-wider hover:bg-arancio transition-premium clip-angular-button">
                Simpan Review
            </button>
        </form>
    </div>
</div>
@endsection



