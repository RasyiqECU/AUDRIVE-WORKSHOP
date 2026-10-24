@extends('layouts.public')

@section('title', 'Tentang Kami — AUDrive Premium Automotive')
@section('meta_description', 'Pelajari lebih lanjut tentang AUDrive — dealer mobil premium, bengkel berstandar internasional, penyedia spare part orisinal, dan pionir teknologi AI diagnosis suara mesin di Indonesia.')

@section('content')
{{-- Hero Section (Fullscreen Cinematic Image 100vh) --}}
<section class="relative w-full h-screen min-h-[650px] overflow-hidden flex items-center justify-center">
    {{-- Background Image (Fullscreen, object-fit: cover) --}}
    <img src="{{ \App\Services\SettingService::getAboutBackground() }}"
         alt="AUDrive — Tentang Kami"
         class="absolute inset-0 w-full h-full object-cover object-center pointer-events-none select-none z-0" />

    {{-- Cinematic Dark Gradients & Overlays --}}
    <div class="absolute inset-x-0 top-0 h-44 bg-gradient-to-b from-nero/90 via-nero/50 to-transparent z-10 pointer-events-none"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-nero/90 via-nero/40 to-nero/70 z-10 pointer-events-none"></div>
    <div class="absolute inset-0 bg-nero/25 z-10 pointer-events-none"></div>
    <div class="absolute inset-x-0 bottom-0 h-48 bg-gradient-to-t from-nero via-nero/80 to-transparent z-10 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 right-0 h-1 bg-hazard-stripes opacity-40 z-20"></div>

    {{-- Hero Content --}}
    <div class="relative z-20 container mx-auto px-4 md:px-8 py-20 flex flex-col justify-between h-full max-h-screen">
        <div class="h-16"></div>

        <div class="max-w-3xl my-auto">
            <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 bg-nero/70 border border-giallo/40 backdrop-blur-md rounded mb-6">
                <span class="inline-block w-2 h-2 bg-giallo rounded-full animate-pulse shadow-sm shadow-giallo"></span>
                <span class="font-heading text-[11px] uppercase tracking-[0.35em] text-giallo font-bold">
                    The AUDrive Legacy
                </span>
            </div>

            <h1 class="font-display text-4xl sm:text-6xl lg:text-7xl font-black text-silver-bright tracking-wider leading-[1.05] uppercase drop-shadow-2xl">
                MENDEFINISI ULANG <span class="text-giallo">PENGALAMAN</span><br />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-silver-bright via-silver to-silver-dim">
                    OTOMOTIF PREMIUM.
                </span>
            </h1>

            <p class="font-body text-sm sm:text-base md:text-lg text-silver-bright/90 mt-6 max-w-xl leading-relaxed drop-shadow">
                AUDrive hadir sebagai ekosistem otomotif terpadu — dari kurasi unit pilihan, bengkel berstandar pabrikan, suku cadang orisinal, hingga diagnosis suara mesin berbasis AI.
            </p>

            <div class="flex flex-wrap items-center gap-4 mt-8">
                <x-ui.button href="{{ route('marketplace.index') }}" variant="primary" size="lg" class="shadow-xl shadow-giallo/20 font-bold tracking-wider">
                    🏎️ Lihat Koleksi Unit
                </x-ui.button>
                <x-ui.button href="{{ route('workshop.index') }}" variant="secondary" size="lg" class="backdrop-blur-md bg-carbon/80 border-white/20 hover:border-giallo/50 tracking-wider">
                    🔧 Layanan Bengkel
                </x-ui.button>
                <x-ui.button href="{{ route('parts.index') }}" variant="ghost" size="lg" class="backdrop-blur-md bg-nero/40 border border-white/10 hover:border-white/30 text-silver-bright tracking-wider">
                    ⚙️ Katalog Spare Part
                </x-ui.button>
            </div>
        </div>

        <div class="pb-6 flex items-center justify-between text-silver-dim text-xs font-mono">
            <div class="flex items-center gap-3">
                <span class="text-giallo font-bold">AUDRIVE</span>
                <span class="h-3 w-px bg-gunmetal"></span>
                <span class="uppercase tracking-widest text-[10px]">Our Story & Heritage</span>
            </div>
            <a href="#about-content" class="flex items-center gap-2 hover:text-giallo transition group">
                <span class="uppercase tracking-wider text-[10px] hidden sm:inline">Pelajari Lebih</span>
                <div class="w-5 h-8 border border-silver-dim/60 group-hover:border-giallo rounded-full flex items-start justify-center p-1 transition">
                    <div class="w-1 h-2 bg-giallo rounded-full animate-bounce"></div>
                </div>
            </a>
        </div>
    </div>
</section>

{{-- Anchor for scroll --}}
<div id="about-content"></div>

{{-- Key Statistics --}}
<section class="bg-carbon border-b border-gunmetal/40 py-10">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <div class="p-4 border border-gunmetal/30 bg-steel/20 rounded">
                <div class="font-display text-3xl md:text-4xl font-bold text-giallo">10+</div>
                <div class="font-heading text-xs uppercase tracking-wider text-silver-bright mt-1">Tahun Pengalaman</div>
                <p class="font-body text-[11px] text-silver-dim mt-1">Melayani supercar & luxury car</p>
            </div>
            <div class="p-4 border border-gunmetal/30 bg-steel/20 rounded">
                <div class="font-display text-3xl md:text-4xl font-bold text-giallo">500+</div>
                <div class="font-heading text-xs uppercase tracking-wider text-silver-bright mt-1">Unit Terkirim</div>
                <p class="font-body text-[11px] text-silver-dim mt-1">Ke seluruh pelosok nusantara</p>
            </div>
            <div class="p-4 border border-gunmetal/30 bg-steel/20 rounded">
                <div class="font-display text-3xl md:text-4xl font-bold text-giallo">150+</div>
                <div class="font-heading text-xs uppercase tracking-wider text-silver-bright mt-1">Titik Inspeksi</div>
                <p class="font-body text-[11px] text-silver-dim mt-1">Sertifikasi kondisi transparan</p>
            </div>
            <div class="p-4 border border-gunmetal/30 bg-steel/20 rounded">
                <div class="font-display text-3xl md:text-4xl font-bold text-giallo">99.8%</div>
                <div class="font-heading text-xs uppercase tracking-wider text-silver-bright mt-1">Kepuasan Klien</div>
                <p class="font-body text-[11px] text-silver-dim mt-1">Layanan VIP purna jual</p>
            </div>
        </div>
    </div>
</section>

{{-- Vision & Mission --}}
<section class="container mx-auto px-4 py-16">
    <div class="grid md:grid-cols-2 gap-12 items-center">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <div class="h-px w-6 bg-giallo"></div>
                <span class="font-heading text-xs uppercase tracking-[0.25em] text-giallo font-semibold">Visi & Misi</span>
            </div>
            <h2 class="font-display text-2xl md:text-4xl font-bold text-silver-bright mb-6">
                INTEGRITAS TANPA KOMPROMI & PREMISI TEKNOLOGI
            </h2>
            <div class="space-y-4 font-body text-silver-dim text-sm leading-relaxed">
                <p>
                    Kami percaya bahwa memiliki mobil premium bukan sekadar transaksi, melainkan perjalanan kepuasan berkendara. Oleh karena itu, AUDrive mengeliminasi keraguan dalam jual beli mobil premium dengan standarisasi inspeksi tanpa toleransi terhadap cacat tersembunyi.
                </p>
                <p>
                    Didukung divisi mekanik master bersertifikasi internasional dan alat diagnostik mutakhir, kami memastikan setiap deru mesin berputar pada performa puncaknya.
                </p>
            </div>

            <div class="mt-8 space-y-3">
                <div class="flex items-start gap-3 bg-carbon p-3 border border-gunmetal/30">
                    <span class="text-giallo text-lg">🛡️</span>
                    <div>
                        <h4 class="font-heading text-xs uppercase tracking-wider text-silver-bright font-semibold">Garansi Keaslian & Legalitas 100%</h4>
                        <p class="font-body text-xs text-silver-dim">Dokumen BPKB, Form A, Faktur, dan riwayat servis terverifikasi lengkap.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 bg-carbon p-3 border border-gunmetal/30">
                    <span class="text-giallo text-lg">🤖</span>
                    <div>
                        <h4 class="font-heading text-xs uppercase tracking-wider text-silver-bright font-semibold">Teknologi AI Engine Sound Scan</h4>
                        <p class="font-body text-xs text-silver-dim">Deteksi anomali ruang bakar, valvetrain, dan bearing via frekuensi akustik mesin.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 bg-carbon p-3 border border-gunmetal/30">
                    <span class="text-giallo text-lg">⚙️</span>
                    <div>
                        <h4 class="font-heading text-xs uppercase tracking-wider text-silver-bright font-semibold">Suku Cadang Asli & Aksesori Teruji</h4>
                        <p class="font-body text-xs text-silver-dim">Ketersediaan part OEM resmi dan komponen upgrade performa bergaransi.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Visual Card Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-ui.card class="p-6 bg-carbon border-gunmetal hover:border-giallo/40 transition-premium">
                <span class="text-3xl block mb-3">🏎️</span>
                <h3 class="font-heading text-sm font-semibold text-silver-bright uppercase tracking-wider mb-2">Showroom Kurasi</h3>
                <p class="font-body text-xs text-silver-dim leading-relaxed">
                    Supercar, sportscar, dan luxury SUV pilihan terbaik dengan jarak tempuh asli dan rekam jejak bebas tabrakan.
                </p>
            </x-ui.card>

            <x-ui.card class="p-6 bg-carbon border-gunmetal hover:border-giallo/40 transition-premium">
                <span class="text-3xl block mb-3">🔧</span>
                <h3 class="font-heading text-sm font-semibold text-silver-bright uppercase tracking-wider mb-2">Workshop Presisi</h3>
                <p class="font-body text-xs text-silver-dim leading-relaxed">
                    Perawatan berkala, perbaikan transmisi canggih, tune-up, ECU remapping, hingga overhaul mesin presisi.
                </p>
            </x-ui.card>

            <x-ui.card class="p-6 bg-carbon border-gunmetal hover:border-giallo/40 transition-premium">
                <span class="text-3xl block mb-3">🔩</span>
                <h3 class="font-heading text-sm font-semibold text-silver-bright uppercase tracking-wider mb-2">Spare Part Store</h3>
                <p class="font-body text-xs text-silver-dim leading-relaxed">
                    Katalog online spare part orisinal, kampas rem karbon keramik, filter balap, dan oli performa tinggi.
                </p>
            </x-ui.card>

            <x-ui.card class="p-6 bg-carbon border-gunmetal hover:border-giallo/40 transition-premium">
                <span class="text-3xl block mb-3">💎</span>
                <h3 class="font-heading text-sm font-semibold text-silver-bright uppercase tracking-wider mb-2">VIP Concierge</h3>
                <p class="font-body text-xs text-silver-dim leading-relaxed">
                    Towing tertutup (enclosed towing) 24/7, pengurusan surat kendaraan khusus, dan konsultasi privat.
                </p>
            </x-ui.card>
        </div>
    </div>
</section>

{{-- Facilities & Workshop Standards --}}
<section class="bg-carbon border-y border-gunmetal/40 py-16">
    <div class="container mx-auto px-4">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <div class="flex items-center justify-center gap-2 mb-2">
                <span class="inline-block w-2 h-2 bg-giallo rounded-full"></span>
                <span class="font-heading text-xs uppercase tracking-[0.25em] text-giallo font-semibold">Standard & Fasilitas</span>
            </div>
            <h2 class="font-display text-2xl md:text-3xl font-bold text-silver-bright">
                FASILITAS BENGKEL KELAS DUNIA
            </h2>
            <p class="font-body text-xs md:text-sm text-silver-dim mt-2">
                Dirancang untuk memenuhi spesifikasi ketat supercar Eropa dan mobil premium Asia.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-nero border border-gunmetal/50 p-6 rounded relative overflow-hidden group">
                <div class="h-1 w-full bg-giallo absolute top-0 left-0"></div>
                <div class="font-mono text-xs text-giallo mb-2">01 / DIAGNOSTICS</div>
                <h3 class="font-heading text-base font-semibold text-silver-bright mb-2">OEM Scanner & AI Sound Scan</h3>
                <p class="font-body text-xs text-silver-dim leading-relaxed">
                    Software diagnostik resmi PIWIS (Porsche), ISTA (BMW), Star Diagnosis (Mercedes-Benz) dipadukan modul kecerdasan buatan AUDrive.
                </p>
            </div>

            <div class="bg-nero border border-gunmetal/50 p-6 rounded relative overflow-hidden group">
                <div class="h-1 w-full bg-giallo absolute top-0 left-0"></div>
                <div class="font-mono text-xs text-giallo mb-2">02 / CLEANROOM</div>
                <h3 class="font-heading text-base font-semibold text-silver-bright mb-2">Engine & Gearbox Cleanroom</h3>
                <p class="font-body text-xs text-silver-dim leading-relaxed">
                    Ruang tertutup bertekanan positif dengan kontrol partikel debu untuk perakitan blok mesin twin-turbo dan transmisi kopling ganda (DCT).
                </p>
            </div>

            <div class="bg-nero border border-gunmetal/50 p-6 rounded relative overflow-hidden group">
                <div class="h-1 w-full bg-giallo absolute top-0 left-0"></div>
                <div class="font-mono text-xs text-giallo mb-2">03 / PRECISION</div>
                <h3 class="font-heading text-base font-semibold text-silver-bright mb-2">3D Wheel Alignment & Dyno Test</h3>
                <p class="font-body text-xs text-silver-dim leading-relaxed">
                    Kalibrasi geometri kaki-kaki presisi tingkat milimeter untuk kestabilan aerodinamika pada kecepatan tinggi.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- CTA Section --}}
<section class="container mx-auto px-4 py-16">
    <div class="bg-gradient-to-r from-carbon via-steel/20 to-carbon border border-giallo/30 p-8 md:p-12 rounded-lg text-center max-w-4xl mx-auto shadow-2xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-giallo/10 rounded-full blur-2xl"></div>
        <h2 class="font-display text-2xl md:text-4xl font-bold text-silver-bright mb-4">
            SIAP MERASAKAN PENGALAMAN <span class="text-giallo">AUDRIVE</span>?
        </h2>
        <p class="font-body text-sm md:text-base text-silver-dim max-w-xl mx-auto mb-8">
            Kunjungi showroom kami atau jadwalkan inspeksi dan servis kendaraan impian Anda bersama tim spesialis kami.
        </p>
        <div class="flex flex-wrap items-center justify-center gap-4">
            <x-ui.button href="{{ route('marketplace.index') }}" variant="primary" size="lg">
                Jelajahi Mobil
            </x-ui.button>
            <x-ui.button href="{{ route('contact') }}" variant="secondary" size="lg">
                Hubungi Kami
            </x-ui.button>
        </div>
    </div>
</section>
@endsection
