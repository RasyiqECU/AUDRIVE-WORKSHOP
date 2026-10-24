@extends('layouts.customer')

@section('title', 'Hasil AI Scan #' . $aiScan->id)

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">
                Hasil Analisis Suara Mesin
            </h1>
            <p class="text-sm text-silver-dim mt-2">
                Scan ID: #{{ $aiScan->id }} • {{ $aiScan->created_at?->format('d M Y, H:i') ?? '-' }}
            </p>
        </div>
        <a href="{{ route('customer.ai-scan.create') }}" class="px-4 py-2 bg-giallo text-nero font-heading font-bold text-xs uppercase tracking-wider hover:bg-arancio transition-premium clip-angular-button">
            Scan Baru
        </a>
    </div>

    @php
        $scanStatus     = $aiScan->status->value ?? (string) $aiScan->status;
        $hasAiResult    = !empty($aiScan->predicted_class);
        $hasMechReview  = $aiScan->is_confirmed && ($aiScan->mechanic_decision || $aiScan->technician_review);
        $urgency        = $aiScan->getEffectiveUrgency();

        // Tentukan urgency dari mekanik jika ada, else dari AI
        $urgencyConfig  = [
            'safe'      => ['bg'=>'bg-verde/10',   'border'=>'border-verde/40',   'text'=>'text-verde',   'icon'=>'✅', 'label'=>'Aman — Tidak Perlu ke Bengkel'],
            'monitor'   => ['bg'=>'bg-giallo/10',  'border'=>'border-giallo/40',  'text'=>'text-giallo',  'icon'=>'⚠️', 'label'=>'Perlu Dipantau'],
            'urgent'    => ['bg'=>'bg-arancio/10', 'border'=>'border-arancio/40', 'text'=>'text-arancio', 'icon'=>'🔶', 'label'=>'Perlu Segera ke Bengkel'],
            'emergency' => ['bg'=>'bg-danger/10',  'border'=>'border-danger/40',  'text'=>'text-danger',  'icon'=>'🚨', 'label'=>'DARURAT — Hentikan Kendaraan'],
        ][$urgency] ?? ['bg'=>'bg-steel/20','border'=>'border-gunmetal','text'=>'text-silver-dim','icon'=>'❓','label'=>'Tidak Diketahui'];
    @endphp


    {{-- ═══ Status Banner: Proses / Gagal ══════════════════════════════════ --}}
    @if(in_array($scanStatus, ['pending', 'processing']))
        <div class="bg-giallo/5 border border-giallo/30 p-4 flex items-center gap-3">
            <div class="w-4 h-4 border-2 border-giallo border-t-transparent rounded-full animate-spin shrink-0"></div>
            <div>
                <p class="font-heading text-sm text-giallo">Sedang Menganalisis Audio...</p>
                <p class="text-xs text-silver-dim">AI sedang memproses rekaman suara mesin Anda.</p>
            </div>
        </div>
        <script>setTimeout(() => location.reload(), 5000);</script>

    @elseif($scanStatus === 'failed' && !$hasMechReview)
        <div class="bg-danger/5 border border-danger/30 p-4 flex items-center gap-3">
            <span class="text-danger text-2xl shrink-0">⚠️</span>
            <div>
                <p class="font-heading text-sm text-danger">Analisis AI Gagal</p>
                @if($aiScan->quality_warning)
                    <p class="text-xs text-silver-dim mt-1">{{ $aiScan->quality_warning }}</p>
                @else
                    <p class="text-xs text-silver-dim">Terjadi kesalahan saat memproses audio. Silakan coba unggah ulang.</p>
                @endif
                <a href="{{ route('customer.ai-scan.create') }}" class="inline-block mt-2 text-xs text-giallo underline">
                    Coba Lagi →
                </a>
            </div>
        </div>
    @endif


    {{-- Kendaraan --}}
    @if($aiScan->vehicle)
    <div class="panel border border-gunmetal p-4 flex items-center gap-4">
        <span class="text-2xl">🚗</span>
        <div>
            <p class="font-heading text-sm text-silver-bright">{{ $aiScan->vehicle->brand }} {{ $aiScan->vehicle->model }}</p>
            <p class="font-mono text-xs text-silver-dim">{{ $aiScan->vehicle->plate_number }} • {{ $aiScan->vehicle->year }}</p>
        </div>
    </div>
    @endif


    {{-- ═══ Tampilkan Diagnosa jika ada setidaknya 1 dari AI atau Mekanik ═══ --}}
    @if($hasAiResult || $hasMechReview)

        {{-- Urgensi Banner — dari mekanik jika ada, else dari AI --}}
        @if($hasMechReview || $hasAiResult)
        <div class="{{ $urgencyConfig['bg'] }} {{ $urgencyConfig['border'] }} border p-4 flex items-center gap-3">
            <span class="text-2xl shrink-0">{{ $urgencyConfig['icon'] }}</span>
            <div>
                <p class="font-heading text-sm {{ $urgencyConfig['text'] }} uppercase tracking-wider">
                    {{ $urgencyConfig['label'] }}
                    @if($hasMechReview)
                        <span class="text-[10px] text-silver-dim normal-case tracking-normal ml-2">(dikonfirmasi mekanik)</span>
                    @endif
                </p>
                <p class="text-xs text-silver-dim mt-0.5">{{ $aiScan->getUrgencyAction() }}</p>
            </div>
        </div>
        @endif


        {{-- Grid Dual Diagnosis --}}
        @php
            // 2 kolom jika ada keduanya, 1 kolom jika hanya 1
            $gridClass = ($hasAiResult && $hasMechReview) ? 'lg:grid-cols-2' : 'grid-cols-1';
        @endphp
        <div class="grid {{ $gridClass }} gap-4">

            {{-- ─── Kolom 1: Diagnosa AI (Otomatis) ─────────────────────── --}}
            @if($hasAiResult)
            <div class="panel border border-giallo/30 overflow-hidden">
                <div class="bg-giallo/10 px-5 py-3 border-b border-giallo/30 flex items-center gap-2">
                    <span class="text-xl">🤖</span>
                    <div>
                        <h2 class="font-heading text-xs uppercase tracking-wider text-giallo">Diagnosa AI (Otomatis)</h2>
                        <p class="text-[10px] text-silver-dim">Hasil analisis gelombang suara oleh sistem AI</p>
                    </div>
                </div>
                <div class="p-5 space-y-5">

                    {{-- Kelas + Confidence --}}
                    <div class="flex items-center gap-5">
                        <div class="flex-1">
                            <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1">Terdeteksi</p>
                            <p class="font-display text-xl text-silver-bright">
                                {{ $aiScan->class_label ?? str_replace('_', ' ', ucfirst($aiScan->predicted_class)) }}
                            </p>
                        </div>
                        @if($aiScan->confidence)
                        <div class="text-center shrink-0">
                            <div class="relative w-16 h-16">
                                <svg class="w-16 h-16 transform -rotate-90" viewBox="0 0 36 36">
                                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                          fill="none" stroke="#24272E" stroke-width="3"/>
                                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                          fill="none" stroke-width="3" stroke-linecap="round"
                                          class="{{ $aiScan->confidence >= 0.8 ? 'text-verde' : ($aiScan->confidence >= 0.6 ? 'text-giallo' : 'text-danger') }}"
                                          stroke="currentColor"
                                          stroke-dasharray="{{ $aiScan->confidence * 100 }}, 100"/>
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="font-mono text-xs font-bold {{ $aiScan->confidence >= 0.8 ? 'text-verde' : ($aiScan->confidence >= 0.6 ? 'text-giallo' : 'text-danger') }}">
                                        {{ round($aiScan->confidence * 100) }}%
                                    </span>
                                </div>
                            </div>
                            <p class="font-heading text-[9px] uppercase text-silver-dim mt-1">Kepercayaan</p>
                        </div>
                        @endif
                    </div>

                    {{-- Analisis Gelombang --}}
                    @if($aiScan->wave_analysis)
                    <div class="bg-steel/20 border border-gunmetal/50 p-3">
                        <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-2">🔊 Analisis Gelombang Suara</p>
                        <p class="font-body text-xs text-silver-bright leading-relaxed">
                            {{ $aiScan->wave_analysis['pattern_description'] ?? '' }}
                        </p>
                        @if(!empty($aiScan->wave_analysis['dominant_frequency_hz']))
                        <p class="font-mono text-[10px] text-silver-dim mt-2">
                            Frekuensi dominan: {{ $aiScan->wave_analysis['dominant_frequency_hz'] }} Hz
                        </p>
                        @endif
                    </div>
                    @endif

                    {{-- Rekomendasi AI --}}
                    @if($aiScan->recommendations && count($aiScan->recommendations) > 0)
                    <div>
                        <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-2">💡 Rekomendasi AI</p>
                        <div class="space-y-2">
                            @foreach($aiScan->recommendations as $rec)
                            <div class="flex items-start gap-2 bg-steel/20 p-2.5">
                                <span class="text-giallo text-xs shrink-0">›</span>
                                <span class="font-body text-xs text-silver-bright">{{ $rec }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Spectrogram --}}
                    @if($aiScan->spectrogram_path)
                    <div>
                        <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-2">Spektogram Mel</p>
                        <img src="{{ asset('storage/' . $aiScan->spectrogram_path) }}"
                             alt="Mel-Spectrogram" class="w-full border border-gunmetal">
                    </div>
                    @endif

                    @if($aiScan->confidence && $aiScan->confidence < 0.6)
                    <p class="text-[10px] text-silver-dim/70 italic">
                        * Kepercayaan AI di bawah 60%. Lihat konfirmasi mekanik jika tersedia.
                    </p>
                    @endif
                </div>
            </div>
            @endif


            {{-- ─── Kolom 2: Diagnosa Mekanik (jika sudah dikonfirmasi) ─── --}}
            @if($hasMechReview)
            <div class="panel border border-verde/40 overflow-hidden">
                <div class="bg-verde/10 px-5 py-3 border-b border-verde/40 flex items-center gap-2">
                    <span class="text-xl">🔧</span>
                    <div>
                        <h2 class="font-heading text-xs uppercase tracking-wider text-verde">Diagnosa Mekanik</h2>
                        <p class="text-[10px] text-silver-dim">
                            Oleh: {{ $aiScan->reviewer?->name ?? 'Mekanik AUDrive' }}
                            • {{ $aiScan->reviewed_at?->format('d M Y') ?? '-' }}
                        </p>
                    </div>
                </div>

                <div class="p-5 space-y-4">
                    {{-- Keputusan Mekanik --}}
                    <div>
                        <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1">Hasil Diagnosa Manual</p>
                        @php
                            $mechClass = $aiScan->mechanic_decision;
                            $mechLabels = [
                                'normal'             => 'Mesin Normal',
                                'timing_chain_belt'  => 'Kerusakan Timing Chain / Belt',
                                'valve_lifter'       => 'Kerusakan Katup / Lifter',
                                'rod_knock'          => 'Rod Knock / Bantalan Mesin',
                                'combustion_misfire' => 'Masalah Pembakaran / Misfire',
                                'exhaust_leak'       => 'Kebocoran Knalpot',
                                'bearing_alternator' => 'Bearing Alternator / Pulley',
                            ];
                            $mechLabel = $mechLabels[$mechClass] ?? str_replace('_', ' ', ucfirst($mechClass ?? ''));
                        @endphp

                        @if($mechClass)
                            {{-- Jika mekanik mengoreksi AI --}}
                            @if($hasAiResult && $mechClass !== $aiScan->predicted_class)
                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                <span class="text-[10px] text-silver-dim/60 line-through">
                                    {{ $aiScan->class_label ?? str_replace('_', ' ', ucfirst($aiScan->predicted_class)) }}
                                </span>
                                <span class="text-silver-dim text-xs">→</span>
                            </div>
                            <p class="font-display text-xl text-verde">{{ $mechLabel }}</p>
                            <p class="text-[10px] text-arancio mt-1">✏️ Mekanik mengoreksi hasil AI</p>
                            @else
                            <p class="font-display text-xl text-verde">{{ $mechLabel ?: '—' }}</p>
                            @if($hasAiResult)
                                <p class="text-[10px] text-verde mt-1">✅ Mekanik mengkonfirmasi hasil AI</p>
                            @endif
                            @endif
                        @else
                            {{-- Tidak ada koreksi kelas, hanya catatan --}}
                            @if($hasAiResult)
                                <p class="font-display text-xl text-verde">
                                    {{ $aiScan->class_label ?? str_replace('_', ' ', ucfirst($aiScan->predicted_class)) }}
                                </p>
                                <p class="text-[10px] text-verde mt-1">✅ Mekanik mengkonfirmasi hasil AI</p>
                            @else
                                <p class="font-display text-xl text-verde">Review Tersedia</p>
                            @endif
                        @endif
                    </div>

                    {{-- Urgensi Final --}}
                    @if($aiScan->final_urgency)
                    <div>
                        <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1">Urgensi (Dikonfirmasi Mekanik)</p>
                        <span class="px-2 py-1 text-[9px] font-heading uppercase tracking-wider border {{ $aiScan->getUrgencyColor() }}">
                            {{ $aiScan->getUrgencyIcon() }} {{ $aiScan->getUrgencyLabel() }}
                        </span>
                    </div>
                    @endif

                    {{-- Catatan Mekanik --}}
                    @if($aiScan->technician_review)
                    <div class="bg-verde/5 border border-verde/20 p-3">
                        <p class="font-heading text-[10px] uppercase tracking-wider text-verde mb-2">📝 Catatan Mekanik</p>
                        <p class="font-body text-xs text-silver-bright leading-relaxed">{{ $aiScan->technician_review }}</p>
                    </div>
                    @endif

                    {{-- Catatan Tambahan --}}
                    @if($aiScan->mechanic_final_notes)
                    <div>
                        <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1">Rekomendasi Tambahan</p>
                        <p class="font-body text-xs text-silver-dim leading-relaxed">{{ $aiScan->mechanic_final_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

        </div>{{-- end grid --}}


        {{-- Info jika AI belum proses tapi mekanik sudah review --}}
        @if(!$hasAiResult && $hasMechReview)
        <div class="bg-steel/20 border border-gunmetal/50 p-4 flex items-start gap-3">
            <span class="text-silver-dim text-lg shrink-0">ℹ️</span>
            <p class="text-xs text-silver-dim">
                Hasil diagnosa AI belum tersedia untuk rekaman ini. Diagnosa di atas merupakan hasil pemeriksaan manual oleh mekanik kami.
            </p>
        </div>
        @endif

        {{-- Info jika AI ada tapi mekanik belum review --}}
        @if($hasAiResult && !$hasMechReview)
        <div class="bg-steel/20 border border-gunmetal/50 p-4 flex items-start gap-3">
            <span class="text-lg shrink-0">ℹ️</span>
            <p class="text-xs text-silver-dim">
                Hasil di atas merupakan analisis otomatis dari AI. Tim mekanik kami dapat memberikan konfirmasi tambahan — hasil review mekanik akan muncul di sini secara otomatis jika tersedia.
            </p>
        </div>
        @endif


        {{-- CTA Booking --}}
        @if(in_array($urgency, ['urgent', 'emergency']))
        <div class="panel border border-arancio/40 p-5 text-center bg-arancio/5">
            <p class="font-heading text-sm text-arancio mb-1">{{ $urgencyConfig['icon'] }} Tindakan Segera Diperlukan</p>
            <p class="text-xs text-silver-dim mb-4">{{ $urgencyConfig['label'] }}</p>
            <a href="{{ route('workshop.booking.create') }}" class="inline-block px-6 py-2.5 bg-arancio text-nero font-heading font-bold text-xs uppercase tracking-wider hover:bg-danger transition-premium clip-angular-button">
                Booking Servis Sekarang
            </a>
        </div>
        @elseif($urgency === 'monitor')
        <div class="panel border border-gunmetal p-5 text-center">
            <p class="font-heading text-sm text-silver-bright mb-3">Butuh pemeriksaan lebih lanjut?</p>
            <a href="{{ route('workshop.booking.create') }}" class="inline-block px-6 py-2.5 bg-giallo text-nero font-heading font-bold text-xs uppercase tracking-wider hover:bg-arancio transition-premium clip-angular-button">
                Booking Servis
            </a>
        </div>
        @endif

    @endif{{-- end hasAiResult || hasMechReview --}}


    {{-- Audio Rekaman --}}
    <div class="panel border border-gunmetal p-5">
        <h2 class="font-heading text-xs uppercase tracking-wider text-silver-bright mb-3">🎵 Audio Rekaman Anda</h2>
        <audio controls class="w-full">
            <source src="{{ asset('storage/' . $aiScan->audio_path) }}" type="audio/mpeg">
            Browser Anda tidak mendukung pemutar audio.
        </audio>
    </div>

</div>
@endsection
