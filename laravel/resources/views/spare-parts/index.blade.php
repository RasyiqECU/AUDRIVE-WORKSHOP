@extends('layouts.public')

@section('title', 'Katalog Suku Cadang & Aksesori Premium — AUDrive')
@section('meta_description', 'Beli suku cadang orisinal (OEM) & aftermarket performa tinggi bergaransi untuk mobil premium dan supercar di AUDrive. Kualitas terjamin dengan layanan instalasi profesional.')

@section('content')
{{-- Hero Section (Lamborghini Style Fullscreen GIF 100vh) --}}
<section class="relative w-full h-screen min-h-[650px] overflow-hidden flex items-center justify-center">
    {{-- GIF Background (Fullscreen 100vh, continuous loop, smooth, object-fit: cover) --}}
    <img src="{{ \App\Services\SettingService::getSparepartBackground() }}"
         alt="AUDrive Genuine Spare Parts & Performance Components"
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
                    100% Genuine OEM & High Performance Parts
                </span>
            </div>

            <h1 class="font-display text-4xl sm:text-6xl lg:text-7xl font-black text-silver-bright tracking-wider leading-[1.05] uppercase drop-shadow-2xl">
                GENUINE OEM & <span class="text-giallo">PERFORMANCE</span><br />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-silver-bright via-silver to-silver-dim">
                    SPARE PARTS.
                </span>
            </h1>

            <p class="font-body text-sm sm:text-base md:text-lg text-silver-bright/90 mt-6 max-w-xl leading-relaxed drop-shadow">
                Komponen mesin presisi, pengereman keramik, elektrikal, suspensi sport, dan pelumas performa tinggi untuk supercar & mobil premium dengan garansi resmi.
            </p>

            <div class="flex flex-wrap items-center gap-4 mt-8">
                <a href="#spareparts-catalog" class="inline-flex items-center justify-center px-6 py-3 bg-giallo hover:bg-giallo/90 text-nero font-heading font-bold text-xs uppercase tracking-wider rounded transition-all shadow-xl shadow-giallo/20">
                    ⚙️ Cari Suku Cadang
                </a>
                @auth
                    <a href="{{ route('parts.cart') }}" class="inline-flex items-center justify-center px-6 py-3 backdrop-blur-md bg-carbon/80 border border-white/20 hover:border-giallo/50 text-silver-bright font-heading font-semibold text-xs uppercase tracking-wider rounded transition-all">
                        🛒 Keranjang Belanja
                    </a>
                @else
                    <a href="{{ route('contact') }}" class="inline-flex items-center justify-center px-6 py-3 backdrop-blur-md bg-carbon/80 border border-white/20 hover:border-white/40 text-silver-bright font-heading font-semibold text-xs uppercase tracking-wider rounded transition-all">
                        💬 Konsultasi Part Indent
                    </a>
                @endauth
            </div>
        </div>

        <div class="pb-6 flex items-center justify-between text-silver-dim text-xs font-mono">
            <div class="flex items-center gap-3">
                <span class="text-giallo font-bold">SPARE PART STORE</span>
                <span class="h-3 w-px bg-gunmetal"></span>
                <span class="uppercase tracking-widest text-[10px]">100% Certified Direct Manufacturer</span>
            </div>

            <a href="#spareparts-catalog" class="flex items-center gap-2 hover:text-giallo transition group">
                <span class="uppercase tracking-wider text-[10px] hidden sm:inline">Katalog Part</span>
                <div class="w-5 h-8 border border-silver-dim/60 group-hover:border-giallo rounded-full flex items-start justify-center p-1 transition">
                    <div class="w-1 h-2 bg-giallo rounded-full animate-bounce"></div>
                </div>
            </a>
        </div>
    </div>
</section>

{{-- Main Spare Parts Catalog --}}
<section id="spareparts-catalog" class="container mx-auto px-4 py-8">
    {{-- Search & Filter Bar --}}
    <div class="bg-carbon border border-gunmetal p-5 mb-8 rounded shadow-lg">
        <form method="GET" action="{{ route('parts.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            {{-- Search Keyword --}}
            <div class="lg:col-span-2">
                <label for="search" class="block font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1.5 font-semibold">
                    Cari Nama Part / Kode SKU / Merek
                </label>
                <div class="relative">
                    <input type="text" name="search" id="search" value="{{ $filters['search'] ?? request('search') }}"
                           placeholder="Contoh: Brake Pad Ceramic, Oil Filter, Spark Plug..."
                           class="w-full bg-steel/30 border border-gunmetal/60 rounded px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none placeholder-silver-dim/50">
                    @if(!empty($filters['search']))
                        <a href="{{ route('parts.index') }}" class="absolute right-2.5 top-2 text-silver-dim hover:text-silver-bright text-xs">✕</a>
                    @endif
                </div>
            </div>

            {{-- Filter Kategori --}}
            <div>
                <label for="category_id" class="block font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1.5 font-semibold">
                    Kategori
                </label>
                <select name="category_id" id="category_id" class="w-full bg-steel/30 border border-gunmetal/60 rounded px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (string)($filters['category_id'] ?? request('category_id')) === (string)$cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Kendaraan / Brand Compatibility --}}
            <div>
                <label for="vehicle_brand" class="block font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1.5 font-semibold">
                    Kompatibilitas Mobil
                </label>
                <input type="text" name="vehicle_brand" id="vehicle_brand" value="{{ $filters['vehicle_brand'] ?? request('vehicle_brand') }}"
                       placeholder="Contoh: Porsche, BMW, Audi..."
                       class="w-full bg-steel/30 border border-gunmetal/60 rounded px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none placeholder-silver-dim/50">
            </div>

            {{-- Urutkan / Sort --}}
            <div>
                <label for="sort" class="block font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1.5 font-semibold">
                    Urutkan
                </label>
                <select name="sort" id="sort" class="w-full bg-steel/30 border border-gunmetal/60 rounded px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                    <option value="name_asc" {{ ($filters['sort'] ?? '') === 'name_asc' ? 'selected' : '' }}>Nama (A - Z)</option>
                    <option value="name_desc" {{ ($filters['sort'] ?? '') === 'name_desc' ? 'selected' : '' }}>Nama (Z - A)</option>
                    <option value="price_asc" {{ ($filters['sort'] ?? '') === 'price_asc' ? 'selected' : '' }}>Harga Terendah</option>
                    <option value="price_desc" {{ ($filters['sort'] ?? '') === 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                </select>
            </div>

            {{-- Action Buttons --}}
            <div class="sm:col-span-2 lg:col-span-5 flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-gunmetal/40">
                <div class="flex items-center gap-2">
                    <button type="submit" class="px-5 py-2 bg-giallo hover:bg-giallo/90 text-nero font-heading font-semibold text-xs uppercase tracking-wider rounded transition-all shadow-md shadow-giallo/10">
                        Cari Spare Part
                    </button>
                    @if(!empty($filters['search']) || !empty($filters['category_id']) || !empty($filters['vehicle_brand']) || request()->has('sort'))
                        <a href="{{ route('parts.index') }}" class="px-4 py-2 border border-gunmetal hover:border-silver-dim text-silver-dim hover:text-silver-bright font-heading text-xs uppercase tracking-wider rounded transition">
                            Reset Filter
                        </a>
                    @endif
                </div>

                @auth
                    <a href="{{ route('parts.cart') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-steel/40 hover:bg-steel/60 border border-gunmetal hover:border-giallo/50 rounded font-heading text-xs text-silver-bright transition">
                        <span>🛒 Keranjang Belanja</span>
                    </a>
                @endauth
            </div>
        </form>
    </div>

    {{-- Category Pills Quick Bar --}}
    @if($categories->count() > 0)
        <div class="flex items-center gap-2 overflow-x-auto pb-4 mb-6 scrollbar-thin">
            <a href="{{ route('parts.index') }}"
               class="px-3.5 py-1.5 rounded-full text-xs font-heading uppercase tracking-wider whitespace-nowrap border transition-premium {{ empty($filters['category_id']) ? 'bg-giallo text-nero border-giallo font-bold' : 'bg-steel/30 text-silver-dim border-gunmetal hover:border-giallo/40 hover:text-silver-bright' }}">
                Semua Part
            </a>
            @foreach($categories as $category)
                <a href="{{ route('parts.category', $category->slug) }}"
                   class="px-3.5 py-1.5 rounded-full text-xs font-heading uppercase tracking-wider whitespace-nowrap border transition-premium {{ (string)($filters['category_id'] ?? '') === (string)$category->id ? 'bg-giallo text-nero border-giallo font-bold' : 'bg-steel/30 text-silver-dim border-gunmetal hover:border-giallo/40 hover:text-silver-bright' }}">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    @endif

    {{-- Result Counter --}}
    <div class="flex items-center justify-between mb-6">
        <p class="font-mono text-xs text-silver-dim">
            Menampilkan <span class="text-silver-bright font-bold">{{ $parts->total() }}</span> suku cadang
        </p>
    </div>

    {{-- Parts Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($parts as $part)
            <x-parts.part-card :part="$part" />
        @empty
            <div class="col-span-full py-12">
                <x-ui.empty-state
                    icon="⚙️"
                    title="Tidak Ada Suku Cadang Ditemukan"
                    description="Coba ubah kata kunci pencarian atau reset filter untuk melihat katalog suku cadang lainnya."
                >
                    <div class="mt-4">
                        <a href="{{ route('parts.index') }}" class="px-4 py-2 bg-giallo text-nero font-heading text-xs uppercase font-semibold rounded">
                            Tampilkan Semua Part
                        </a>
                    </div>
                </x-ui.empty-state>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($parts->hasPages())
        <div class="mt-10">
            {{ $parts->withQueryString()->links() }}
        </div>
    @endif
</section>

{{-- Feature Highlights --}}
<section class="bg-carbon border-t border-gunmetal/40 py-12 mt-12">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="flex items-start gap-4 p-4 border border-gunmetal/30 bg-steel/10 rounded">
                <span class="text-3xl text-giallo">🛡️</span>
                <div>
                    <h3 class="font-heading text-xs font-semibold text-silver-bright uppercase tracking-wider mb-1">100% Suku Cadang Asli</h3>
                    <p class="font-body text-xs text-silver-dim">Garansi keaslian komponen langsung dari produsen OEM terpercaya dunia.</p>
                </div>
            </div>
            <div class="flex items-start gap-4 p-4 border border-gunmetal/30 bg-steel/10 rounded">
                <span class="text-3xl text-giallo">🔧</span>
                <div>
                    <h3 class="font-heading text-xs font-semibold text-silver-bright uppercase tracking-wider mb-1">Layanan Pasang di Bengkel</h3>
                    <p class="font-body text-xs text-silver-dim">Dapat langsung dipasang oleh teknisi master bersertifikat AUDrive.</p>
                </div>
            </div>
            <div class="flex items-start gap-4 p-4 border border-gunmetal/30 bg-steel/10 rounded">
                <span class="text-3xl text-giallo">🚚</span>
                <div>
                    <h3 class="font-heading text-xs font-semibold text-silver-bright uppercase tracking-wider mb-1">Pengiriman Cepat & Aman</h3>
                    <p class="font-body text-xs text-silver-dim">Kemasan proteksi ganda dengan asuransi pengiriman ke seluruh Indonesia.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
