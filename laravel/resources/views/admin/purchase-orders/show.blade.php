@extends('layouts.admin')

@section('title', 'Detail Purchase Order #' . $purchaseOrder->id)

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    {{-- Header --}}
    <div>
        <a href="{{ route('admin.purchase-orders.index') }}" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1 mb-2">
            ← Kembali ke Daftar PO
        </a>
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <span class="text-xs text-giallo font-mono">#PO-{{ str_pad($purchaseOrder->id, 5, '0', STR_PAD_LEFT) }}</span>
                <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                    PURCHASE ORDER: {{ $purchaseOrder->supplier_name }}
                </h1>
                <p class="text-xs text-silver-dim mt-1">
                    Dibuat oleh: {{ $purchaseOrder->creator?->name ?? 'System' }} pada {{ $purchaseOrder->created_at->format('d M Y, H:i') }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 text-xs uppercase rounded font-bold
                    @if($purchaseOrder->status === 'received') bg-emerald-950/60 text-emerald-400 border border-emerald-800
                    @elseif($purchaseOrder->status === 'sent') bg-blue-950/60 text-blue-400 border border-blue-800
                    @elseif($purchaseOrder->status === 'cancelled') bg-red-950/60 text-red-400 border border-red-800
                    @else bg-steel text-silver-dim border border-gunmetal @endif">
                    {{ str_replace('_', ' ', $purchaseOrder->status) }}
                </span>
            </div>
        </div>
    </div>

    {{-- Items Table --}}
    <div class="panel border border-gunmetal/70 bg-carbon overflow-hidden">
        <div class="p-4 border-b border-gunmetal/50 bg-steel/30">
            <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright">Daftar Item Spare Part</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-silver-muted">
                <thead class="bg-steel/50 text-silver-bright font-heading uppercase text-[10px] tracking-wider border-b border-gunmetal">
                    <tr>
                        <th class="p-3">SKU</th>
                        <th class="p-3">Nama Part</th>
                        <th class="p-3 text-center">Qty Dipesan</th>
                        <th class="p-3 text-center">Qty Diterima</th>
                        <th class="p-3 text-right">Harga Beli</th>
                        <th class="p-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/40 font-mono">
                    @php $grandTotal = 0; @endphp
                    @forelse($purchaseOrder->items as $item)
                        @php $subtotal = $item->qty * $item->cost_idr; $grandTotal += $subtotal; @endphp
                        <tr class="hover:bg-steel/20 transition-colors">
                            <td class="p-3 text-giallo">{{ $item->sparePart?->sku ?? '-' }}</td>
                            <td class="p-3 font-sans text-silver-bright font-medium">{{ $item->sparePart?->name ?? 'Custom Part' }}</td>
                            <td class="p-3 text-center text-silver-bright">{{ $item->qty }}</td>
                            <td class="p-3 text-center text-emerald-400 font-bold">{{ $item->received_qty ?? 0 }}</td>
                            <td class="p-3 text-right text-silver-bright">Rp {{ number_format($item->cost_idr, 0, ',', '.') }}</td>
                            <td class="p-3 text-right text-giallo font-bold">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-silver-dim">Tidak ada item dalam PO ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="border-t border-gunmetal bg-steel/40 font-mono">
                    <tr>
                        <td colspan="5" class="p-3 text-right font-heading uppercase text-silver-bright font-bold">Total Nilai PO:</td>
                        <td class="p-3 text-right text-giallo text-sm font-bold">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
