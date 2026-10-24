@extends('layouts.admin')

@section('title', 'Struk Transaksi POS #' . $salesOrder->id)

@section('content')
<div class="space-y-6 max-w-xl mx-auto">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.pos.index') }}" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1">
            ← Kasir Baru
        </a>
        <a href="{{ route('admin.pos.print', $salesOrder) }}" target="_blank" class="px-4 py-2 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright font-heading text-xs uppercase tracking-wider rounded">
            🖨️ Cetak Struk
        </a>
    </div>

    {{-- Thermal Receipt Preview --}}
    <div class="panel border border-gunmetal/70 bg-carbon p-6 space-y-4 font-mono text-xs text-silver-muted shadow-2xl">
        <div class="text-center space-y-1 border-b border-gunmetal/60 pb-4">
            <h2 class="font-display text-lg text-silver-bright font-bold tracking-widest">AUDRIVE MOTORS</h2>
            <p class="text-[10px] text-silver-dim">Workshop & Luxury Auto Solutions</p>
            <p class="text-[10px] text-silver-dim">No. Trx: #{{ $salesOrder->code }}</p>
            <p class="text-[10px] text-silver-dim">{{ $salesOrder->created_at->format('d/m/Y H:i:s') }}</p>
            @if($salesOrder->customer)
                <p class="text-[10px] text-silver-bright font-bold">Customer: {{ $salesOrder->customer->user?->name }}</p>
            @endif
        </div>

        <div class="space-y-2 py-2 border-b border-gunmetal/60">
            @foreach($salesOrder->items as $item)
                <div class="flex justify-between">
                    <div>
                        <p class="text-silver-bright font-medium">{{ $item->description ?: ($item->sparePart?->name ?? 'Item') }}</p>
                        <p class="text-[10px] text-silver-dim">{{ $item->qty }} x Rp {{ number_format($item->unit_price_idr, 0, ',', '.') }}</p>
                    </div>
                    <p class="text-silver-bright font-bold">Rp {{ number_format($item->subtotal_idr, 0, ',', '.') }}</p>
                </div>
            @endforeach
        </div>

        <div class="space-y-1.5 pt-2">
            @if($salesOrder->discount_idr > 0)
                <div class="flex justify-between text-xs text-verde">
                    <span>Diskon:</span>
                    <span>- Rp {{ number_format($salesOrder->discount_idr, 0, ',', '.') }}</span>
                </div>
            @endif
            @if($salesOrder->tax_idr > 0)
                <div class="flex justify-between text-xs text-silver-dim">
                    <span>PPN (11%):</span>
                    <span>Rp {{ number_format($salesOrder->tax_idr, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="flex justify-between text-silver-bright text-sm font-bold border-t border-gunmetal/60 pt-2">
                <span>TOTAL:</span>
                <span class="text-giallo">Rp {{ number_format($salesOrder->total_idr, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-[11px] text-silver-dim">
                <span>Pembayaran:</span>
                <span class="uppercase font-bold text-silver-bright">{{ $salesOrder->payments?->first()?->method ?? 'CASH' }}</span>
            </div>
        </div>

        <div class="text-center pt-6 text-[10px] text-silver-dim">
            <p>Terima kasih atas kunjungan Anda!</p>
            <p>Barang yang sudah dibeli tidak dapat ditukar kecuali ada perjanjian.</p>
        </div>
    </div>
</div>
@endsection
