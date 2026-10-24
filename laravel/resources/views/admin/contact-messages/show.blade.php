@extends('layouts.admin')

@section('title', 'Detail & Jawab Pertanyaan Customer')

@section('breadcrumb')
    <a href="{{ route('admin.contact-messages.index') }}" class="text-silver-dim hover:text-giallo transition-premium font-heading uppercase tracking-wider">
        Pesan Customer
    </a>
    <span class="text-gunmetal">/</span>
    <span class="text-giallo font-heading uppercase tracking-wider">{{ $contactMessage->name }}</span>
@endsection

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <a href="{{ route('admin.contact-messages.index') }}" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1 mb-2">
                ← Kembali ke Inbox Pesan Customer
            </a>
            <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Respon & Follow-up Sales</p>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                PERTANYAAN DARI: {{ strtoupper($contactMessage->name) }}
            </h1>
            <p class="text-xs text-silver-dim mt-1">
                Diterima pada {{ $contactMessage->created_at->format('d F Y, H:i:s') }} ({{ $contactMessage->created_at->diffForHumans() }})
            </p>
        </div>

        <div class="flex items-center gap-2">
            <form action="{{ route('admin.contact-messages.destroy', $contactMessage) }}" method="POST" onsubmit="return confirm('Hapus pesan kontak ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3 py-2 bg-red-950/40 hover:bg-red-900 border border-red-800 text-red-300 text-xs font-heading uppercase tracking-wider rounded transition-premium">
                    🗑️ Hapus Pesan
                </button>
            </form>
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Left: Customer Info Card --}}
        <div class="panel border border-gunmetal/70 bg-carbon p-6 space-y-4">
            <h2 class="font-heading text-sm uppercase tracking-wider text-silver-bright border-b border-gunmetal pb-2 flex items-center justify-between">
                <span>Profil Pengirim</span>
                @if($contactMessage->status === 'replied')
                    <span class="px-2 py-0.5 text-[9px] font-heading uppercase bg-emerald-950 text-emerald-400 border border-emerald-800 rounded font-bold">
                        ✓ Dibalas
                    </span>
                @else
                    <span class="px-2 py-0.5 text-[9px] font-heading uppercase bg-giallo text-carbon rounded font-bold">
                        Belum Dibalas
                    </span>
                @endif
            </h2>

            <div class="space-y-3 text-xs">
                <div>
                    <p class="text-silver-dim">Nama Lengkap</p>
                    <p class="font-medium text-silver-bright mt-0.5 text-sm">{{ $contactMessage->name }}</p>
                </div>
                <div>
                    <p class="text-silver-dim">Nomor WhatsApp / HP</p>
                    <p class="font-mono text-emerald-400 mt-0.5 text-sm flex items-center gap-1.5">
                        <span>💬</span> {{ $contactMessage->phone }}
                    </p>
                </div>
                <div>
                    <p class="text-silver-dim">Alamat Email</p>
                    <p class="text-silver-bright mt-0.5 font-mono">{{ $contactMessage->email }}</p>
                </div>
                <div>
                    <p class="text-silver-dim">Kategori Keperluan</p>
                    <p class="mt-0.5">
                        <span class="px-2 py-0.5 text-[10px] font-heading uppercase tracking-wider rounded
                            @if($contactMessage->topic === 'sales') bg-giallo/20 text-giallo border border-giallo/40
                            @elseif($contactMessage->topic === 'workshop') bg-blue-950/40 text-blue-300 border border-blue-800/40
                            @elseif($contactMessage->topic === 'parts') bg-purple-950/40 text-purple-300 border border-purple-800/40
                            @else bg-steel text-silver-dim border border-gunmetal @endif">
                            {{ strtoupper($contactMessage->topic) }}
                        </span>
                    </p>
                </div>
                <div>
                    <p class="text-silver-dim">Akun Terdaftar di Sistem</p>
                    <p class="mt-0.5">
                        @if($registeredUser)
                            <span class="text-emerald-400 font-semibold flex items-center gap-1">
                                ✓ Terdaftar (Role: {{ strtoupper($registeredUser->role) }})
                            </span>
                        @else
                            <span class="text-silver-dim">Tamu / Pengunjung Publik</span>
                        @endif
                    </p>
                </div>
            </div>

            {{-- Quick Action Buttons for Sales --}}
            <div class="pt-4 border-t border-gunmetal space-y-2">
                <p class="font-heading text-[10px] uppercase tracking-wider text-giallo font-bold">
                    Cara Cepat Sales Menghubungi:
                </p>
                <a href="{{ $waUrl }}" target="_blank" class="w-full py-2.5 px-3 bg-emerald-600 hover:bg-emerald-500 text-white font-heading text-xs uppercase tracking-wider font-bold rounded flex items-center justify-center gap-2 transition shadow">
                    <span>💬 Hubungi via WhatsApp VIP</span>
                </a>
                <a href="mailto:{{ $contactMessage->email }}?subject={{ urlencode('Tanggapan AUDrive Mengenai Keperluan ' . strtoupper($contactMessage->topic)) }}" class="w-full py-2 px-3 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright font-heading text-xs uppercase tracking-wider rounded flex items-center justify-center gap-2 transition">
                    <span>✉️ Kirim Email Langsung</span>
                </a>
            </div>
        </div>

        {{-- Right: Message Details & Sales Reply Form --}}
        <div class="md:col-span-2 space-y-6">
            {{-- Customer's Message Box --}}
            <div class="panel border border-gunmetal/70 bg-carbon p-6 space-y-3">
                <div class="flex items-center justify-between border-b border-gunmetal pb-2">
                    <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright font-bold flex items-center gap-2">
                        <span>💬</span> Pesan / Pertanyaan yang Diajukan Customer
                    </h3>
                    <span class="text-[10px] font-mono text-silver-dim">
                        {{ $contactMessage->created_at->format('d M Y, H:i') }}
                    </span>
                </div>
                <div class="bg-steel/30 border border-gunmetal/60 rounded p-4 text-silver-bright leading-relaxed text-sm whitespace-pre-wrap">
                    {{ $contactMessage->message ?: '(Pengirim tidak menuliskan detail pesan tambahan)' }}
                </div>
            </div>

            {{-- Previous Reply History if already replied --}}
            @if($contactMessage->reply)
                <div class="panel border border-emerald-800/60 bg-emerald-950/10 p-6 space-y-3">
                    <div class="flex items-center justify-between border-b border-emerald-800/40 pb-2">
                        <h3 class="font-heading text-xs uppercase tracking-wider text-emerald-400 font-bold flex items-center gap-2">
                            <span>✅</span> Tanggapan / Jawaban Sales Sebelumnya
                        </h3>
                        @if($contactMessage->replied_at)
                            <span class="text-[10px] font-mono text-emerald-300">
                                {{ $contactMessage->replied_at->format('d M Y, H:i') }}
                            </span>
                        @endif
                    </div>
                    @if($contactMessage->repliedBy)
                        <p class="text-[11px] text-silver-dim">
                            Dijawab oleh: <span class="font-semibold text-silver-bright">{{ $contactMessage->repliedBy->name }}</span> ({{ strtoupper($contactMessage->repliedBy->role) }})
                        </p>
                    @endif
                    <div class="bg-carbon/80 border border-emerald-900/60 rounded p-4 text-silver leading-relaxed text-xs whitespace-pre-wrap">
                        {{ $contactMessage->reply }}
                    </div>
                </div>
            @endif

            {{-- Sales Response Form --}}
            <div class="panel border border-gunmetal/70 bg-carbon p-6 space-y-4">
                <div class="border-b border-gunmetal pb-2">
                    <h3 class="font-heading text-xs uppercase tracking-wider text-giallo font-bold flex items-center gap-2">
                        <span>✍️</span> {{ $contactMessage->reply ? 'Perbarui Tanggapan / Jawaban Sales' : 'Jawab Pertanyaan Customer Ini' }}
                    </h3>
                    <p class="text-[11px] text-silver-dim mt-1">
                        Tuliskan jawaban atau catatan follow-up dari Sales. Jika customer memiliki akun terdaftar, jawaban ini akan langsung dikirimkan ke pusat notifikasi customer.
                    </p>
                </div>

                <form action="{{ route('admin.contact-messages.reply', $contactMessage) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="reply" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1 font-semibold">
                            Teks Jawaban / Respon Sales <span class="text-red-500">*</span>
                        </label>
                        <textarea name="reply" id="reply" rows="5" required placeholder="Tuliskan jawaban yang ramah dan profesional, misalnya informasi stok unit, konfirmasi simulasi kredit, ketersediaan servis, atau ringkasan hasil komunikasi via WhatsApp/telepon..." class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-3 focus:border-giallo focus:outline-none leading-relaxed">{{ old('reply', $contactMessage->reply) }}</textarea>
                        @error('reply') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-gunmetal/60">
                        <span class="text-[11px] text-silver-dim">
                            Status akan otomatis diubah menjadi <strong class="text-emerald-400">"Telah Dibalas"</strong>
                        </span>
                        <button type="submit" class="px-5 py-2.5 bg-giallo hover:bg-giallo-bright text-carbon font-heading text-xs uppercase tracking-wider font-bold rounded clip-angular-button transition-premium shadow">
                            ✓ Simpan Tanggapan Sales
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
