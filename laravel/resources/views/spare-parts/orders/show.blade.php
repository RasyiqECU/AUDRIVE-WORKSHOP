@extends('layouts.public')

@section('title', 'Detail Pesanan #' . $order->code . ' — AUDrive')

@section('content')
<section class="container mx-auto px-4 py-10">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('parts.orders.index') }}" class="font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider">
                ← Kembali ke Riwayat Pesanan
            </a>
        </div>

        <div class="bg-carbon border border-gunmetal rounded-lg overflow-hidden shadow-2xl">
            {{-- Header --}}
            <div class="bg-steel/30 p-6 border-b border-gunmetal flex flex-wrap items-center justify-between gap-4">
                <div>
                    <span class="font-mono text-xs text-silver-dim block">FAKTUR PESANAN</span>
                    <h1 class="font-display text-xl md:text-2xl font-bold text-silver-bright">
                        #{{ $order->code }}
                    </h1>
                </div>
                <div class="text-right">
                    <span class="font-mono text-xs text-silver-dim block">Tanggal: {{ $order->created_at->format('d M Y, H:i') }}</span>
                    <span class="inline-block mt-1 px-3 py-0.5 text-xs font-mono font-bold rounded uppercase {{ $order->status->value === 'completed' ? 'bg-verde/20 text-verde' : 'bg-warning/20 text-warning' }}">
                        {{ strtoupper($order->status->value ?? 'PENDING') }}
                    </span>
                </div>
            </div>

            {{-- Items Table --}}
            <div class="p-6">
                <table class="w-full text-left font-body text-xs mb-6">
                    <thead>
                        <tr class="border-b border-gunmetal font-heading uppercase text-[10px] text-silver-dim">
                            <th class="py-2.5 px-3">Item Suku Cadang</th>
                            <th class="py-2.5 px-3 text-right">Harga Satuan</th>
                            <th class="py-2.5 px-3 text-center">Qty</th>
                            <th class="py-2.5 px-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gunmetal/30">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="py-3 px-3">
                                    <p class="font-heading font-semibold text-silver-bright">{{ $item->description }}</p>
                                    @if($item->sparePart)
                                        <p class="font-mono text-[10px] text-silver-dim">SKU: {{ $item->sparePart->sku }}</p>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-right font-mono text-silver-bright">
                                    Rp {{ number_format($item->unit_price_idr, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-center font-mono">{{ $item->qty }}</td>
                                <td class="py-3 px-3 text-right font-mono font-bold text-silver-bright">
                                    Rp {{ number_format($item->subtotal_idr, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Totals --}}
                <div class="border-t border-gunmetal/40 pt-4 max-w-xs ml-auto space-y-2 font-body text-xs">
                    <div class="flex justify-between text-silver-dim">
                        <span>Subtotal</span>
                        <span class="font-mono text-silver-bright">Rp {{ number_format($order->subtotal_idr, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-silver-dim">
                        <span>PPN (11%)</span>
                        <span class="font-mono text-silver-bright">Rp {{ number_format($order->tax_idr, 0, ',', '.') }}</span>
                    </div>
                    <div class="pt-2 border-t border-gunmetal/40 flex justify-between font-bold">
                        <span class="font-heading uppercase text-silver-bright">Total Tagihan</span>
                        <span class="font-mono text-base text-giallo">Rp {{ number_format($order->total_idr, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Notes / Shipping Address --}}
                @if($order->notes)
                    <div class="mt-6 p-4 bg-nero border border-gunmetal/40 rounded text-xs">
                        <span class="font-heading uppercase text-[10px] text-silver-dim block mb-1 font-semibold">Informasi Pengiriman / Catatan:</span>
                        <p class="font-body text-silver-bright">{{ $order->notes }}</p>
                    </div>
                @endif

                {{-- Action Buttons --}}
                <div class="mt-8 pt-4 border-t border-gunmetal/40 flex flex-wrap justify-between items-center gap-4">
                    <button onclick="window.print()" class="px-4 py-2 bg-steel/30 hover:bg-steel/50 border border-gunmetal text-silver-bright font-heading text-xs uppercase rounded transition">
                        🖨️ Cetak Invoice
                    </button>
                    @if($order->status->value === 'pending')
                        <a href="{{ route('parts.checkout.payment', $order) }}" class="px-5 py-2 bg-giallo text-nero font-heading text-xs uppercase font-bold rounded transition">
                            Lakukan Pembayaran →
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
