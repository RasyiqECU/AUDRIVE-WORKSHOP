@extends('layouts.public')

@section('title', 'Riwayat Pesanan Spare Part — AUDrive')

@section('content')
<section class="container mx-auto px-4 py-10">
    <div class="max-w-5xl mx-auto">
        <div class="flex items-center justify-between mb-8 pb-4 border-b border-gunmetal/40">
            <div>
                <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright">
                    RIWAYAT <span class="text-giallo">PESANAN</span>
                </h1>
                <p class="font-body text-xs text-silver-dim mt-1">Daftar transaksi dan status pengiriman suku cadang Anda.</p>
            </div>
            <a href="{{ route('parts.index') }}" class="font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider">
                + Beli Part Lainnya
            </a>
        </div>

        @if($orders->count() > 0)
            <div class="space-y-4">
                @foreach($orders as $order)
                    <div class="bg-carbon border border-gunmetal p-5 rounded-lg shadow-lg hover:border-giallo/40 transition">
                        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gunmetal/30 pb-3 mb-3">
                            <div class="flex items-center gap-3">
                                <span class="font-mono text-sm font-bold text-giallo">{{ $order->code }}</span>
                                <span class="font-mono text-xs text-silver-dim">{{ $order->created_at->format('d M Y, H:i') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 text-[10px] font-mono rounded uppercase {{ $order->status->value === 'completed' ? 'bg-verde/20 text-verde' : 'bg-warning/20 text-warning' }}">
                                    {{ strtoupper($order->status->value ?? 'PENDING') }}
                                </span>
                            </div>
                        </div>

                        <div class="space-y-2 mb-4">
                            @foreach($order->items as $item)
                                <div class="flex justify-between text-xs font-body text-silver-dim">
                                    <span>{{ $item->qty }}x {{ $item->description }}</span>
                                    <span class="font-mono text-silver-bright">Rp {{ number_format($item->subtotal_idr, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-gunmetal/30">
                            <div>
                                <span class="font-mono text-[10px] text-silver-dim uppercase block">Total Pembayaran</span>
                                <span class="font-mono text-sm font-bold text-giallo">Rp {{ number_format($order->total_idr, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('parts.orders.show', $order) }}" class="px-3 py-1.5 bg-steel/30 hover:bg-steel/50 border border-gunmetal text-silver-bright font-heading text-xs uppercase rounded transition">
                                    Detail Pesanan
                                </a>
                                @if($order->status->value === 'pending')
                                    <a href="{{ route('parts.checkout.payment', $order) }}" class="px-3 py-1.5 bg-giallo text-nero font-heading text-xs uppercase font-semibold rounded transition">
                                        Bayar Sekarang
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $orders->links() }}
            </div>
        @else
            <div class="py-16 text-center">
                <x-ui.empty-state
                    icon="📦"
                    title="Belum Ada Pesanan"
                    description="Anda belum memiliki riwayat pemesanan suku cadang di AUDrive."
                >
                    <div class="mt-6">
                        <x-ui.button href="{{ route('parts.index') }}" variant="primary" size="md">
                            Mulai Belanja Spare Part
                        </x-ui.button>
                    </div>
                </x-ui.empty-state>
            </div>
        @endif
    </div>
</section>
@endsection
