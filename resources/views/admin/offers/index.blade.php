@extends('layouts.admin')

@section('title', 'Penawaran Mobil Masuk')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Showroom Leads</p>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                PENAWARAN MOBIL (OFFERS)
            </h1>
            <p class="text-xs text-silver-dim mt-1">
                Kelola negosiasi harga dan penawaran dari calon pembeli unit mobil showroom.
            </p>
        </div>
    </div>

    {{-- Offers Table --}}
    <div class="panel border border-gunmetal/70 bg-carbon overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-silver-muted">
                <thead class="bg-steel/50 text-silver-bright font-heading uppercase text-[10px] tracking-wider border-b border-gunmetal">
                    <tr>
                        <th class="p-3">ID Offer</th>
                        <th class="p-3">Unit Mobil</th>
                        <th class="p-3">Customer</th>
                        <th class="p-3 text-right">Harga Penawaran</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Tanggal</th>
                        <th class="p-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/40 font-mono">
                    @forelse($offers as $offer)
                        <tr class="hover:bg-steel/20 transition-colors">
                            <td class="p-3 text-giallo font-semibold">#OFR-{{ str_pad($offer->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td class="p-3 font-sans text-silver-bright font-medium">{{ $offer->carUnit?->title ?? $offer->carUnit?->brand . ' ' . $offer->carUnit?->model }}</td>
                            <td class="p-3 font-sans text-silver-dim">{{ $offer->customer?->user?->name ?? 'Guest User' }}</td>
                            <td class="p-3 text-right text-silver-bright font-bold">Rp {{ number_format($offer->offered_price_idr ?? 0, 0, ',', '.') }}</td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 text-[10px] uppercase rounded font-bold
                                    @if($offer->status === 'accepted') bg-emerald-950/60 text-emerald-400 border border-emerald-800
                                    @elseif($offer->status === 'rejected') bg-red-950/60 text-red-400 border border-red-800
                                    @else bg-amber-950/60 text-amber-400 border border-amber-800 @endif">
                                    {{ $offer->status }}
                                </span>
                            </td>
                            <td class="p-3 text-silver-dim text-[11px]">{{ $offer->created_at->format('d M Y') }}</td>
                            <td class="p-3 text-right font-sans">
                                <a href="{{ route('admin.offers.show', $offer->id) }}" class="text-xs text-giallo hover:underline font-heading uppercase">
                                    Review →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-silver-dim font-sans">
                                Belum ada penawaran harga masuk.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($offers->hasPages())
        <div>{{ $offers->links() }}</div>
    @endif
</div>
@endsection
