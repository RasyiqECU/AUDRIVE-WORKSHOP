@extends('layouts.admin')

@section('title', 'Titip Jual Mobil')

@section('content')
<div class="space-y-6">
    <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">TITIP JUAL MOBIL</h1>
    <p class="text-xs text-silver-dim">Kepala Mekanik menginspeksi, Manager menyetujui harga &amp; komisi, lalu Sales memasarkan.</p>

    <div class="panel border border-gunmetal bg-carbon divide-y divide-gunmetal/40">
        @forelse($cars as $car)
            <a href="{{ route('admin.consignments.show', $car) }}" class="flex flex-wrap justify-between gap-2 p-4 text-xs hover:bg-steel/20">
                <span class="text-silver-bright font-bold">{{ $car->full_name }}</span>
                <span class="text-silver-dim">{{ $car->seller?->user?->name }}</span>
                <span class="font-mono text-giallo">Rp {{ number_format($car->price_sell_idr, 0, ',', '.') }}</span>
                <span class="text-silver">
                    @if($car->status->value === 'in_inspection')
                        {{ $car->latestInspection ? 'Menunggu persetujuan Manager' : 'Menunggu inspeksi' }}
                    @else
                        {{ $car->status->label() }}
                    @endif
                </span>
            </a>
        @empty
            <p class="p-8 text-center text-xs text-silver-dim">Belum ada pengajuan titip jual.</p>
        @endforelse
    </div>

    {{ $cars->links() }}
</div>
@endsection
