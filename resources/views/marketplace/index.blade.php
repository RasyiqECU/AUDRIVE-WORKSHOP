@extends('layouts.public')

@section('title', 'Katalog Mobil Bekas & Baru Premium — AUDrive Marketplace')
@section('meta_description', 'Jelajahi koleksi mobil premium bergaransi di AUDrive. Seluruh unit telah lulus inspeksi 150+ titik dengan sertifikasi kondisi dan rekam jejak transparan.')

@section('content')
{{-- Hero Section (Lamborghini Style Fullscreen GIF 100vh) --}}
<section class="relative w-full h-screen min-h-[650px] overflow-hidden flex items-center justify-center">
    {{-- GIF Background (Fullscreen 100vh, continuous loop, smooth, object-fit: cover) --}}
    <img src="{{ asset('images/marketplace.gif') }}"
         alt="AUDrive Marketplace Supercar Showroom"
         class="absolute inset-0 w-full h-full object-cover object-center pointer-events-none select-none z-0" />

    {{-- Cinematic Dark Gradients & Overlays --}}
    <div class="absolute inset-x-0 top-0 h-44 bg-gradient-to-b from-nero/90 via-nero/50 to-transparent z-10 pointer-events-none"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-nero/90 via-nero/30 to-nero/75 z-10 pointer-events-none"></div>
    <div class="absolute inset-0 bg-nero/30 backdrop-brightness-95 z-10 pointer-events-none"></div>
    <div class="absolute inset-x-0 bottom-0 h-48 bg-gradient-to-t from-nero via-nero/80 to-transparent z-10 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 right-0 h-1 bg-hazard-stripes opacity-40 z-20"></div>

    {{-- Hero Content (Lamborghini Luxury Aesthetics) --}}
    <div class="relative z-20 container mx-auto px-4 md:px-8 py-20 flex flex-col justify-between h-full max-h-screen">
        <div class="h-16"></div>

        <div class="max-w-3xl my-auto">
            <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 bg-nero/70 border border-giallo/40 backdrop-blur-md rounded mb-6">
                <span class="inline-block w-2 h-2 bg-giallo rounded-full animate-pulse shadow-sm shadow-giallo"></span>
                <span class="font-heading text-[11px] uppercase tracking-[0.35em] text-giallo font-bold">
                    Official Showroom & Certified Pre-Owned
                </span>
            </div>

            <h1 class="font-display text-4xl sm:text-6xl lg:text-7xl font-black text-silver-bright tracking-wider leading-[1.05] uppercase drop-shadow-2xl">
                SUPERCAR & <span class="text-giallo">LUXURY</span><br />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-silver-bright via-silver to-silver-dim">
                    COLLECTION.
                </span>
            </h1>

            <p class="font-body text-sm sm:text-base md:text-lg text-silver-bright/90 mt-6 max-w-xl leading-relaxed drop-shadow">
                Setiap unit melalui inspeksi ketat 150+ titik dengan garansi mesin, transmisi, bebas tabrak besar, dan jaminan sertifikasi keaslian dokumen 100%.
            </p>

            <div class="flex flex-wrap items-center gap-4 mt-8">
                <a href="#marketplace-catalog" class="inline-flex items-center justify-center px-6 py-3 bg-giallo hover:bg-giallo/90 text-nero font-heading font-bold text-xs uppercase tracking-wider rounded transition-all shadow-xl shadow-giallo/20">
                    🏎️ Jelajahi Unit Showroom
                </a>
                <a href="{{ route('marketplace.sell.create') }}" class="inline-flex items-center justify-center px-6 py-3 backdrop-blur-md bg-carbon/80 border border-white/20 hover:border-giallo/50 text-silver-bright font-heading font-semibold text-xs uppercase tracking-wider rounded transition-all">
                    💎 Jual / Tukar Tambah
                </a>
            </div>
        </div>

        <div class="pb-6 flex items-center justify-between text-silver-dim text-xs font-mono">
            <div class="flex items-center gap-3">
                <span class="text-giallo font-bold">MARKETPLACE</span>
                <span class="h-3 w-px bg-gunmetal"></span>
                <span class="uppercase tracking-widest text-[10px]">150+ Points Certified Inspection</span>
            </div>

            <a href="#marketplace-catalog" class="flex items-center gap-2 hover:text-giallo transition group">
                <span class="uppercase tracking-wider text-[10px] hidden sm:inline">Lihat Unit</span>
                <div class="w-5 h-8 border border-silver-dim/60 group-hover:border-giallo rounded-full flex items-start justify-center p-1 transition">
                    <div class="w-1 h-2 bg-giallo rounded-full animate-bounce"></div>
                </div>
            </a>
        </div>
    </div>
</section>

{{-- Main Marketplace Content --}}
<section id="marketplace-catalog" class="container mx-auto px-4 py-8">
    {{-- Filter & Search Bar --}}
    <div class="bg-carbon border border-gunmetal p-5 mb-8 rounded shadow-lg">
        <form method="GET" action="{{ route('marketplace.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            {{-- Search Keyword --}}
            <div class="lg:col-span-2">
                <label for="search" class="block font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1.5 font-semibold">
                    Cari Model / Tipe Mobil
                </label>
                <div class="relative">
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                           placeholder="Contoh: Porsche 911, BMW M4, Civic Type R..."
                           class="w-full bg-steel/30 border border-gunmetal/60 rounded px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none placeholder-silver-dim/50">
                    @if(request('search'))
                        <a href="{{ route('marketplace.index') }}" class="absolute right-2.5 top-2 text-silver-dim hover:text-silver-bright text-xs">✕</a>
                    @endif
                </div>
            </div>

            {{-- Filter Merek --}}
            <div>
                <label for="brand" class="block font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1.5 font-semibold">
                    Merek
                </label>
                <select name="brand" id="brand" class="w-full bg-steel/30 border border-gunmetal/60 rounded px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                    <option value="">Semua Merek</option>
                    @foreach($brands as $brandName)
                        <option value="{{ $brandName }}" {{ request('brand') === $brandName ? 'selected' : '' }}>
                            {{ $brandName }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Transmisi --}}
            <div>
                <label for="transmission" class="block font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1.5 font-semibold">
                    Transmisi
                </label>
                <select name="transmission" id="transmission" class="w-full bg-steel/30 border border-gunmetal/60 rounded px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                    <option value="">Semua Transmisi</option>
                    <option value="automatic" {{ request('transmission') === 'automatic' ? 'selected' : '' }}>Automatic (A/T)</option>
                    <option value="manual" {{ request('transmission') === 'manual' ? 'selected' : '' }}>Manual (M/T)</option>
                </select>
            </div>

            {{-- Urutkan / Sort --}}
            <div>
                <label for="sort" class="block font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1.5 font-semibold">
                    Urutkan
                </label>
                <select name="sort" id="sort" class="w-full bg-steel/30 border border-gunmetal/60 rounded px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                    <option value="" {{ !request('sort') ? 'selected' : '' }}>Unit Terbaru</option>
                    <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Harga Terendah</option>
                    <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                    <option value="mileage_asc" {{ request('sort') === 'mileage_asc' ? 'selected' : '' }}>KM Terendah</option>
                    <option value="year_desc" {{ request('sort') === 'year_desc' ? 'selected' : '' }}>Tahun Terbaru</option>
                </select>
            </div>

            {{-- Action Buttons --}}
            <div class="sm:col-span-2 lg:col-span-5 flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-gunmetal/40">
                <div class="flex items-center gap-2">
                    <button type="submit" class="px-5 py-2 bg-giallo hover:bg-giallo/90 text-nero font-heading font-semibold text-xs uppercase tracking-wider rounded transition-all shadow-md shadow-giallo/10">
                        Terapkan Filter
                    </button>
                    @if(request()->hasAny(['search', 'brand', 'transmission', 'sort', 'min_price', 'max_price']))
                        <a href="{{ route('marketplace.index') }}" class="px-4 py-2 border border-gunmetal hover:border-silver-dim text-silver-dim hover:text-silver-bright font-heading text-xs uppercase tracking-wider rounded transition">
                            Reset Filter
                        </a>
                    @endif
                </div>

                <div class="text-xs text-silver-dim font-mono">
                    Menampilkan <span class="text-silver-bright font-bold">{{ $cars->total() }}</span> unit mobil
                </div>
            </div>
        </form>
    </div>

    {{-- Cars Listing Grid --}}
    @if($cars->isEmpty())
        <div class="bg-carbon border border-gunmetal rounded-lg p-12 text-center">
            <x-ui.empty-state
                icon="🏎️"
                title="Tidak Ada Unit Mobil Sesuai Kriteria"
                description="Kriteria pencarian atau filter yang Anda terapkan tidak menemukan unit yang cocok. Coba atur ulang kata kunci atau filter merek."
            />
            <div class="mt-6">
                <a href="{{ route('marketplace.index') }}" class="inline-flex items-center px-5 py-2.5 bg-giallo text-nero font-heading font-semibold text-xs uppercase tracking-wider rounded hover:bg-giallo/90 transition shadow-lg shadow-giallo/20">
                    Lihat Semua Koleksi
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($cars as $car)
                <x-marketplace.car-card :car="$car" />
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-10">
            {{ $cars->links() }}
        </div>
    @endif

    {{-- Jual Mobil CTA Banner --}}
    <div class="mt-16 bg-carbon-fiber border border-gunmetal p-8 rounded-lg relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="absolute right-0 top-0 w-64 h-64 bg-giallo/5 rounded-full blur-2xl"></div>
        <div class="relative z-10 max-w-xl">
            <span class="text-giallo text-xs uppercase tracking-widest font-heading font-semibold">Tukar Tambah / Jual Mobil</span>
            <h3 class="font-display text-2xl font-bold text-silver-bright mt-1">Ingin Menjual Kendaraan Anda?</h3>
            <p class="text-silver-dim text-xs md:text-sm mt-2 leading-relaxed">
                Dapatkan penawaran harga terbaik dengan proses inspeksi transparan, pembayaran kilat, dan layanan pengurusan dokumen resmi oleh tim AUDrive.
            </p>
        </div>
        <div class="relative z-10 flex flex-wrap gap-3">
            <a href="{{ route('marketplace.sell.create') }}" class="px-6 py-3 bg-giallo hover:bg-giallo/90 text-nero font-heading font-semibold text-xs uppercase tracking-wider rounded transition-all shadow-lg shadow-giallo/20">
                Ajukan Penjualan
            </a>
            <a href="{{ route('contact') }}" class="px-6 py-3 border border-white/20 hover:border-white/40 text-silver-bright font-heading font-semibold text-xs uppercase tracking-wider rounded transition">
                Hubungi Konsultan
            </a>
        </div>
    </div>
</section>
@endsection
