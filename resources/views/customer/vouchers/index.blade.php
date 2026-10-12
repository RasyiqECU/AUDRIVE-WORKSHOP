@extends('layouts.customer')

@section('title', 'Voucher & Promo')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Diskon & Promo</p>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                VOUCHER SAYA & KLAIM DISKON
            </h1>
            <p class="text-xs text-silver-dim mt-1">
                Gunakan kode voucher saat checkout servis berkala atau pemesanan spare part.
            </p>
        </div>
        <a href="{{ route('customer.promotions.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright font-heading text-xs uppercase tracking-wider rounded transition-premium shrink-0">
            🔥 Promo Spesial Berlangsung
        </a>
    </div>

    {{-- Claimed Vouchers --}}
    <div class="space-y-3">
        <h2 class="font-heading text-xs uppercase tracking-wider text-silver-bright">Voucher Tersimpan</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($claimedVouchers as $redemption)
                <div class="panel border border-giallo/40 bg-carbon p-5 space-y-3 relative overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between">
                            <span class="px-2 py-0.5 text-[10px] font-mono bg-giallo/10 border border-giallo/30 text-giallo rounded">
                                {{ $redemption->voucher?->code ?? 'VOUCHER' }}
                            </span>
                            <span class="text-[10px] text-emerald-400 font-heading uppercase">{{ $redemption->status }}</span>
                        </div>
                        <h3 class="font-display text-lg text-silver-bright font-bold mt-2">
                            {{ $redemption->voucher?->name ?? 'Diskon Spesial AUDrive' }}
                        </h3>
                        <p class="text-xs text-silver-dim mt-1">{{ $redemption->voucher?->description }}</p>
                    </div>
                    <div class="border-t border-gunmetal/40 pt-3 flex items-center justify-between text-[11px] text-silver-dim">
                        <span>Kedaluwarsa: {{ $redemption->voucher?->end_date?->format('d M Y') ?? 'Tanpa Batas' }}</span>
                        <span class="text-giallo font-bold font-mono">Diskon {{ $redemption->voucher?->discount_percentage ? $redemption->voucher->discount_percentage . '%' : 'Rp ' . number_format($redemption->voucher?->discount_amount ?? 0) }}</span>
                    </div>
                </div>
            @empty
                <div class="col-span-full panel border border-gunmetal/70 bg-carbon p-6 text-center text-silver-dim">
                    <p class="text-xs">Belum ada voucher yang Anda simpan. Klaim dari daftar di bawah!</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Available Vouchers to Claim --}}
    <div class="space-y-3 pt-4">
        <h2 class="font-heading text-xs uppercase tracking-wider text-silver-bright">Voucher Tersedia untuk Diklaim</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($availableVouchers as $voucher)
                <div class="panel border border-gunmetal/70 bg-carbon p-5 space-y-3 flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between">
                            <span class="px-2 py-0.5 text-[10px] font-mono bg-steel border border-gunmetal text-silver-bright rounded">
                                {{ $voucher->code }}
                            </span>
                            <span class="text-xs text-giallo font-bold font-mono">
                                {{ $voucher->discount_percentage ? $voucher->discount_percentage . '%' : 'Rp ' . number_format($voucher->discount_amount ?? 0) }} OFF
                            </span>
                        </div>
                        <h3 class="font-display text-base text-silver-bright font-bold mt-2">
                            {{ $voucher->name }}
                        </h3>
                        <p class="text-xs text-silver-dim mt-1">{{ $voucher->description }}</p>
                    </div>

                    <div class="border-t border-gunmetal/40 pt-3 flex items-center justify-between">
                        <span class="text-[10px] text-silver-dim">S/D: {{ $voucher->end_date?->format('d M Y') ?? 'Selamanya' }}</span>
                        <form action="{{ route('customer.vouchers.claim', $voucher->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 bg-giallo hover:bg-giallo-bright text-carbon text-xs font-heading uppercase font-bold rounded clip-angular-button">
                                Klaim Voucher
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full panel border border-gunmetal/70 bg-carbon p-6 text-center text-silver-dim">
                    <p class="text-xs">Tidak ada voucher umum yang dapat diklaim saat ini.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
