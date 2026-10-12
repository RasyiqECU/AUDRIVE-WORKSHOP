@extends('layouts.app')

@section('title', 'Daftar Penawaran Harga Mobil — AUDrive')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="font-display text-2xl font-bold text-silver-bright">PENAWARAN HARGA SAYA</h1>
        <p class="text-xs text-silver-dim mt-1">Daftar penawaran harga mobil yang Anda ajukan ke showroom AUDrive.</p>
    </div>

    @if($offers->isEmpty())
        <div class="bg-carbon border border-gunmetal rounded-lg p-12 text-center">
            <x-ui.empty-state
                icon="🏷️"
                title="Belum Ada Penawaran"
                description="Anda belum mengajukan penawaran harga untuk unit mobil manapun."
            />
            <div class="mt-4">
                <a href="{{ route('marketplace.index') }}" class="inline-block px-5 py-2.5 bg-giallo text-nero font-heading font-semibold text-xs uppercase tracking-wider rounded">
                    Jelajahi Mobil
                </a>
            </div>
        </div>
    @else
        <div class="space-y-4">
            @foreach($offers as $offer)
                <div class="bg-carbon border border-gunmetal rounded-lg p-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h4 class="font-heading font-bold text-silver-bright text-sm">{{ $offer->carUnit?->full_name }}</h4>
                        <p class="text-xs text-silver-dim mt-0.5">
                            Penawaran Anda: <span class="text-giallo font-semibold">Rp {{ number_format($offer->amount_idr, 0, ',', '.') }}</span>
                        </p>
                        <p class="text-[11px] text-silver-dim font-mono mt-0.5">
                            Status: <span class="uppercase font-semibold text-silver-bright">{{ $offer->status->value ?? $offer->status }}</span>
                            • Diajukan pada: {{ $offer->created_at->format('d M Y') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($offer->carUnit)
                            <a href="{{ route('marketplace.show', $offer->carUnit->slug) }}" class="px-3 py-1.5 border border-gunmetal text-xs text-silver-bright rounded hover:border-giallo transition">
                                Lihat Unit
                            </a>
                        @endif
                        @if(($offer->status->value ?? $offer->status) === 'pending')
                            <form action="{{ route('marketplace.offer.destroy', $offer) }}" method="POST" onsubmit="return confirm('Batalkan penawaran ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 border border-red-500/30 text-xs text-red-400 rounded hover:bg-red-500/10 transition">
                                    Batal
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach

            <div class="mt-4">
                {{ $offers->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
