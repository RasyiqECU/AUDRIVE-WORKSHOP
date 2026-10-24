@extends('layouts.admin')

@section('title', 'Peringatan Stok Menipis')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <a href="{{ route('admin.inventory.index') }}" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1 mb-2">
                ← Kembali ke Inventaris
            </a>
            <p class="font-heading text-xs uppercase tracking-[0.2em] text-red-400">Inventory Alert</p>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                DAFTAR SPARE PART STOK MENIPIS
            </h1>
            <p class="text-xs text-silver-dim mt-1">
                Daftar suku cadang yang telah mencapai atau berada di bawah batas minimum pemesanan ulang.
            </p>
        </div>
        <a href="{{ route('admin.purchase-orders.create') }}" class="px-4 py-2 bg-giallo hover:bg-giallo-bright text-carbon font-heading text-xs uppercase tracking-wider font-bold rounded clip-angular-button transition-premium">
            + Buat Purchase Order
        </a>
    </div>

    {{-- Low Stock Table --}}
    <div class="panel border border-red-950/70 bg-carbon overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-silver-muted">
                <thead class="bg-red-950/30 text-silver-bright font-heading uppercase text-[10px] tracking-wider border-b border-red-900/50">
                    <tr>
                        <th class="p-3">SKU</th>
                        <th class="p-3">Nama Spare Part</th>
                        <th class="p-3">Kategori</th>
                        <th class="p-3 text-center">Stok Saat Ini</th>
                        <th class="p-3 text-center">Batas Minimum</th>
                        <th class="p-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/40 font-mono">
                    @forelse($parts as $part)
                        <tr class="hover:bg-steel/20 transition-colors">
                            <td class="p-3 text-giallo font-semibold">{{ $part->sku }}</td>
                            <td class="p-3 font-sans text-silver-bright font-medium">{{ $part->name }}</td>
                            <td class="p-3 font-sans text-silver-dim">{{ $part->category?->name ?? '-' }}</td>
                            <td class="p-3 text-center font-bold text-red-400">{{ $part->stock }}</td>
                            <td class="p-3 text-center text-silver-dim">{{ $part->low_stock_threshold }}</td>
                            <td class="p-3 text-right font-sans">
                                <a href="{{ route('admin.inventory.edit', $part->id) }}" class="text-xs text-giallo hover:underline font-heading uppercase">
                                    Restock / Edit →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-silver-dim font-sans">
                                Semua stok suku cadang aman di atas batas minimum.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
