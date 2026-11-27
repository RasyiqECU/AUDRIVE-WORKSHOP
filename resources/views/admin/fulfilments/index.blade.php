@extends('layouts.admin')

@section('title', 'Siapkan Pesanan')

@section('content')
<div class="space-y-6">
    <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">SIAPKAN PESANAN LUNAS</h1>

    @forelse($orders as $order)
        <div class="panel border border-gunmetal bg-carbon p-5 flex flex-wrap items-start justify-between gap-4 text-xs">
            <div class="space-y-1">
                <p class="font-mono font-bold text-giallo">#{{ $order->code }}</p>
                <p class="text-silver-bright">{{ $order->customer?->user?->name }} ({{ $order->customer?->user?->phone }})</p>
                <p class="text-silver">{{ $order->items->map(fn ($i) => "{$i->description} x{$i->qty}")->implode(', ') }}</p>
                <p class="text-silver-dim">
                    {{ $order->fulfillment_method === 'delivery' ? 'Dikirim kurir: ' . $order->shipping_address : 'Diambil di bengkel' }}
                </p>
                <p class="text-silver-dim">{{ $order->progressLabel() }}</p>
            </div>
            @if(! $order->prepared_at)
                <form method="POST" action="{{ route('admin.fulfilments.prepare', $order) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-giallo text-nero font-heading font-bold uppercase rounded">Barang Sudah Siap</button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.fulfilments.hand-over', $order) }}" onsubmit="return confirm('Barang sudah diserahkan/dikirim?')">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-verde text-nero font-heading font-bold uppercase rounded">
                        {{ $order->fulfillment_method === 'delivery' ? 'Sudah Dikirim' : 'Sudah Diambil' }}
                    </button>
                </form>
            @endif
        </div>
    @empty
        <div class="panel border border-gunmetal bg-carbon p-8 text-center text-xs text-silver-dim">Tidak ada pesanan yang perlu disiapkan.</div>
    @endforelse
</div>
@endsection
