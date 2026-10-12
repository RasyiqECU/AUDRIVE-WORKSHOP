@extends('layouts.public')

@section('title', $car->full_name . ' — AUDrive Marketplace')
@section('meta_description', 'Beli ' . $car->full_name . ' dengan garansi resmi dan inspeksi 150+ titik di AUDrive. Harga terbaik ' . $car->formatted_price)

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs text-silver-dim mb-6 font-heading">
        <a href="{{ route('home') }}" class="hover:text-silver-bright transition">Home</a>
        <span>/</span>
        <a href="{{ route('marketplace.index') }}" class="hover:text-silver-bright transition">Marketplace</a>
        <span>/</span>
        <span class="text-giallo font-semibold">{{ $car->full_name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Left 2 Columns: Photos & Detailed Specs --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Main Photo & Gallery --}}
            <div class="bg-carbon border border-gunmetal rounded-lg overflow-hidden dashboard-card">
                <div class="relative aspect-[16/10] bg-steel/30 overflow-hidden">
                    @if($car->primaryPhoto)
                        <img id="main-car-photo" src="{{ asset('storage/' . $car->primaryPhoto->path) }}" alt="{{ $car->full_name }}" class="w-full h-full object-cover" />
                    @elseif($car->photos->isNotEmpty())
                        <img id="main-car-photo" src="{{ asset('storage/' . $car->photos->first()->path) }}" alt="{{ $car->full_name }}" class="w-full h-full object-cover" />
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gunmetal">
                            <span class="text-7xl">🏎</span>
                        </div>
                    @endif

                    <div class="absolute top-4 left-4">
                        <x-ui.status-pill :status="$car->status->value" />
                    </div>

                    @if($car->featured_at)
                        <div class="absolute top-4 right-4">
                            <span class="bg-giallo text-nero font-heading text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-sm shadow">
                                ★ Pilihan Terbaik
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Photo Thumbnails --}}
                @if($car->photos->count() > 1)
                    <div class="p-3 bg-steel/20 border-t border-gunmetal flex gap-2 overflow-x-auto">
                        @foreach($car->photos as $photo)
                            <button type="button" onclick="document.getElementById('main-car-photo').src='{{ asset('storage/' . $photo->path) }}'"
                                    class="w-16 h-12 flex-shrink-0 border border-gunmetal hover:border-giallo rounded overflow-hidden transition">
                                <img src="{{ asset('storage/' . $photo->path) }}" class="w-full h-full object-cover" alt="Foto unit" />
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Specifications Grid --}}
            <div class="bg-carbon border border-gunmetal rounded-lg p-6 dashboard-card">
                <h2 class="font-heading text-sm font-bold text-silver-bright uppercase tracking-wider mb-4 border-b border-gunmetal/40 pb-2 flex items-center gap-2">
                    <span class="text-giallo">⚡</span> Spesifikasi Teknis
                </h2>
                <x-marketplace.car-spec-grid :car="$car" />
            </div>

            {{-- Description & Details --}}
            <div class="bg-carbon border border-gunmetal rounded-lg p-6 dashboard-card">
                <h2 class="font-heading text-sm font-bold text-silver-bright uppercase tracking-wider mb-4 border-b border-gunmetal/40 pb-2">
                    Deskripsi Kendaraan
                </h2>
                <div class="text-silver-dim text-xs md:text-sm leading-relaxed whitespace-pre-line font-body">
                    {{ $car->description ?? 'Unit mobil bekas berkualitas tinggi dengan rekam jejak servis berkala rutin. Interior dan eksterior dalam kondisi sangat terawat.' }}
                </div>

                {{-- Legalitas & Kelengkapan --}}
                <div class="mt-6 pt-4 border-t border-gunmetal/40 grid grid-cols-2 sm:grid-cols-4 gap-3 text-center text-xs">
                    <div class="bg-steel/20 p-2.5 rounded border border-gunmetal/30">
                        <span class="text-silver-dim block text-[10px] uppercase font-heading">BPKB</span>
                        <span class="text-silver-bright font-semibold mt-0.5 block">{{ $car->bpkb_status ?? 'Lengkap & Asli' }}</span>
                    </div>
                    <div class="bg-steel/20 p-2.5 rounded border border-gunmetal/30">
                        <span class="text-silver-dim block text-[10px] uppercase font-heading">STNK</span>
                        <span class="text-silver-bright font-semibold mt-0.5 block">{{ $car->stnk_status ?? 'Pajak Hidup' }}</span>
                    </div>
                    <div class="bg-steel/20 p-2.5 rounded border border-gunmetal/30">
                        <span class="text-silver-dim block text-[10px] uppercase font-heading">Garansi</span>
                        <span class="text-giallo font-semibold mt-0.5 block">{{ $car->warranty_status ?? 'Garansi 30 Hari' }}</span>
                    </div>
                    <div class="bg-steel/20 p-2.5 rounded border border-gunmetal/30">
                        <span class="text-silver-dim block text-[10px] uppercase font-heading">Stok</span>
                        <span class="text-silver-bright font-semibold mt-0.5 block">{{ $car->stock_status ?? 'Ready Unit' }}</span>
                    </div>
                </div>
            </div>

            {{-- Hasil Inspeksi --}}
            @if($car->latestInspection)
                <div>
                    <x-marketplace.inspection-panel :inspection="$car->latestInspection" />
                </div>
            @endif
        </div>

        {{-- Right 1 Column: Price, Booking Test Drive & Offer Actions --}}
        <div class="space-y-6">
            {{-- Price & Summary Card --}}
            <div class="bg-carbon border border-gunmetal rounded-lg p-6 dashboard-card">
                <span class="text-silver-dim text-[11px] font-heading uppercase tracking-wider block">Harga Penjualan (OTR)</span>
                <div class="mt-1 flex items-baseline gap-2">
                    <span class="font-display text-3xl font-bold text-giallo">
                        {{ $car->formatted_price }}
                    </span>
                </div>
                <div class="mt-3 text-xs text-silver-dim">
                    Kode Unit: <span class="font-mono text-silver-bright font-semibold">{{ $car->code }}</span>
                </div>

                {{-- Quick Contact & Consultation --}}
                <div class="mt-6 pt-4 border-t border-gunmetal/40 space-y-2">
                    <a href="https://wa.me/6281234567890?text=Halo%20AUDrive,%20saya%20tertarik%20dengan%20unit%20{{ urlencode($car->full_name) }}%20(Kode:%20{{ $car->code }})"
                       target="_blank"
                       class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-[#25D366] hover:bg-[#20ba5a] text-white font-heading font-semibold text-xs uppercase tracking-wider rounded transition">
                        💬 Hubungi via WhatsApp
                    </a>
                </div>
            </div>

            {{-- Form Booking Test Drive --}}
            <div class="bg-carbon border border-gunmetal rounded-lg p-6 dashboard-card">
                <h3 class="font-heading text-xs font-bold text-silver-bright uppercase tracking-wider mb-4 border-b border-gunmetal/40 pb-2 flex items-center gap-2">
                    <span class="text-giallo">🚗</span> Jadwalkan Test Drive
                </h3>
                @auth
                    <x-marketplace.test-drive-form :car="$car" />
                @else
                    <div class="text-center py-4 text-xs text-silver-dim space-y-3">
                        <p>Silakan login terlebih dahulu untuk mengajukan jadwal test drive unit ini.</p>
                        <a href="{{ route('login') }}" class="inline-block px-5 py-2 bg-giallo text-nero font-heading font-semibold text-xs uppercase tracking-wider rounded hover:bg-giallo/90 transition">
                            Login Akun
                        </a>
                    </div>
                @endauth
            </div>

            {{-- Form Ajukan Penawaran Harga --}}
            <div class="bg-carbon border border-gunmetal rounded-lg p-6 dashboard-card">
                <h3 class="font-heading text-xs font-bold text-silver-bright uppercase tracking-wider mb-4 border-b border-gunmetal/40 pb-2 flex items-center gap-2">
                    <span class="text-giallo">🏷️</span> Ajukan Penawaran Harga
                </h3>
                @auth
                    <x-marketplace.offer-form :car="$car" />
                @else
                    <div class="text-center py-4 text-xs text-silver-dim space-y-3">
                        <p>Silakan login untuk mengirimkan penawaran harga kepada sales representatif.</p>
                        <a href="{{ route('login') }}" class="inline-block px-5 py-2 border border-white/20 text-silver-bright font-heading font-semibold text-xs uppercase tracking-wider rounded hover:border-giallo transition">
                            Login Akun
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    {{-- Rekomendasi Unit Serupa --}}
    @if(isset($relatedCars) && $relatedCars->isNotEmpty())
        <div class="mt-16 pt-10 border-t border-gunmetal/40">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="font-display text-2xl font-bold text-silver-bright">UNIT REKOMENDASI LAINNYA</h2>
                    <p class="font-heading text-xs text-silver-dim uppercase tracking-wider mt-1">Koleksi sejenis dari merek {{ $car->brand }}</p>
                </div>
                <a href="{{ route('marketplace.index', ['brand' => $car->brand]) }}" class="font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider transition">
                    Lihat Semua {{ $car->brand }} →
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedCars as $relCar)
                    <x-marketplace.car-card :car="$relCar" />
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
