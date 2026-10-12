@extends('layouts.customer')


@section('title', 'Hasil AI Scan')


@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">
                Hasil Analisis AI
            </h1>
            <p class="text-sm text-silver-dim mt-2">
                Scan ID: #{{ $aiScan->id }} • {{ $aiScan->created_at->format('d M Y, H:i') }}
            </p>
        </div>
        <a href="{{ route('customer.ai-scan.create') }}" class="px-4 py-2 bg-giallo text-nero font-heading font-bold text-xs uppercase tracking-wider hover:bg-arancio transition-premium clip-angular-button">
            Scan Baru
        </a>
    </div>


    {{-- Status Banner --}}
    @if($aiScan->status === 'processing')
        <div class="bg-giallo/5 border border-giallo/30 p-4 flex items-center gap-3">
            <div class="spinner-audrive"></div>
            <div>
                <p class="font-heading text-sm text-giallo">Sedang Menganalisis...</p>
                <p class="text-xs text-silver-dim">AI sedang memproses audio Anda. Halaman akan otomatis refresh.</p>
            </div>
        </div>
        <script>
            setTimeout(() => location.reload(), 5000);
        </script>
    @elseif($aiScan->status === 'manual_review')
        <div class="bg-arancio/5 border border-arancio/30 p-4 flex items-center gap-3">
            <span class="text-arancio text-xl">⚠️</span>
            <div>
                <p class="font-heading text-sm text-arancio">Memerlukan Review Manual</p>
                <p class="text-xs text-silver-dim">Teknisi kami akan menganalisis audio Anda secara manual.</p>
            </div>
        </div>
    @elseif($aiScan->status === 'failed')
        <div class="bg-danger/5 border border-danger/30 p-4 flex items-center gap-3">
            <span class="text-danger text-xl">✕</span>
            <div>
                <p class="font-heading text-sm text-danger">Analisis Gagal</p>
                <p class="text-xs text-silver-dim">Terjadi kesalahan saat memproses audio. Silakan coba lagi.</p>
            </div>
        </div>
    @endif


    {{-- Vehicle Info --}}
    @if($aiScan->vehicle)
        <div class="panel border border-gunmetal p-5">
            <h2 class="font-heading text-xs uppercase tracking-wider text-silver-bright mb-3">
                Kendaraan
            </h2>
            <div class="flex items-center gap-4">
                <span class="text-3xl">🚗</span>
                <div>
                    <p class="font-heading text-sm text-silver-bright">
                        {{ $aiScan->vehicle->brand }} {{ $aiScan->vehicle->model }}
                    </p>
                    <p class="font-mono text-xs text-silver-dim">
                        {{ $aiScan->vehicle->plate_number }} • {{ $aiScan->vehicle->year }}
                    </p>
                </div>
            </div>
        </div>
    @endif


    {{-- Results --}}
    @if($aiScan->status === 'completed' && $aiScan->predicted_class)
        <div class="panel border border-gunmetal overflow-hidden">
            <div class="bg-steel/30 px-5 py-3 border-b border-gunmetal">
                <h2 class="font-heading text-xs uppercase tracking-wider text-silver-bright flex items-center gap-2">
                    <span class="text-giallo">🔍</span> Hasil Prediksi
                </h2>
            </div>


            <div class="p-5 space-y-6">
                {{-- Prediction --}}
                <div class="flex items-center gap-6">
                    <div class="flex-1">
                        <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1">
                            Kerusakan Terdeteksi
                        </p>
                        <p class="font-display text-xl text-silver-bright">
                            {{ str_replace('_', ' ', ucfirst($aiScan->predicted_class)) }}
                        </p>
                    </div>


                    {{-- Confidence Meter --}}
                    <div class="text-center">
                        <div class="relative w-20 h-20">
                            <svg class="w-20 h-20 transform -rotate-90" viewBox="0 0 36 36">
                                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                      fill="none" stroke="#24272E" stroke-width="3" />
                                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                      fill="none" stroke-width="3" stroke-linecap="round"
                                      class="{{ aiScan->confidence>=0.8?'text-verde':(aiScan->confidence >= 0.6 ? 'text-giallo' : 'text-danger') }}"
                                      stroke="currentColor"
                                      stroke-dasharray="{{ $aiScan->confidence * 100 }}, 100" />
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="font-mono text-sm font-bold {{ aiScan->confidence>=0.8?'text-verde':(aiScan->confidence >= 0.6 ? 'text-giallo' : 'text-danger') }}">
                                    {{ round($aiScan->confidence * 100) }}%
                                </span>
                            </div>
                        </div>
                        <p class="font-heading text-[9px] uppercase tracking-wider text-silver-dim mt-1">
                            Confidence
                        </p>
                    </div>
                </div>


                {{-- Spectrogram --}}
                @if($aiScan->spectrogram_path)
                    <div>
                        <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-2">
                            Spektogram
                        </p>
                        <img src="{{ asset('storage/' . $aiScan->spectrogram_path) }}"
                             alt="Spectrogram"
                             class="w-full border border-gunmetal">
                    </div>
                @endif


                {{-- Recommendations --}}
                @if($aiScan->recommendations && count($aiScan->recommendations) > 0)
                    <div>
                        <p class="font-heading text-xs uppercase tracking-wider text-silver-bright mb-3">
                            💡 Rekomendasi
                        </p>
                        <div class="space-y-2">
                            @foreach($aiScan->recommendations as $recommendation)
                                <div class="flex items-start gap-2 bg-steel/20 border border-gunmetal/30 p-3">
                                    <span class="text-verde text-sm shrink-0">✓</span>
                                    <span class="font-body text-xs text-silver-bright">{{ $recommendation }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif


                {{-- Technician Review --}}
                @if($aiScan->technician_review)
                    <div class="bg-steel/20 border border-gunmetal/30 p-4">
                        <p class="font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">
                            📝 Review Teknisi
                        </p>
                        <p class="font-body text-xs text-silver-dim mb-2">
                            {{ $aiScan->technician_review }}
                        </p>
                        <p class="font-mono text-[10px] text-silver-dim">
                            Oleh: {{ $aiScan->reviewer->name ?? 'Teknisi' }} • {{ $aiScan->reviewed_at->format('d M Y, H:i') }}
                        </p>
                    </div>
                @endif
            </div>
        </div>


        {{-- CTA --}}
        <div class="panel border border-gunmetal p-5 text-center">
            <p class="font-heading text-sm text-silver-bright mb-3">
                Butuh bantuan lebih lanjut?
            </p>
            <a href="{{ route('workshop.booking.create') }}" class="inline-block px-6 py-2.5 bg-giallo text-nero font-heading font-bold text-xs uppercase tracking-wider hover:bg-arancio transition-premium clip-angular-button">
                Booking Servis
            </a>
        </div>
    @endif


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
</div>
@endsection



