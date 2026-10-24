@extends('layouts.admin')

@section('title', 'Review AI Scan #' . $aiScan->id)

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">
                Review AI Scan <span class="text-giallo">#{{ $aiScan->id }}</span>
            </h1>
            <p class="text-sm text-silver-dim mt-2">
                {{ $aiScan->created_at?->format('d M Y, H:i') ?? '-' }}
                @if($aiScan->is_confirmed)
                    • <span class="text-verde font-heading text-xs uppercase">✅ Sudah Dikonfirmasi Mekanik</span>
                @else
                    • <span class="text-silver-dim font-heading text-xs uppercase">💬 Belum Ada Review Mekanik</span>
                @endif
            </p>
        </div>
        <a href="{{ route('admin.ai-scans.index') }}" class="px-4 py-2 border border-gunmetal text-silver-dim font-heading text-xs uppercase tracking-wider hover:border-silver-dim transition-premium">
            ← Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="border border-verde/50 bg-verde/10 px-4 py-3 text-verde text-sm font-body">
            {{ session('success') }}
        </div>
    @endif


    {{-- Info Customer & Kendaraan --}}
    <div class="grid md:grid-cols-2 gap-4">
        <div class="panel border border-gunmetal p-5">
            <h2 class="font-heading text-xs uppercase tracking-wider text-silver-bright mb-3">👤 Customer</h2>
            @if($aiScan->customer)
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="font-body text-xs text-silver-dim">Nama:</span>
                    <span class="font-heading text-xs text-silver-bright">{{ $aiScan->customer->user?->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-body text-xs text-silver-dim">Email:</span>
                    <span class="font-heading text-xs text-silver-bright">{{ $aiScan->customer->user?->email ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-body text-xs text-silver-dim">ID:</span>
                    <span class="font-mono text-xs text-silver-bright">{{ $aiScan->customer->code ?? '-' }}</span>
                </div>
            </div>
            @else
                <p class="text-xs text-silver-dim italic">Data customer tidak ditemukan.</p>
            @endif
        </div>

        @if($aiScan->vehicle)
        <div class="panel border border-gunmetal p-5">
            <h2 class="font-heading text-xs uppercase tracking-wider text-silver-bright mb-3">🚗 Kendaraan</h2>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="font-body text-xs text-silver-dim">Unit:</span>
                    <span class="font-heading text-xs text-silver-bright">{{ $aiScan->vehicle->brand }} {{ $aiScan->vehicle->model }}</span>
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


    {{-- Audio Player --}}
    <div class="panel border border-gunmetal p-5">
        <h2 class="font-heading text-xs uppercase tracking-wider text-silver-bright mb-3">🎵 Audio Rekaman — Dengarkan Sebelum Review</h2>
        <audio controls class="w-full">
            <source src="{{ asset('storage/' . $aiScan->audio_path) }}" type="audio/mpeg">
            Browser tidak mendukung pemutar audio.
        </audio>
        @if($aiScan->quality_warning)
            <p class="text-xs text-giallo mt-2">⚠️ {{ $aiScan->quality_warning }}</p>
        @endif
        @if($aiScan->quality_score)
            <p class="text-xs text-silver-dim mt-1">Skor kualitas audio: {{ round((float)$aiScan->quality_score * 100) }}%</p>
        @endif
    </div>


    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- DUAL DIAGNOSIS: Hasil AI vs Hasil Manual                             --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    <div class="grid lg:grid-cols-2 gap-4">

        {{-- Kolom 1: Hasil Diagnosa AI (Otomatis) --}}
        <div class="panel border border-giallo/30 overflow-hidden">
            <div class="bg-giallo/10 px-5 py-3 border-b border-giallo/30 flex items-center gap-2">
                <span class="text-giallo text-lg">🤖</span>
                <div>
                    <h2 class="font-heading text-xs uppercase tracking-wider text-giallo">Hasil Diagnosa AI (Otomatis)</h2>
                    <p class="text-[10px] text-silver-dim mt-0.5">Prediksi awal — memerlukan verifikasi mekanik</p>
                </div>
            </div>

            <div class="p-5 space-y-4">
                {{-- Status & Urgensi --}}
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="px-2 py-0.5 text-[9px] font-heading uppercase tracking-wider border {{ $aiScan->getStatusColor() }} bg-steel/30">
                        {{ $aiScan->getStatusLabel() }}
                    </span>
                    @if($aiScan->urgency_level)
                    <span class="px-2 py-0.5 text-[9px] font-heading uppercase tracking-wider border
                        {{ match($aiScan->urgency_level) {
                            'safe'      => 'text-verde border-verde bg-verde/10',
                            'monitor'   => 'text-giallo border-giallo bg-giallo/10',
                            'urgent'    => 'text-arancio border-arancio bg-arancio/10',
                            'emergency' => 'text-danger border-danger bg-danger/10',
                            default     => 'text-silver-dim border-silver-dim'
                        } }}">
                        {{ match($aiScan->urgency_level) { 'safe'=>'✅ Aman', 'monitor'=>'⚠️ Pantau', 'urgent'=>'🔶 Segera', 'emergency'=>'🚨 Darurat', default=>'?' } }}
                    </span>
                    @endif
                </div>

                {{-- Kelas Kerusakan --}}
                @if($aiScan->predicted_class)
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1">Diagnosa Terdeteksi</p>
                    <p class="font-display text-lg text-silver-bright">
                        {{ $aiScan->class_label ?? str_replace('_', ' ', ucfirst($aiScan->predicted_class)) }}
                    </p>
                </div>
                @endif

                {{-- Confidence --}}
                @if($aiScan->confidence)
                <div class="flex items-center gap-3">
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Kepercayaan AI:</p>
                    <div class="flex-1 bg-steel/30 h-2 rounded-full overflow-hidden">
                        <div class="h-full rounded-full {{ $aiScan->confidence >= 0.8 ? 'bg-verde' : ($aiScan->confidence >= 0.6 ? 'bg-giallo' : 'bg-danger') }}"
                             style="width: {{ round($aiScan->confidence * 100) }}%"></div>
                    </div>
                    <span class="font-mono text-xs {{ $aiScan->confidence >= 0.8 ? 'text-verde' : ($aiScan->confidence >= 0.6 ? 'text-giallo' : 'text-danger') }}">
                        {{ round($aiScan->confidence * 100) }}%
                    </span>
                </div>
                @endif

                {{-- Analisis Gelombang --}}
                @if($aiScan->wave_analysis)
                <div class="bg-steel/20 border border-gunmetal/50 p-3">
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-2">🔊 Analisis Gelombang Suara</p>
                    <p class="font-body text-xs text-silver-bright leading-relaxed">
                        {{ $aiScan->wave_analysis['pattern_description'] ?? '-' }}
                    </p>
                    @if(!empty($aiScan->wave_analysis['anomaly_details']))
                        <p class="font-mono text-[10px] text-silver-dim mt-2">{{ $aiScan->wave_analysis['anomaly_details'] }}</p>
                    @endif
                    @if(!empty($aiScan->wave_analysis['dominant_frequency_hz']))
                        <p class="font-mono text-[10px] text-silver-dim mt-1">
                            Frekuensi dominan: {{ $aiScan->wave_analysis['dominant_frequency_hz'] }} Hz
                        </p>
                    @endif
                </div>
                @endif

                {{-- Catatan Teknis untuk Mekanik --}}
                @if($aiScan->mechanic_ai_notes)
                <div class="bg-giallo/5 border border-giallo/20 p-3">
                    <p class="font-heading text-[10px] uppercase tracking-wider text-giallo mb-2">📋 Catatan Teknis AI (untuk Mekanik)</p>
                    <p class="font-body text-xs text-silver-dim leading-relaxed">{{ $aiScan->mechanic_ai_notes }}</p>
                </div>
                @endif

                {{-- Top 3 Prediksi --}}
                {{-- Spectrogram --}}
                @if($aiScan->spectrogram_path)
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-2">Spektogram Mel</p>
                    <img src="{{ asset('storage/' . $aiScan->spectrogram_path) }}"
                         alt="Mel-Spectrogram" class="w-full border border-gunmetal">
                </div>
                @endif
            </div>
        </div>

        {{-- Kolom 2: Hasil Diagnosa Manual (Mekanik/Admin) --}}
        <div class="panel border {{ $aiScan->is_confirmed ? 'border-verde/40' : 'border-gunmetal' }} overflow-hidden">
            <div class="bg-{{ $aiScan->is_confirmed ? 'verde' : 'steel' }}/10 px-5 py-3 border-b border-{{ $aiScan->is_confirmed ? 'verde/40' : 'gunmetal' }} flex items-center gap-2">
                <span class="text-lg">{{ $aiScan->is_confirmed ? '🔧' : '⏳' }}</span>
                <div>
                    <h2 class="font-heading text-xs uppercase tracking-wider {{ $aiScan->is_confirmed ? 'text-verde' : 'text-silver-bright' }}">
                        Hasil Diagnosa Manual
                    </h2>
                    <p class="text-[10px] text-silver-dim mt-0.5">
                        {{ $aiScan->is_confirmed ? 'Dikonfirmasi oleh: ' . ($aiScan->reviewer?->name ?? 'Mekanik') : 'Belum direview oleh mekanik' }}
                    </p>
                </div>
            </div>

            @if($aiScan->is_confirmed)
            {{-- Sudah ada review --}}
            <div class="p-5 space-y-4">
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1">Keputusan Mekanik</p>
                    <p class="font-display text-lg text-verde">
                        {{ $aiScan->mechanic_decision
                            ? str_replace('_', ' ', ucfirst($aiScan->mechanic_decision))
                            : '✅ Setuju dengan hasil AI' }}
                    </p>
                </div>
                @if($aiScan->final_urgency)
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1">Urgensi Final</p>
                    <span class="px-2 py-0.5 text-[9px] font-heading uppercase tracking-wider border {{ $aiScan->getUrgencyColor() }}">
                        {{ $aiScan->getUrgencyIcon() }} {{ $aiScan->getUrgencyLabel() }}
                    </span>
                </div>
                @endif
                @if($aiScan->technician_review)
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1">Review Mekanik</p>
                    <p class="font-body text-xs text-silver-bright leading-relaxed">{{ $aiScan->technician_review }}</p>
                </div>
                @endif
                @if($aiScan->mechanic_final_notes)
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1">Catatan Final</p>
                    <p class="font-body text-xs text-silver-bright leading-relaxed">{{ $aiScan->mechanic_final_notes }}</p>
                </div>
                @endif
                <div class="text-right">
                    <p class="font-mono text-[10px] text-silver-dim">
                        Direview: {{ $aiScan->reviewed_at?->format('d M Y, H:i') ?? '-' }}
                    </p>
                </div>
            </div>

            @else
            {{-- Form Review Manual (Langkah 4) --}}
            <form action="{{ route('admin.ai-scans.review', ['aiScan' => $aiScan->id]) }}" method="POST" class="p-5 space-y-4">
                @csrf
                @method('PATCH')

                <div class="bg-steel/20 border border-gunmetal/30 p-3 text-xs text-silver-dim">
                    ℹ️ Hasil AI sudah langsung diterima customer. Review Anda bersifat <strong>opsional</strong> — gunakan untuk mengoreksi atau mengkonfirmasi diagnosa AI berdasarkan keahlian Anda.
                </div>

                {{-- Pilih Aksi --}}
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">Tindakan</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-2 cursor-pointer border border-gunmetal p-3 hover:border-verde/50 transition-premium has-[:checked]:border-verde">
                            <input type="radio" name="action" value="approve" checked class="accent-verde">
                            <div>
                                <p class="font-heading text-xs text-verde">✅ Setujui Hasil AI</p>
                                <p class="font-body text-[10px] text-silver-dim">Konfirmasi prediksi AI sudah benar</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer border border-gunmetal p-3 hover:border-arancio/50 transition-premium has-[:checked]:border-arancio">
                            <input type="radio" name="action" value="override" class="accent-arancio">
                            <div>
                                <p class="font-heading text-xs text-arancio">✏️ Koreksi Hasil AI</p>
                                <p class="font-body text-[10px] text-silver-dim">Ganti dengan diagnosa manual</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Override Fields (tampil jika koreksi dipilih) --}}
                <div id="override-fields" class="space-y-3 hidden">
                    <div>
                        <label class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">Koreksi Diagnosa</label>
                        <select name="mechanic_decision" class="w-full bg-nero border border-gunmetal px-4 py-2.5 text-silver-bright text-sm focus:border-arancio focus:outline-none">
                            <option value="">— Pilih diagnosa yang benar —</option>
                            <option value="normal">Mesin Normal</option>
                            <option value="timing_chain_belt">Kerusakan Timing Chain / Belt</option>
                            <option value="valve_lifter">Kerusakan Katup / Lifter</option>
                            <option value="rod_knock">Rod Knock / Bantalan Mesin</option>
                            <option value="combustion_misfire">Masalah Pembakaran / Misfire</option>
                            <option value="exhaust_leak">Kebocoran Knalpot</option>
                            <option value="bearing_alternator">Bearing Alternator / Pulley</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">Urgensi Hasil Koreksi</label>
                        <select name="final_urgency" class="w-full bg-nero border border-gunmetal px-4 py-2.5 text-silver-bright text-sm focus:border-arancio focus:outline-none">
                            <option value="safe">✅ Aman</option>
                            <option value="monitor">⚠️ Perlu Dipantau</option>
                            <option value="urgent" selected>🔶 Perlu Segera ke Bengkel</option>
                            <option value="emergency">🚨 Darurat</option>
                        </select>
                    </div>
                </div>

                {{-- Review & Catatan --}}
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">
                        Review / Temuan Mekanik <span class="text-danger">*</span>
                    </label>
                    <textarea name="technician_review" rows="4" required
                        class="w-full bg-nero border border-gunmetal px-4 py-2.5 text-silver-bright text-sm focus:border-giallo focus:outline-none resize-none"
                        placeholder="Deskripsikan temuan saat mendengarkan audio dan pemeriksaan visual...">{{ old('technician_review', $aiScan->technician_review) }}</textarea>
                    @error('technician_review')
                        <p class="text-xs text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">Catatan Tambahan (Opsional)</label>
                    <textarea name="mechanic_final_notes" rows="2"
                        class="w-full bg-nero border border-gunmetal px-4 py-2.5 text-silver-bright text-sm focus:border-giallo focus:outline-none resize-none"
                        placeholder="Rekomendasi tindakan atau informasi tambahan..."></textarea>
                </div>

                <button type="submit" class="w-full px-5 py-3 bg-giallo text-nero font-heading font-bold text-xs uppercase tracking-wider hover:bg-arancio transition-premium">
                    Simpan & Konfirmasi Review
                </button>
            </form>
            @endif
        </div>
    </div>


    {{-- Rekomendasi untuk Customer --}}
    @if($aiScan->recommendations && count($aiScan->recommendations) > 0)
    <div class="panel border border-gunmetal overflow-hidden">
        <div class="bg-steel/30 px-5 py-3 border-b border-gunmetal">
            <h2 class="font-heading text-xs uppercase tracking-wider text-silver-bright">📋 Rekomendasi yang Diterima Customer</h2>
        </div>
        <div class="p-5 space-y-2">
            @foreach($aiScan->recommendations as $rec)
                <p class="font-body text-xs text-silver-dim">{{ $rec }}</p>
            @endforeach
        </div>
    </div>
    @endif

</div>

<script>
    // Toggle override fields
    document.querySelectorAll('input[name="action"]').forEach(radio => {
        radio.addEventListener('change', () => {
            const overrideFields = document.getElementById('override-fields');
            if (overrideFields) {
                overrideFields.classList.toggle('hidden', radio.value !== 'override' || !radio.checked);
            }
        });
    });
</script>
@endsection
