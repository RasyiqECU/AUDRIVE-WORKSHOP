@extends('layouts.admin')

@section('title', 'Laporan Stok & Inventori')

@section('content')
<div class="space-y-6">
    {{-- Header & Sub-navigation Tabs --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.reports.index') }}" class="text-giallo font-mono text-sm tracking-widest uppercase hover:underline">Laporan</a>
                <span class="text-silver-dim text-xs">/</span>
                <span class="text-silver-dim text-xs font-mono">Suku Cadang & Stok</span>
            </div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide mt-1">
                LAPORAN INVENTORI & SUKU CADANG
            </h1>
            <p class="text-sm text-silver-dim mt-1">
                Valuasi persediaan suku cadang supercar, pemantauan batas stok minimum, dan katalog gudang.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-steel/40 hover:bg-steel text-silver-bright border border-gunmetal font-heading text-xs uppercase tracking-wider rounded transition-premium flex items-center gap-2">
                <span>🖨️</span> Cetak Laporan
            </button>
        </div>
    </div>

    {{-- Sub Navigation Tabs --}}
    <div class="flex items-center gap-2 border-b border-gunmetal overflow-x-auto pb-2">
        <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 border-b-2 border-transparent text-silver-dim hover:text-silver-bright font-heading text-xs uppercase tracking-wider transition-premium flex items-center gap-2">
            <span>📊</span> Ikhtisar Utama
        </a>
        <a href="{{ route('admin.reports.sales') }}" class="px-4 py-2 border-b-2 border-transparent text-silver-dim hover:text-silver-bright font-heading text-xs uppercase tracking-wider transition-premium flex items-center gap-2">
            <span>🏎</span> Penjualan Supercar
        </a>
        <a href="{{ route('admin.reports.workshop') }}" class="px-4 py-2 border-b-2 border-transparent text-silver-dim hover:text-silver-bright font-heading text-xs uppercase tracking-wider transition-premium flex items-center gap-2">
            <span>🔧</span> Servis & Workshop
        </a>
        <a href="{{ route('admin.reports.inventory') }}" class="px-4 py-2 border-b-2 border-giallo text-giallo font-heading text-xs uppercase tracking-wider flex items-center gap-2">
            <span>⚙️</span> Suku Cadang & Stok
        </a>
        <a href="{{ route('admin.reports.finance') }}" class="px-4 py-2 border-b-2 border-transparent text-silver-dim hover:text-silver-bright font-heading text-xs uppercase tracking-wider transition-premium flex items-center gap-2">
            <span>💳</span> Finansial & Kasir
        </a>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="panel border border-gunmetal bg-carbon p-5">
            <span class="text-silver-dim text-xs font-heading uppercase tracking-wider">Total Nilai Aset Suku Cadang</span>
            <p class="font-mono text-2xl font-bold text-giallo mt-2">
                Rp {{ number_format($totalPartsValue, 0, ',', '.') }}
            </p>
        </div>
        <div class="panel border border-gunmetal bg-carbon p-5">
            <span class="text-silver-dim text-xs font-heading uppercase tracking-wider">Total Varian Part (SKU)</span>
            <p class="font-mono text-2xl font-bold text-silver-bright mt-2">
                {{ $totalItems }} <span class="text-xs font-normal text-silver-dim">Item</span>
            </p>
        </div>
        <div class="panel border border-gunmetal bg-carbon p-5">
            <span class="text-silver-dim text-xs font-heading uppercase tracking-wider">Part Menipis / Perlu Re-order</span>
            <p class="font-mono text-2xl font-bold text-danger mt-2">
                {{ $lowStockCount }} <span class="text-xs font-normal text-silver-dim">SKU</span>
            </p>
        </div>
    </div>

    {{-- Inventory Table --}}
    <div class="panel border border-gunmetal bg-carbon overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-steel/30 text-silver-dim font-heading uppercase text-[10px] border-b border-gunmetal">
                        <th class="py-3 px-4">Kode Part</th>
                        <th class="py-3 px-4">Nama Suku Cadang</th>
                        <th class="py-3 px-4">Kategori / Kompatibilitas</th>
                        <th class="py-3 px-4 text-center">Sisa Stok</th>
                        <th class="py-3 px-4 text-right">Harga Satuan</th>
                        <th class="py-3 px-4 text-right">Valuasi Stok</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/30">
                    @forelse($spareParts as $part)
                        <tr class="hover:bg-steel/10 transition-premium">
                            <td class="py-3.5 px-4 font-mono font-bold text-silver-bright">
                                {{ $part->part_number ?? $part->code ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="font-bold text-silver-bright">{{ $part->name }}</p>
                                <p class="text-[10px] font-mono text-silver-dim">{{ $part->brand ?? 'OEM' }}</p>
                            </td>
                            <td class="py-3.5 px-4 text-silver-dim">
                                {{ $part->category ?? 'General' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-mono font-bold {{ $part->stock <= $part->low_stock_threshold ? 'bg-danger/20 text-danger border border-danger/40' : 'bg-steel/40 text-silver-bright' }}">
                                    {{ $part->stock }} pcs
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-silver-dim text-right">
                                Rp {{ number_format($part->price_idr, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-giallo text-right">
                                Rp {{ number_format($part->stock * $part->price_idr, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-silver-dim">Belum ada data inventori suku cadang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($spareParts->hasPages())
            <div class="p-4 border-t border-gunmetal">
                {{ $spareParts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
