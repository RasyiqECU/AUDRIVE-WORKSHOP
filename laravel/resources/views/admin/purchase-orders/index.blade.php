@extends('layouts.admin')

@section('title', 'Purchase Orders')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Procurement & Supplier</p>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                PURCHASE ORDERS (PO)
            </h1>
            <p class="text-xs text-silver-dim mt-1">
                Kelola pemesanan suku cadang ke supplier dan penerimaan barang masuk ke gudang.
            </p>
        </div>
        <a href="{{ route('admin.purchase-orders.create') }}" class="px-4 py-2 bg-giallo hover:bg-giallo-bright text-carbon font-heading text-xs uppercase tracking-wider font-bold rounded clip-angular-button transition-premium">
            + Buat PO Baru
        </a>
    </div>

    {{-- Filter --}}
    <div class="panel border border-gunmetal/70 bg-carbon p-4">
        <form action="{{ route('admin.purchase-orders.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <select name="status" class="bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2 focus:border-giallo focus:outline-none">
                <option value="">Semua Status</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent to Supplier</option>
                <option value="partially_received" {{ request('status') == 'partially_received' ? 'selected' : '' }}>Partially Received</option>
                <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Received (Selesai)</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright text-xs font-heading uppercase rounded">
                Filter
            </button>
            <a href="{{ route('admin.purchase-orders.index') }}" class="px-3 py-2 text-silver-dim hover:text-silver-bright text-xs font-heading uppercase">
                Reset
            </a>
        </form>
    </div>

    {{-- PO Table --}}
    <div class="panel border border-gunmetal/70 bg-carbon overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-silver-muted">
                <thead class="bg-steel/50 text-silver-bright font-heading uppercase text-[10px] tracking-wider border-b border-gunmetal">
                    <tr>
                        <th class="p-3">No. PO</th>
                        <th class="p-3">Supplier</th>
                        <th class="p-3">Dibuat Oleh</th>
                        <th class="p-3 text-right">Total Biaya</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Tanggal</th>
                        <th class="p-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/40 font-mono">
                    @forelse($orders as $po)
                        <tr class="hover:bg-steel/20 transition-colors">
                            <td class="p-3 text-giallo font-semibold">#PO-{{ str_pad($po->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td class="p-3 font-sans text-silver-bright font-medium">{{ $po->supplier_name }}</td>
                            <td class="p-3 font-sans text-silver-dim">{{ $po->creator?->name ?? 'System' }}</td>
                            <td class="p-3 text-right text-silver-bright">Rp {{ number_format($po->total_cost_idr ?? 0, 0, ',', '.') }}</td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 text-[10px] uppercase rounded font-bold
                                    @if($po->status === 'received') bg-emerald-950/60 text-emerald-400 border border-emerald-800
                                    @elseif($po->status === 'sent') bg-blue-950/60 text-blue-400 border border-blue-800
                                    @elseif($po->status === 'cancelled') bg-red-950/60 text-red-400 border border-red-800
                                    @else bg-steel text-silver-dim border border-gunmetal @endif">
                                    {{ str_replace('_', ' ', $po->status) }}
                                </span>
                            </td>
                            <td class="p-3 text-silver-dim text-[11px]">{{ $po->created_at->format('d M Y') }}</td>
                            <td class="p-3 text-right font-sans">
                                <a href="{{ route('admin.purchase-orders.show', $po->id) }}" class="text-xs text-giallo hover:underline font-heading uppercase">
                                    Detail →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-silver-dim font-sans">
                                Belum ada transaksi Purchase Order.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($orders->hasPages())
        <div>{{ $orders->links() }}</div>
    @endif
</div>
@endsection
