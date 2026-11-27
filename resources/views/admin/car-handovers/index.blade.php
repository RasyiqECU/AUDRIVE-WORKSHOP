@extends('layouts.admin')

@section('title', 'Serah Terima Mobil')

@section('content')
<div class="space-y-6">
    <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">SERAH TERIMA MOBIL</h1>

    @forelse($orders as $order)
        <div class="panel border border-gunmetal bg-carbon p-5 flex flex-wrap items-start justify-between gap-4 text-xs">
            <div class="space-y-1">
                <p class="font-mono font-bold text-giallo">#{{ $order->code }}</p>
                <p class="text-silver-bright">{{ $order->items->first()?->description }}</p>
                <p class="text-silver">{{ $order->customer?->user?->name }} ({{ $order->customer?->user?->phone }})</p>
                <p class="text-silver-dim">{{ $order->fulfillment_method === 'delivery' ? 'Antar towing: ' . $order->shipping_address : 'Diambil sendiri' }}</p>
            </div>
            <form method="POST" action="{{ route('admin.car-handovers.store', $order) }}" onsubmit="return confirm('Mobil sudah diserahkan?')">
                @csrf
                <button type="submit" class="px-4 py-2 bg-giallo text-nero font-heading font-bold uppercase rounded">
                    {{ $order->fulfillment_method === 'delivery' ? 'Sudah Dikirim' : 'Sudah Diambil' }}
                </button>
            </form>
        </div>
    @empty
        <div class="panel border border-gunmetal bg-carbon p-8 text-center text-xs text-silver-dim">Tidak ada mobil lunas yang menunggu serah terima.</div>
    @endforelse
</div>
@endsection
