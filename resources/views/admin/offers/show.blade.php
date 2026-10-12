@extends('layouts.admin')

@section('title', 'Detail Penawaran #' . $offer->id)

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    {{-- Header --}}
    <div>
        <a href="{{ route('admin.offers.index') }}" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1 mb-2">
            ← Kembali ke Daftar Offers
        </a>
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <span class="text-xs text-giallo font-mono">#OFR-{{ str_pad($offer->id, 5, '0', STR_PAD_LEFT) }}</span>
                <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                    REVIEW PENAWARAN HARGA MOBIL
                </h1>
            </div>
            <div>
                <span class="px-3 py-1 text-xs uppercase rounded font-bold
                    @if($offer->status === 'accepted') bg-emerald-950/60 text-emerald-400 border border-emerald-800
                    @elseif($offer->status === 'rejected') bg-red-950/60 text-red-400 border border-red-800
                    @else bg-amber-950/60 text-amber-400 border border-amber-800 @endif">
                    {{ $offer->status }}
                </span>
            </div>
        </div>
    </div>

    {{-- Details Card --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="panel border border-gunmetal/70 bg-carbon p-6 space-y-4">
            <h3 class="font-heading text-xs uppercase tracking-wider text-giallo border-b border-gunmetal/40 pb-2">
                Informasi Calon Pembeli
            </h3>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-silver-dim">Nama:</span>
                    <span class="text-silver-bright font-bold">{{ $offer->customer?->user?->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-silver-dim">Email:</span>
                    <span class="text-silver-bright font-mono">{{ $offer->customer?->user?->email ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-silver-dim">No. Telepon:</span>
                    <span class="text-silver-bright font-mono">{{ $offer->customer?->phone ?? $offer->customer?->user?->phone ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-silver-dim">Tanggal Penawaran:</span>
                    <span class="text-silver-bright font-mono">{{ $offer->created_at->format('d M Y, H:i') }}</span>
                </div>
            </div>
        </div>

        <div class="panel border border-gunmetal/70 bg-carbon p-6 space-y-4">
            <h3 class="font-heading text-xs uppercase tracking-wider text-giallo border-b border-gunmetal/40 pb-2">
                Unit Mobil & Harga
            </h3>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-silver-dim">Unit:</span>
                    <span class="text-silver-bright font-bold">{{ $offer->carUnit?->title ?? $offer->carUnit?->brand . ' ' . $offer->carUnit?->model }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-silver-dim">Harga Listing:</span>
                    <span class="text-silver-dim line-through font-mono">Rp {{ number_format($offer->carUnit?->price_idr ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-gunmetal/30">
                    <span class="text-silver-dim">Harga Penawaran:</span>
                    <span class="text-giallo font-mono text-lg font-bold">Rp {{ number_format($offer->offered_price_idr ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
