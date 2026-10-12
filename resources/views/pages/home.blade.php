@extends('layouts.public')

@section('title', 'AUDrive — Dealer Mobil Premium & Supercar')
@section('meta_description', 'AUDrive adalah dealer mobil premium dan supercar dengan layanan bengkel berstandar tinggi dan teknologi AI untuk diagnosis suara mesin.')

@section('content')
{{-- Hero Section (Lamborghini Style Fullscreen GIF 100vh) --}}
<section class="relative w-full h-screen min-h-[650px] overflow-hidden flex items-center justify-center">
    {{-- GIF Background (Fullscreen 100vh, continuous loop, smooth, object-fit: cover) --}}
    <img src="{{ asset('images/lambo.gif') }}"
         alt="Lamborghini Supercar Experience"
         class="absolute inset-0 w-full h-full object-cover object-center pointer-events-none select-none z-0" />

    {{-- Cinematic Dark Gradients & Overlays --}}
    <!-- Top Gradient for Navbar readability -->
    <div class="absolute inset-x-0 top-0 h-44 bg-gradient-to-b from-nero/90 via-nero/50 to-transparent z-10 pointer-events-none"></div>

    <!-- Central Vignette & Directional Lighting -->
    <div class="absolute inset-0 bg-gradient-to-r from-nero/90 via-nero/30 to-nero/75 z-10 pointer-events-none"></div>

    <!-- Overall Subtle Contrast Enhancer -->
    <div class="absolute inset-0 bg-nero/30 backdrop-brightness-95 z-10 pointer-events-none"></div>

    <!-- Bottom Gradient into Dashboard Sections -->
    <div class="absolute inset-x-0 bottom-0 h-48 bg-gradient-to-t from-nero via-nero/80 to-transparent z-10 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 right-0 h-1 bg-hazard-stripes opacity-40 z-20"></div>

    {{-- Hero Content (Lamborghini Luxury Aesthetics) --}}
    <div class="relative z-20 container mx-auto px-4 md:px-8 py-20 flex flex-col justify-between h-full max-h-screen">
        {{-- Spacer to balance vertical alignment with navbar --}}
        <div class="h-16"></div>

        {{-- Main Text / CTA --}}
        <div class="max-w-3xl my-auto">
            {{-- Badge / Subheading --}}
            <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 bg-nero/70 border border-giallo/40 backdrop-blur-md rounded mb-6">
                <span class="inline-block w-2 h-2 bg-giallo rounded-full animate-pulse shadow-sm shadow-giallo"></span>
                <span class="font-heading text-[11px] uppercase tracking-[0.35em] text-giallo font-bold">
                    Automotive Perfection & High Performance
                </span>
            </div>

            {{-- Title --}}
            <h1 class="font-display text-4xl sm:text-6xl lg:text-7xl font-black text-silver-bright tracking-wider leading-[1.05] uppercase drop-shadow-2xl">
                BEYOND <span class="text-giallo">DRIVING.</span><br />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-silver-bright via-silver to-silver-dim">
                    PURE ADRENALINE.
                </span>
            </h1>

            {{-- Subtitle --}}
            <p class="font-body text-sm sm:text-base md:text-lg text-silver-bright/90 mt-6 max-w-xl leading-relaxed drop-shadow">
                Dealer mobil premium dengan layanan bengkel berstandar supercar dan teknologi AI diagnosis suara mesin pertama di Indonesia.
            </p>

            {{-- CTA Buttons --}}
            <div class="flex flex-wrap items-center gap-4 mt-8">
                <x-ui.button href="{{ route('marketplace.index') }}" variant="primary" size="lg" class="shadow-xl shadow-giallo/20 font-bold tracking-wider">
                    🏎️ Jelajahi Katalog
                </x-ui.button>
                <x-ui.button href="{{ route('workshop.index') }}" variant="secondary" size="lg" class="backdrop-blur-md bg-carbon/80 border-white/20 hover:border-giallo/50 tracking-wider">
                    🔧 Booking Servis
                </x-ui.button>
                <x-ui.button href="{{ route('parts.index') }}" variant="ghost" size="lg" class="backdrop-blur-md bg-nero/40 border border-white/10 hover:border-white/30 text-silver-bright tracking-wider">
                    ⚙️ Suku Cadang
                </x-ui.button>
            </div>
        </div>

        {{-- Bottom Indicator (Scroll cue) --}}
        <div class="pb-6 flex items-center justify-between text-silver-dim text-xs font-mono">
            <div class="flex items-center gap-3">
                <span class="text-giallo font-bold">AUDRIVE</span>
                <span class="h-3 w-px bg-gunmetal"></span>
                <span class="uppercase tracking-widest text-[10px]">Official Luxury Experience</span>
            </div>

            <a href="#featured-section" class="flex items-center gap-2 hover:text-giallo transition group">
                <span class="uppercase tracking-wider text-[10px] hidden sm:inline">Scroll Down</span>
                <div class="w-5 h-8 border border-silver-dim/60 group-hover:border-giallo rounded-full flex items-start justify-center p-1 transition">
                    <div class="w-1 h-2 bg-giallo rounded-full animate-bounce"></div>
                </div>
            </a>
        </div>
    </div>
</section>

{{-- Featured Cars --}}
<section id="featured-section" class="container mx-auto px-4 py-12">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="font-display text-2xl tracking-wider text-silver-bright">UNIT UNGGULAN</h2>
            <p class="font-heading text-xs text-silver-dim uppercase tracking-wider mt-1">Koleksi terbaik kami</p>
        </div>
        <a href="{{ route('marketplace.index') }}" class="font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider transition-premium">
            Lihat Semua →
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($featuredCars as $car)
            <x-marketplace.car-card :car="$car" />
        @empty
            <x-ui.empty-state icon="🏎" title="Belum ada unit" description="Unit unggulan akan segera tersedia." />
        @endforelse
    </div>
</section>

{{-- CTA Section --}}
<section class="container mx-auto px-4 pb-12">
    <div class="grid md:grid-cols-3 gap-6">
        <x-ui.card hover class="text-center p-6">
            <span class="text-4xl mb-4 block">🏎</span>
            <h3 class="font-heading text-sm font-semibold text-silver-bright uppercase tracking-wider">Test Drive</h3>
            <p class="font-body text-xs text-silver-dim mt-2">Rasakan langsung performa mobil impian Anda</p>
            <x-ui.button href="#" variant="ghost" size="sm" class="mt-4">Selengkapnya</x-ui.button>
        </x-ui.card>

        <x-ui.card hover class="text-center p-6">
            <span class="text-4xl mb-4 block">🔧</span>
            <h3 class="font-heading text-sm font-semibold text-silver-bright uppercase tracking-wider">Booking Servis</h3>
            <p class="font-body text-xs text-silver-dim mt-2">Jadwalkan servis berkala kendaraan Anda</p>
            <x-ui.button href="#" variant="ghost" size="sm" class="mt-4">Selengkapnya</x-ui.button>
        </x-ui.card>

        <x-ui.card hover class="text-center p-6">
            <span class="text-4xl mb-4 block">🔊</span>
            <h3 class="font-heading text-sm font-semibold text-silver-bright uppercase tracking-wider">AI Sound Scan</h3>
            <p class="font-body text-xs text-silver-dim mt-2">Diagnosis suara mesin dengan teknologi AI</p>
            <x-ui.button href="#" variant="ghost" size="sm" class="mt-4">Selengkapnya</x-ui.button>
        </x-ui.card>
    </div>
</section>

{{-- Categories --}}
<section class="container mx-auto px-4 pb-12">
    <h2 class="font-display text-2xl tracking-wider text-silver-bright mb-6">KATEGORI SPARE PART</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        @foreach($categories as $category)
            <a href="{{ route('parts.category', $category->slug) }}" class="bg-steel/20 border border-gunmetal/30 p-4 text-center hover:border-giallo/30 transition-premium">
                <span class="text-2xl block mb-2">⚙️</span>
                <p class="font-heading text-xs text-silver-bright uppercase tracking-wider">{{ $category->name }}</p>
            </a>
        @endforeach
    </div>
</section>

{{-- Promo --}}
@if($promotions->count() > 0)
<section class="container mx-auto px-4 pb-12">
    <div class="bg-gradient-to-r from-giallo/10 to-arancio/5 border border-giallo/20 p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-giallo/5 rounded-full blur-2xl"></div>
        <div class="relative z-10">
            <span class="font-heading text-xs uppercase tracking-[0.3em] text-giallo">Promo Spesial</span>
            <h3 class="font-display text-2xl text-silver-bright mt-2">{{ $promotions->first()->name }}</h3>
            <p class="font-body text-xs text-silver-dim mt-2">{{ $promotions->first()->description }}</p>
            <x-ui.button href="#" variant="primary" class="mt-4">Klaim Promo</x-ui.button>
        </div>
    </div>
</section>
@endif

{{-- Keunggulan --}}
<section class="container mx-auto px-4 pb-12">
    <div class="text-center mb-8">
        <h2 class="font-display text-2xl tracking-wider text-silver-bright">MENGAPA AUDRIVE?</h2>
        <p class="font-heading text-xs text-silver-dim uppercase tracking-wider mt-2">Keunggulan layanan kami</p>
    </div>
    <div class="grid md:grid-cols-4 gap-6">
        <div class="text-center">
            <div class="w-12 h-12 mx-auto bg-giallo/10 border border-giallo/30 clip-hexagon flex items-center justify-center mb-3">
                <span class="text-giallo text-xl">✓</span>
            </div>
            <h4 class="font-heading text-xs text-silver-bright uppercase tracking-wider">Unit Berkualitas</h4>
            <p class="font-body text-[11px] text-silver-dim mt-1">Semua unit melewati inspeksi ketat</p>
        </div>
        <div class="text-center">
            <div class="w-12 h-12 mx-auto bg-arancio/10 border border-arancio/30 clip-hexagon flex items-center justify-center mb-3">
                <span class="text-arancio text-xl">🔧</span>
            </div>
            <h4 class="font-heading text-xs text-silver-bright uppercase tracking-wider">Mekanik Profesional</h4>
            <p class="font-body text-[11px] text-silver-dim mt-1">Teknisi bersertifikat resmi</p>
        </div>
        <div class="text-center">
            <div class="w-12 h-12 mx-auto bg-verde/10 border border-verde/30 clip-hexagon flex items-center justify-center mb-3">
                <span class="text-verde text-xl">🔊</span>
            </div>
            <h4 class="font-heading text-xs text-silver-bright uppercase tracking-wider">Teknologi AI</h4>
            <p class="font-body text-[11px] text-silver-dim mt-1">Diagnosis suara mesin akurat</p>
        </div>
        <div class="text-center">
            <div class="w-12 h-12 mx-auto bg-silver/10 border border-silver/30 clip-hexagon flex items-center justify-center mb-3">
                <span class="text-silver-bright text-xl">⭐</span>
            </div>
            <h4 class="font-heading text-xs text-silver-bright uppercase tracking-wider">Loyalty Program</h4>
            <p class="font-body text-[11px] text-silver-dim mt-1">Dapatkan poin setiap transaksi</p>
        </div>
    </div>
</section>
@endsection
