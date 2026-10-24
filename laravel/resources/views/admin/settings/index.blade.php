@extends('layouts.admin')

@section('title', 'Konfigurasi Media & Branding Sistem')

@section('breadcrumb')
    <span class="text-silver-bright font-heading uppercase tracking-wider">Konfigurasi Sistem</span>
@endsection

@section('content')
<div class="space-y-8">
    {{-- Header Page --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-gunmetal pb-6">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">
                    MEDIA & BRANDING SISTEM
                </h1>
                <span class="px-2.5 py-0.5 rounded border border-giallo/40 bg-giallo/10 text-giallo font-heading text-[10px] font-bold uppercase tracking-widest">
                    Super Admin Only
                </span>
            </div>
            <p class="text-sm text-silver-dim mt-1.5 max-w-2xl">
                Kustomisasi visual GIF/gambar latar belakang untuk seluruh modul (Login, Dashboard, Bengkel, Marketplace, Sparepart, Tentang, Kontak) serta logo dan identitas AUDrive.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2 shrink-0">
            <a href="{{ route('home') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright font-heading text-[11px] uppercase tracking-wider transition-premium">
                🏁 Home
            </a>
            <a href="{{ route('marketplace.index') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright font-heading text-[11px] uppercase tracking-wider transition-premium">
                🏎️ Showroom
            </a>
            <a href="{{ route('workshop.index') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright font-heading text-[11px] uppercase tracking-wider transition-premium">
                🔧 Bengkel
            </a>
            <a href="{{ route('parts.index') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright font-heading text-[11px] uppercase tracking-wider transition-premium">
                ⚙️ Sparepart
            </a>
            <a href="{{ route('about') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright font-heading text-[11px] uppercase tracking-wider transition-premium">
                🏛️ Tentang
            </a>
            <a href="{{ route('contact') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright font-heading text-[11px] uppercase tracking-wider transition-premium">
                📞 Kontak
            </a>
            <a href="{{ route('login') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-giallo/10 border border-giallo/40 text-giallo font-heading text-[11px] uppercase tracking-wider transition-premium">
                🎬 Login Page
            </a>
        </div>
    </div>

    {{-- Main Form --}}
    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        {{-- BAGIAN 1: Identitas & Logo Brand --}}
        <div class="panel border border-gunmetal bg-carbon p-6 space-y-6">
            <div class="border-b border-gunmetal pb-4 flex items-center justify-between flex-wrap gap-2">
                <div>
                    <h2 class="font-heading text-sm uppercase tracking-wider text-giallo font-bold flex items-center gap-2">
                        <span class="text-base">🏷️</span> Identitas Brand & Logo
                    </h2>
                    <p class="text-xs text-silver-dim mt-0.5">
                        Logo resmi dan identitas teks yang tampil pada navbar, footer, sidebar, dan judul web.
                    </p>
                </div>
                @if($settings['custom_logo'])
                    <span class="text-[10px] font-mono text-verde bg-verde/10 border border-verde/30 px-2 py-0.5 rounded">
                        Logo Custom Aktif
                    </span>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Logo Upload --}}
                <div class="space-y-3">
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright">
                        Logo Aplikasi
                    </label>
                    <div class="flex items-center gap-4 bg-nero p-3 border border-gunmetal">
                        <div class="w-16 h-16 bg-steel/20 border border-gunmetal rounded p-1 flex items-center justify-center shrink-0">
                            <img id="preview-logo-img" src="{{ $settings['app_logo'] }}" alt="Logo" class="max-w-full max-h-full object-contain">
                        </div>
                        <div class="flex-1 overflow-hidden">
                            <p class="text-xs font-heading text-silver-bright truncate">
                                {{ $settings['custom_logo'] ? basename($settings['custom_logo']) : 'AUDrive-logo.png' }}
                            </p>
                            <p class="text-[10px] text-silver-dim mt-0.5 font-mono">
                                {{ $settings['custom_logo'] ? 'File kustom tersimpan' : 'Aset bawaan sistem' }}
                            </p>
                            @if($settings['custom_logo'])
                                <button type="button" onclick="submitReset('logo')" class="text-[10px] text-red-400 hover:text-red-300 font-heading uppercase mt-1">
                                    [Reset ke Default]
                                </button>
                            @endif
                        </div>
                    </div>
                    <input type="file" 
                           id="logo_file" 
                           name="logo_file" 
                           accept=".png,.jpg,.jpeg,.webp,.svg,.gif" 
                           class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none file:mr-3 file:py-1 file:px-3 file:border-0 file:text-[10px] file:font-heading file:uppercase file:bg-steel file:text-silver-bright hover:file:bg-giallo hover:file:text-nero cursor-pointer" 
                           onchange="previewImage(event, 'preview-logo-img')" />
                </div>

                {{-- Nama & Tagline --}}
                <div class="space-y-4 md:col-span-2">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label for="app_name" class="block font-heading text-xs uppercase tracking-wider text-silver-bright">
                                Nama Aplikasi <span class="text-giallo">*</span>
                            </label>
                            <input type="text" 
                                   id="app_name" 
                                   name="app_name" 
                                   value="{{ old('app_name', $settings['app_name']) }}" 
                                   required
                                   maxlength="100"
                                   placeholder="Contoh: AUDRIVE"
                                   class="w-full bg-nero border border-gunmetal px-3.5 py-2 text-silver-bright font-display text-sm tracking-wider focus:border-giallo focus:outline-none transition-premium" />
                        </div>
                        <div class="space-y-1.5">
                            <label for="app_tagline" class="block font-heading text-xs uppercase tracking-wider text-silver-bright">
                                Tagline / Slogan
                            </label>
                            <input type="text" 
                                   id="app_tagline" 
                                   name="app_tagline" 
                                   value="{{ old('app_tagline', $settings['app_tagline']) }}" 
                                   maxlength="200"
                                   placeholder="Contoh: PREMIUM AUTOMOTIVE DEALER"
                                   class="w-full bg-nero border border-gunmetal px-3.5 py-2 text-silver-bright font-heading text-xs tracking-wider uppercase focus:border-giallo focus:outline-none transition-premium" />
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label for="app_description" class="block font-heading text-xs uppercase tracking-wider text-silver-bright">
                            Deskripsi Ringkas Perusahaan
                        </label>
                        <textarea id="app_description" 
                                  name="app_description" 
                                  rows="2"
                                  maxlength="500"
                                  placeholder="Deskripsi singkat untuk meta tags dan footer..."
                                  class="w-full bg-nero border border-gunmetal px-3.5 py-2 text-silver-bright font-body text-xs focus:border-giallo focus:outline-none transition-premium">{{ old('app_description', $settings['app_description']) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- BAGIAN 2: Media Background / GIF Per Halaman & Modul --}}
        <div>
            <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                <div>
                    <h2 class="font-heading text-sm uppercase tracking-wider text-silver-bright font-bold flex items-center gap-2">
                        <span class="text-base">🎬</span> Gambar / GIF Latar Belakang Per Halaman
                    </h2>
                    <p class="text-xs text-silver-dim mt-0.5">
                        Super Admin dapat mengganti banner GIF atau foto resolusi tinggi untuk setiap segmen halaman secara independen.
                    </p>
                </div>
                <span class="text-[10px] font-mono text-silver-dim">Mendukung format: GIF, JPG, PNG, WEBP, MP4 (Maks. 30 MB)</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- 1. Halaman Login --}}
                <div class="panel border border-gunmetal bg-carbon p-5 space-y-4 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="font-heading text-xs uppercase tracking-wider text-giallo font-bold flex items-center gap-1.5">
                                🔐 Halaman Login & Registrasi
                            </h3>
                            @if($settings['custom_login_bg'])
                                <span class="text-[9px] font-mono text-verde bg-verde/10 border border-verde/30 px-1.5 py-0.5 rounded">Custom</span>
                            @else
                                <span class="text-[9px] font-mono text-silver-dim bg-steel/30 px-1.5 py-0.5 rounded">Default</span>
                            @endif
                        </div>
                        <p class="text-[11px] text-silver-dim leading-relaxed">
                            Latar belakang animasi visual pada halaman Login, Register, dan Reset Password.
                        </p>
                        <div class="relative h-32 bg-nero border border-gunmetal overflow-hidden rounded group">
                            <img id="preview-login-bg-img" src="{{ $settings['login_bg'] }}" alt="Login BG" class="w-full h-full object-cover">
                            <div class="absolute bottom-1 right-1 bg-nero/80 px-2 py-0.5 text-[9px] font-mono text-silver-bright rounded">
                                {{ $settings['custom_login_bg'] ? 'Custom File' : 'lambo.gif' }}
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2 pt-2 border-t border-gunmetal/60">
                        <div class="flex items-center justify-between text-[11px]">
                            <label for="login_bg_file" class="font-heading uppercase tracking-wider text-silver-bright">Unggah File Baru</label>
                            @if($settings['custom_login_bg'])
                                <button type="button" onclick="submitReset('login_bg')" class="text-red-400 hover:text-red-300 font-mono text-[10px]">Reset</button>
                            @endif
                        </div>
                        <input type="file" 
                               id="login_bg_file" 
                               name="login_bg_file" 
                               accept=".gif,.jpg,.jpeg,.png,.webp,.mp4" 
                               class="w-full bg-nero border border-gunmetal px-2.5 py-1.5 text-xs text-silver-bright focus:border-giallo file:mr-2 file:py-1 file:px-2 file:border-0 file:text-[10px] file:font-heading file:uppercase file:bg-steel file:text-silver-bright hover:file:bg-giallo hover:file:text-nero cursor-pointer" 
                               onchange="previewImage(event, 'preview-login-bg-img')" />
                    </div>
                </div>

                {{-- 2. Halaman Dashboard --}}
                <div class="panel border border-gunmetal bg-carbon p-5 space-y-4 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="font-heading text-xs uppercase tracking-wider text-giallo font-bold flex items-center gap-1.5">
                                📊 Dashboard Admin & Customer
                            </h3>
                            @if($settings['custom_dashboard_bg'])
                                <span class="text-[9px] font-mono text-verde bg-verde/10 border border-verde/30 px-1.5 py-0.5 rounded">Custom</span>
                            @else
                                <span class="text-[9px] font-mono text-silver-dim bg-steel/30 px-1.5 py-0.5 rounded">Default</span>
                            @endif
                        </div>
                        <p class="text-[11px] text-silver-dim leading-relaxed">
                            Hero banner latar belakang pada dashboard staf internal dan dashboard portal customer.
                        </p>
                        <div class="relative h-32 bg-nero border border-gunmetal overflow-hidden rounded group">
                            <img id="preview-dashboard-bg-img" src="{{ $settings['dashboard_bg'] }}" alt="Dashboard BG" class="w-full h-full object-cover">
                            <div class="absolute bottom-1 right-1 bg-nero/80 px-2 py-0.5 text-[9px] font-mono text-silver-bright rounded">
                                {{ $settings['custom_dashboard_bg'] ? 'Custom File' : 'lambo.gif' }}
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2 pt-2 border-t border-gunmetal/60">
                        <div class="flex items-center justify-between text-[11px]">
                            <label for="dashboard_bg_file" class="font-heading uppercase tracking-wider text-silver-bright">Unggah File Baru</label>
                            @if($settings['custom_dashboard_bg'])
                                <button type="button" onclick="submitReset('dashboard_bg')" class="text-red-400 hover:text-red-300 font-mono text-[10px]">Reset</button>
                            @endif
                        </div>
                        <input type="file" 
                               id="dashboard_bg_file" 
                               name="dashboard_bg_file" 
                               accept=".gif,.jpg,.jpeg,.png,.webp,.mp4" 
                               class="w-full bg-nero border border-gunmetal px-2.5 py-1.5 text-xs text-silver-bright focus:border-giallo file:mr-2 file:py-1 file:px-2 file:border-0 file:text-[10px] file:font-heading file:uppercase file:bg-steel file:text-silver-bright hover:file:bg-giallo hover:file:text-nero cursor-pointer" 
                               onchange="previewImage(event, 'preview-dashboard-bg-img')" />
                    </div>
                </div>

                {{-- 3. Halaman Bengkel & Workshop --}}
                <div class="panel border border-gunmetal bg-carbon p-5 space-y-4 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="font-heading text-xs uppercase tracking-wider text-giallo font-bold flex items-center gap-1.5">
                                🔧 Halaman Bengkel & Workshop
                            </h3>
                            @if($settings['custom_workshop_bg'])
                                <span class="text-[9px] font-mono text-verde bg-verde/10 border border-verde/30 px-1.5 py-0.5 rounded">Custom</span>
                            @else
                                <span class="text-[9px] font-mono text-silver-dim bg-steel/30 px-1.5 py-0.5 rounded">Default</span>
                            @endif
                        </div>
                        <p class="text-[11px] text-silver-dim leading-relaxed">
                            Hero banner layar penuh animasi pada halaman utama layanan bengkel AUDrive Workshop.
                        </p>
                        <div class="relative h-32 bg-nero border border-gunmetal overflow-hidden rounded group">
                            <img id="preview-workshop-bg-img" src="{{ $settings['workshop_bg'] }}" alt="Workshop BG" class="w-full h-full object-cover">
                            <div class="absolute bottom-1 right-1 bg-nero/80 px-2 py-0.5 text-[9px] font-mono text-silver-bright rounded">
                                {{ $settings['custom_workshop_bg'] ? 'Custom File' : 'bengkel.gif' }}
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2 pt-2 border-t border-gunmetal/60">
                        <div class="flex items-center justify-between text-[11px]">
                            <label for="workshop_bg_file" class="font-heading uppercase tracking-wider text-silver-bright">Unggah File Baru</label>
                            @if($settings['custom_workshop_bg'])
                                <button type="button" onclick="submitReset('workshop_bg')" class="text-red-400 hover:text-red-300 font-mono text-[10px]">Reset</button>
                            @endif
                        </div>
                        <input type="file" 
                               id="workshop_bg_file" 
                               name="workshop_bg_file" 
                               accept=".gif,.jpg,.jpeg,.png,.webp,.mp4" 
                               class="w-full bg-nero border border-gunmetal px-2.5 py-1.5 text-xs text-silver-bright focus:border-giallo file:mr-2 file:py-1 file:px-2 file:border-0 file:text-[10px] file:font-heading file:uppercase file:bg-steel file:text-silver-bright hover:file:bg-giallo hover:file:text-nero cursor-pointer" 
                               onchange="previewImage(event, 'preview-workshop-bg-img')" />
                    </div>
                </div>

                {{-- 4. Halaman Marketplace Showroom --}}
                <div class="panel border border-gunmetal bg-carbon p-5 space-y-4 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="font-heading text-xs uppercase tracking-wider text-giallo font-bold flex items-center gap-1.5">
                                🏎️ Halaman Marketplace Supercar
                            </h3>
                            @if($settings['custom_marketplace_bg'])
                                <span class="text-[9px] font-mono text-verde bg-verde/10 border border-verde/30 px-1.5 py-0.5 rounded">Custom</span>
                            @else
                                <span class="text-[9px] font-mono text-silver-dim bg-steel/30 px-1.5 py-0.5 rounded">Default</span>
                            @endif
                        </div>
                        <p class="text-[11px] text-silver-dim leading-relaxed">
                            Hero banner animasi supercar pada katalog marketplace dan showroom mobil premium.
                        </p>
                        <div class="relative h-32 bg-nero border border-gunmetal overflow-hidden rounded group">
                            <img id="preview-marketplace-bg-img" src="{{ $settings['marketplace_bg'] }}" alt="Marketplace BG" class="w-full h-full object-cover">
                            <div class="absolute bottom-1 right-1 bg-nero/80 px-2 py-0.5 text-[9px] font-mono text-silver-bright rounded">
                                {{ $settings['custom_marketplace_bg'] ? 'Custom File' : 'marketplace.gif' }}
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2 pt-2 border-t border-gunmetal/60">
                        <div class="flex items-center justify-between text-[11px]">
                            <label for="marketplace_bg_file" class="font-heading uppercase tracking-wider text-silver-bright">Unggah File Baru</label>
                            @if($settings['custom_marketplace_bg'])
                                <button type="button" onclick="submitReset('marketplace_bg')" class="text-red-400 hover:text-red-300 font-mono text-[10px]">Reset</button>
                            @endif
                        </div>
                        <input type="file" 
                               id="marketplace_bg_file" 
                               name="marketplace_bg_file" 
                               accept=".gif,.jpg,.jpeg,.png,.webp,.mp4" 
                               class="w-full bg-nero border border-gunmetal px-2.5 py-1.5 text-xs text-silver-bright focus:border-giallo file:mr-2 file:py-1 file:px-2 file:border-0 file:text-[10px] file:font-heading file:uppercase file:bg-steel file:text-silver-bright hover:file:bg-giallo hover:file:text-nero cursor-pointer" 
                               onchange="previewImage(event, 'preview-marketplace-bg-img')" />
                    </div>
                </div>

                {{-- 5. Halaman Sparepart --}}
                <div class="panel border border-gunmetal bg-carbon p-5 space-y-4 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="font-heading text-xs uppercase tracking-wider text-giallo font-bold flex items-center gap-1.5">
                                ⚙️ Halaman Suku Cadang & Spare Part
                            </h3>
                            @if($settings['custom_sparepart_bg'])
                                <span class="text-[9px] font-mono text-verde bg-verde/10 border border-verde/30 px-1.5 py-0.5 rounded">Custom</span>
                            @else
                                <span class="text-[9px] font-mono text-silver-dim bg-steel/30 px-1.5 py-0.5 rounded">Default</span>
                            @endif
                        </div>
                        <p class="text-[11px] text-silver-dim leading-relaxed">
                            Hero banner animasi visual pada etalase toko dan katalog pembelian suku cadang resmi.
                        </p>
                        <div class="relative h-32 bg-nero border border-gunmetal overflow-hidden rounded group">
                            <img id="preview-sparepart-bg-img" src="{{ $settings['sparepart_bg'] }}" alt="Sparepart BG" class="w-full h-full object-cover">
                            <div class="absolute bottom-1 right-1 bg-nero/80 px-2 py-0.5 text-[9px] font-mono text-silver-bright rounded">
                                {{ $settings['custom_sparepart_bg'] ? 'Custom File' : 'sparepart.gif' }}
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2 pt-2 border-t border-gunmetal/60">
                        <div class="flex items-center justify-between text-[11px]">
                            <label for="sparepart_bg_file" class="font-heading uppercase tracking-wider text-silver-bright">Unggah File Baru</label>
                            @if($settings['custom_sparepart_bg'])
                                <button type="button" onclick="submitReset('sparepart_bg')" class="text-red-400 hover:text-red-300 font-mono text-[10px]">Reset</button>
                            @endif
                        </div>
                        <input type="file" 
                               id="sparepart_bg_file" 
                               name="sparepart_bg_file" 
                               accept=".gif,.jpg,.jpeg,.png,.webp,.mp4" 
                               class="w-full bg-nero border border-gunmetal px-2.5 py-1.5 text-xs text-silver-bright focus:border-giallo file:mr-2 file:py-1 file:px-2 file:border-0 file:text-[10px] file:font-heading file:uppercase file:bg-steel file:text-silver-bright hover:file:bg-giallo hover:file:text-nero cursor-pointer" 
                               onchange="previewImage(event, 'preview-sparepart-bg-img')" />
                    </div>
                </div>

                {{-- 6. Halaman Tentang Kami --}}
                <div class="panel border border-gunmetal bg-carbon p-5 space-y-4 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="font-heading text-xs uppercase tracking-wider text-giallo font-bold flex items-center gap-1.5">
                                🏛️ Halaman Tentang Kami (About Us)
                            </h3>
                            @if($settings['custom_about_bg'])
                                <span class="text-[9px] font-mono text-verde bg-verde/10 border border-verde/30 px-1.5 py-0.5 rounded">Custom</span>
                            @else
                                <span class="text-[9px] font-mono text-silver-dim bg-steel/30 px-1.5 py-0.5 rounded">Default</span>
                            @endif
                        </div>
                        <p class="text-[11px] text-silver-dim leading-relaxed">
                            Hero banner sinematik layar penuh pada halaman profil, filosofi, dan sejarah AUDrive.
                        </p>
                        <div class="relative h-32 bg-nero border border-gunmetal overflow-hidden rounded group">
                            <img id="preview-about-bg-img" src="{{ $settings['about_bg'] }}" alt="About BG" class="w-full h-full object-cover">
                            <div class="absolute bottom-1 right-1 bg-nero/80 px-2 py-0.5 text-[9px] font-mono text-silver-bright rounded">
                                {{ $settings['custom_about_bg'] ? 'Custom File' : 'tentang.jpeg' }}
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2 pt-2 border-t border-gunmetal/60">
                        <div class="flex items-center justify-between text-[11px]">
                            <label for="about_bg_file" class="font-heading uppercase tracking-wider text-silver-bright">Unggah File Baru</label>
                            @if($settings['custom_about_bg'])
                                <button type="button" onclick="submitReset('about_bg')" class="text-red-400 hover:text-red-300 font-mono text-[10px]">Reset</button>
                            @endif
                        </div>
                        <input type="file" 
                               id="about_bg_file" 
                               name="about_bg_file" 
                               accept=".gif,.jpg,.jpeg,.png,.webp,.mp4" 
                               class="w-full bg-nero border border-gunmetal px-2.5 py-1.5 text-xs text-silver-bright focus:border-giallo file:mr-2 file:py-1 file:px-2 file:border-0 file:text-[10px] file:font-heading file:uppercase file:bg-steel file:text-silver-bright hover:file:bg-giallo hover:file:text-nero cursor-pointer" 
                               onchange="previewImage(event, 'preview-about-bg-img')" />
                    </div>
                </div>

                {{-- 7. Halaman Kontak Kami --}}
                <div class="panel border border-gunmetal bg-carbon p-5 space-y-4 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="font-heading text-xs uppercase tracking-wider text-giallo font-bold flex items-center gap-1.5">
                                📞 Halaman Kontak & Bantuan VIP
                            </h3>
                            @if($settings['custom_contact_bg'])
                                <span class="text-[9px] font-mono text-verde bg-verde/10 border border-verde/30 px-1.5 py-0.5 rounded">Custom</span>
                            @else
                                <span class="text-[9px] font-mono text-silver-dim bg-steel/30 px-1.5 py-0.5 rounded">Default</span>
                            @endif
                        </div>
                        <p class="text-[11px] text-silver-dim leading-relaxed">
                            Hero banner visual pada halaman concierge, layanan pelanggan, dan lokasi cabang.
                        </p>
                        <div class="relative h-32 bg-nero border border-gunmetal overflow-hidden rounded group">
                            <img id="preview-contact-bg-img" src="{{ $settings['contact_bg'] }}" alt="Contact BG" class="w-full h-full object-cover">
                            <div class="absolute bottom-1 right-1 bg-nero/80 px-2 py-0.5 text-[9px] font-mono text-silver-bright rounded">
                                {{ $settings['custom_contact_bg'] ? 'Custom File' : 'kontak.jpg' }}
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2 pt-2 border-t border-gunmetal/60">
                        <div class="flex items-center justify-between text-[11px]">
                            <label for="contact_bg_file" class="font-heading uppercase tracking-wider text-silver-bright">Unggah File Baru</label>
                            @if($settings['custom_contact_bg'])
                                <button type="button" onclick="submitReset('contact_bg')" class="text-red-400 hover:text-red-300 font-mono text-[10px]">Reset</button>
                            @endif
                        </div>
                        <input type="file" 
                               id="contact_bg_file" 
                               name="contact_bg_file" 
                               accept=".gif,.jpg,.jpeg,.png,.webp,.mp4" 
                               class="w-full bg-nero border border-gunmetal px-2.5 py-1.5 text-xs text-silver-bright focus:border-giallo file:mr-2 file:py-1 file:px-2 file:border-0 file:text-[10px] file:font-heading file:uppercase file:bg-steel file:text-silver-bright hover:file:bg-giallo hover:file:text-nero cursor-pointer" 
                               onchange="previewImage(event, 'preview-contact-bg-img')" />
                    </div>
                </div>

                {{-- 8. Hero Halaman Utama (Landing Page) --}}
                <div class="panel border border-gunmetal bg-carbon p-5 space-y-4 flex flex-col justify-between md:col-span-2 lg:col-span-2">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright font-bold flex items-center gap-1.5">
                                🏁 Hero Halaman Utama (Home Landing Page)
                            </h3>
                            @if($settings['custom_hero_bg'])
                                <span class="text-[9px] font-mono text-verde bg-verde/10 border border-verde/30 px-1.5 py-0.5 rounded">Custom Terpisah</span>
                            @else
                                <span class="text-[9px] font-mono text-silver-dim bg-steel/30 px-1.5 py-0.5 rounded">Mengikuti Login BG</span>
                            @endif
                        </div>
                        <p class="text-[11px] text-silver-dim leading-relaxed">
                            Hero banner utama ketika pengunjung pertama kali membuka website AUDrive. Jika dibiarkan kosong, otomatis mengikuti file Login BG.
                        </p>
                        <div class="relative h-32 bg-nero border border-gunmetal overflow-hidden rounded group">
                            <img id="preview-hero-bg-img" src="{{ $settings['hero_bg'] }}" alt="Hero BG" class="w-full h-full object-cover">
                            <div class="absolute bottom-1 right-1 bg-nero/80 px-2 py-0.5 text-[9px] font-mono text-silver-bright rounded">
                                {{ $settings['custom_hero_bg'] ? 'Custom Terpisah' : 'Auto (Login BG)' }}
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2 pt-2 border-t border-gunmetal/60">
                        <div class="flex items-center justify-between text-[11px]">
                            <label for="hero_bg_file" class="font-heading uppercase tracking-wider text-silver-bright">Ganti Khusus Hero Home (Opsional)</label>
                            @if($settings['custom_hero_bg'])
                                <button type="button" onclick="submitReset('hero_bg')" class="text-red-400 hover:text-red-300 font-mono text-[10px]">Reset</button>
                            @endif
                        </div>
                        <input type="file" 
                               id="hero_bg_file" 
                               name="hero_bg_file" 
                               accept=".gif,.jpg,.jpeg,.png,.webp,.mp4" 
                               class="w-full bg-nero border border-gunmetal px-2.5 py-1.5 text-xs text-silver-bright focus:border-giallo file:mr-2 file:py-1 file:px-2 file:border-0 file:text-[10px] file:font-heading file:uppercase file:bg-steel file:text-silver-bright hover:file:bg-giallo hover:file:text-nero cursor-pointer" 
                               onchange="previewImage(event, 'preview-hero-bg-img')" />
                    </div>
                </div>

            </div>
        </div>

        {{-- Action Bar Sticky / Bottom --}}
        <div class="panel border border-giallo/40 bg-carbon p-6 shadow-xl shadow-giallo/5 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright font-bold">
                    Konfirmasi Simpan Perubahan
                </h3>
                <p class="text-[11px] text-silver-dim mt-0.5">
                    File yang diunggah akan otomatis menggantikan aset halaman yang bersangkutan secara instan.
                </p>
            </div>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <button type="button" onclick="submitReset('all')" class="w-full sm:w-auto px-4 py-3 bg-steel/60 hover:bg-steel border border-gunmetal text-silver-dim hover:text-silver-bright font-heading text-xs uppercase tracking-wider transition-premium">
                    Reset Semua ke Default
                </button>
                <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-giallo hover:bg-arancio text-nero font-heading font-bold text-xs uppercase tracking-widest transition-premium clip-angular-button shadow-lg shadow-giallo/20 shrink-0">
                    💾 Simpan Semua Perubahan
                </button>
            </div>
        </div>
    </form>

    {{-- Form Tersembunyi untuk Reset Item --}}
    <form id="reset-form" action="{{ route('admin.settings.reset') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="item" id="reset-item-input" value="">
    </form>
</div>

@push('scripts')
<script>
    function previewImage(event, previewElementId) {
        const file = event.target.files[0];
        if (file) {
            const url = URL.createObjectURL(file);
            const targetEl = document.getElementById(previewElementId);
            if (targetEl) {
                targetEl.src = url;
            }
        }
    }

    function submitReset(item) {
        const labels = {
            'logo': 'Logo aplikasi',
            'login_bg': 'Background halaman Login',
            'dashboard_bg': 'Background Dashboard',
            'workshop_bg': 'Background halaman Bengkel',
            'marketplace_bg': 'Background halaman Marketplace',
            'sparepart_bg': 'Background halaman Sparepart',
            'about_bg': 'Background halaman Tentang Kami',
            'contact_bg': 'Background halaman Kontak Kami',
            'hero_bg': 'Hero background Landing Page',
            'all': 'SELURUH media & branding ke bawaan awal sistem'
        };

        const targetLabel = labels[item] || 'item ini';

        if (confirm(`Apakah Anda yakin ingin mengembalikan ${targetLabel} ke default bawaan sistem?`)) {
            document.getElementById('reset-item-input').value = item;
            document.getElementById('reset-form').submit();
        }
    }
</script>
@endpush
@endsection
