@extends('layouts.customer')

@section('title', 'Loyalty Point')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Program Loyalitas</p>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                AUDRIVE LOYALTY CLUB
            </h1>
            <p class="text-xs text-silver-dim mt-1">
                Kumpulkan poin dari setiap transaksi servis & pembelian suku cadang untuk ditukar dengan voucher eksklusif.
            </p>
        </div>
        <a href="{{ route('customer.loyalty.history') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright font-heading text-xs uppercase tracking-wider rounded transition-premium shrink-0">
            📜 Riwayat Poin
        </a>
    </div>

    {{-- Loyalty Card --}}
    <div class="panel border border-giallo/40 bg-gradient-to-r from-carbon via-steel/30 to-carbon p-6 md:p-8 relative overflow-hidden shadow-2xl">
        <div class="absolute -right-10 -bottom-10 opacity-10 text-9xl">⭐</div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <span class="font-heading text-[10px] uppercase tracking-[0.2em] text-giallo bg-giallo/10 px-2.5 py-1 border border-giallo/30 rounded-sm">
                    Tier: {{ strtoupper($customer->loyalty_tier ?? 'Bronze Member') }}
                </span>
                <p class="font-display text-4xl md:text-5xl font-bold text-giallo tracking-tight mt-3">
                    {{ number_format($customer->loyalty_points, 0, ',', '.') }} <span class="text-lg font-heading text-silver-bright">PTS</span>
                </p>
                <p class="font-mono text-xs text-silver-dim mt-1">Customer ID: {{ $customer->code }}</p>
            </div>
            <div class="text-left md:text-right space-y-1 text-xs text-silver-dim">
                <p>💡 <strong class="text-silver-bright">1 Poin = Rp 1.000</strong> pembelanjaan servis</p>
                <p>Tingkatkan terus transaksimu untuk mencapai tier <span class="text-giallo">Gold</span> & <span class="text-silver-bright">Platinum</span>!</p>
            </div>
        </div>
    </div>

    {{-- Redeem Vouchers --}}
    <div class="space-y-4">
        <h2 class="font-heading text-sm uppercase tracking-wider text-silver-bright">Tukar Poin dengan Voucher</h2>
        @if(isset($availableVouchers) && $availableVouchers->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($availableVouchers as $voucher)
                    <div class="panel border border-gunmetal bg-carbon p-5 flex flex-col justify-between hover:border-giallo/40 transition-premium">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-mono text-xs font-bold text-giallo">{{ $voucher->code }}</span>
                                <span class="text-[10px] font-mono text-verde bg-verde/10 px-2 py-0.5 border border-verde/20">
                                    {{ number_format($voucher->points_cost) }} PTS
                                </span>
                            </div>
                            <h3 class="font-display font-bold text-silver-bright text-base">{{ $voucher->name ?? $voucher->code }}</h3>
                            <p class="text-xs text-silver-dim mt-1">{{ $voucher->description }}</p>
                        </div>
                        <form action="{{ route('customer.loyalty.redeem') }}" method="POST" class="mt-4 pt-3 border-t border-gunmetal">
                            @csrf
                            <input type="hidden" name="voucher_id" value="{{ $voucher->id }}">
                            <button type="submit"
                                    @if($customer->loyalty_points < ($voucher->points_cost ?? 0)) disabled @endif
                                    class="w-full py-2 text-center text-xs font-heading font-bold uppercase tracking-wider rounded transition-premium {{ $customer->loyalty_points >= ($voucher->points_cost ?? 0) ? 'bg-giallo text-nero hover:bg-arancio' : 'bg-steel/30 text-silver-dim cursor-not-allowed' }}">
                                {{ $customer->loyalty_points >= ($voucher->points_cost ?? 0) ? 'Tukar Sekarang' : 'Poin Kurang' }}
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @else
            <div class="panel border border-gunmetal bg-carbon/50 p-8 text-center text-silver-dim text-xs">
                Belum ada voucher penukaran poin yang tersedia saat ini.
            </div>
        @endif
    </div>
</div>
@endsection
