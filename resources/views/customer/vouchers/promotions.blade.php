@extends('layouts.customer')

@section('title', 'Promo Spesial AUDrive')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <a href="{{ route('customer.vouchers.index') }}" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1 mb-2">
                ← Kembali ke Voucher Saya
            </a>
            <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Event & Penawaran</p>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                PROMO & PENAWARAN SPESIAL
            </h1>
            <p class="text-xs text-silver-dim mt-1">
                Dapatkan penawaran terbatas untuk servis mobil, paket tune-up, dan aksesoris resmi.
            </p>
        </div>
    </div>

    {{-- Promotions Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($promotions as $promo)
            <div class="panel border border-gunmetal/70 bg-carbon overflow-hidden flex flex-col justify-between">
                @if($promo->banner_image ?? false)
                    <img src="{{ asset($promo->banner_image) }}" alt="{{ $promo->title }}" class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-32 bg-gradient-to-r from-carbon via-steel to-carbon flex items-center justify-center text-4xl">
                        🏎️
                    </div>
                @endif
                <div class="p-5 space-y-3 flex-1 flex flex-col justify-between">
                    <div>
                        <span class="inline-block px-2 py-0.5 text-[10px] font-mono bg-giallo/10 border border-giallo/30 text-giallo rounded mb-2">
                            PROMO SPESIAL
                        </span>
                        <h3 class="font-display text-lg text-silver-bright font-bold">
                            {{ $promo->title }}
                        </h3>
                        <p class="text-xs text-silver-dim mt-2 leading-relaxed">
                            {{ $promo->description }}
                        </p>
                    </div>

                    <div class="border-t border-gunmetal/40 pt-3 flex items-center justify-between">
                        <span class="text-[11px] text-silver-dim font-mono">
                            Periode: {{ $promo->start_date?->format('d M') }} - {{ $promo->end_date?->format('d M Y') }}
                        </span>
                        <a href="{{ route('workshop.booking') }}" class="px-3 py-1.5 bg-giallo hover:bg-giallo-bright text-carbon text-xs font-heading uppercase font-bold rounded clip-angular-button">
                            Gunakan Promo
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full panel border border-gunmetal/70 bg-carbon p-8 text-center text-silver-dim">
                <span class="text-4xl block mb-2">🏷️</span>
                <p class="font-heading text-sm text-silver-bright uppercase tracking-wider">Belum Ada Promo Aktif</p>
                <p class="text-xs mt-1">Nantikan promo menarik berikutnya dari AUDrive Workshop & Showroom.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
