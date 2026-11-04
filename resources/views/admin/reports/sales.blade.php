@extends('layouts.admin')

@section('title', 'Laporan Resmi Penjualan Supercar')

@section('content')
<div class="space-y-6">
    {{-- Header & Sub-navigation Tabs (Screen only) --}}
    <div class="no-print flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.reports.index') }}" class="text-giallo font-mono text-sm tracking-widest uppercase hover:underline">Laporan</a>
                <span class="text-silver-dim text-xs">/</span>
                <span class="text-silver-dim text-xs font-mono">Penjualan Supercar</span>
            </div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide mt-1">
                LAPORAN RESMI PENJUALAN SUPERCAR
            </h1>
            <p class="text-sm text-silver-dim mt-1">
                Rekapitulasi transaksi unit supercar showroom, faktur penjualan, diskon, dan performa penutupan penjualan unit.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-steel/60 hover:bg-steel text-silver-bright border border-gunmetal font-heading text-xs uppercase tracking-wider rounded transition-premium flex items-center gap-2 shadow-sm">
                <span>🖨️</span> Cetak Laporan Formal
            </button>
        </div>
    </div>

    {{-- Sub Navigation Tabs (Screen only) --}}
    <div class="no-print flex items-center gap-2 border-b border-gunmetal overflow-x-auto pb-2 print:hidden">
        <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 border-b-2 border-transparent text-silver-dim hover:text-silver-bright font-heading text-xs uppercase tracking-wider transition-premium flex items-center gap-2">
            <span>📊</span> Ikhtisar Utama
        </a>
        <a href="{{ route('admin.reports.sales') }}" class="px-4 py-2 border-b-2 border-giallo text-giallo font-heading text-xs uppercase tracking-wider flex items-center gap-2 font-bold">
            <span>🏎</span> Penjualan Supercar
        </a>
        <a href="{{ route('admin.reports.workshop') }}" class="px-4 py-2 border-b-2 border-transparent text-silver-dim hover:text-silver-bright font-heading text-xs uppercase tracking-wider transition-premium flex items-center gap-2">
            <span>🔧</span> Servis & Workshop
        </a>
        <a href="{{ route('admin.reports.inventory') }}" class="px-4 py-2 border-b-2 border-transparent text-silver-dim hover:text-silver-bright font-heading text-xs uppercase tracking-wider transition-premium flex items-center gap-2">
            <span>⚙️</span> Suku Cadang & Stok
        </a>
        <a href="{{ route('admin.reports.finance') }}" class="px-4 py-2 border-b-2 border-transparent text-silver-dim hover:text-silver-bright font-heading text-xs uppercase tracking-wider transition-premium flex items-center gap-2">
            <span>💳</span> Finansial & Kasir
        </a>
    </div>

    {{-- Filter Bar --}}
    <div class="no-print panel border border-gunmetal bg-carbon p-4 print:hidden">
        <form method="GET" action="{{ route('admin.reports.sales') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            {{-- Quick Presets --}}
            <div class="sm:col-span-2 lg:col-span-5 flex items-center gap-1.5 flex-wrap pb-2 border-b border-gunmetal/50">
                <span class="text-xs font-heading uppercase text-silver-dim mr-1">Preset:</span>
                <a href="{{ route('admin.reports.sales', ['preset' => 'today']) }}" class="px-2.5 py-1 rounded text-xs font-mono uppercase {{ $preset === 'today' ? 'bg-giallo text-carbon font-bold' : 'bg-steel/30 text-silver-dim hover:text-silver-bright' }}">Hari Ini</a>
                <a href="{{ route('admin.reports.sales', ['preset' => 'this_week']) }}" class="px-2.5 py-1 rounded text-xs font-mono uppercase {{ $preset === 'this_week' ? 'bg-giallo text-carbon font-bold' : 'bg-steel/30 text-silver-dim hover:text-silver-bright' }}">Minggu Ini</a>
                <a href="{{ route('admin.reports.sales', ['preset' => 'this_month']) }}" class="px-2.5 py-1 rounded text-xs font-mono uppercase {{ $preset === 'this_month' ? 'bg-giallo text-carbon font-bold' : 'bg-steel/30 text-silver-dim hover:text-silver-bright' }}">Bulan Ini</a>
                <a href="{{ route('admin.reports.sales', ['preset' => 'last_month']) }}" class="px-2.5 py-1 rounded text-xs font-mono uppercase {{ $preset === 'last_month' ? 'bg-giallo text-carbon font-bold' : 'bg-steel/30 text-silver-dim hover:text-silver-bright' }}">Bulan Lalu</a>
                <a href="{{ route('admin.reports.sales', ['preset' => 'this_year']) }}" class="px-2.5 py-1 rounded text-xs font-mono uppercase {{ $preset === 'this_year' ? 'bg-giallo text-carbon font-bold' : 'bg-steel/30 text-silver-dim hover:text-silver-bright' }}">Tahun Ini</a>
                <a href="{{ route('admin.reports.sales', ['preset' => 'all']) }}" class="px-2.5 py-1 rounded text-xs font-mono uppercase {{ $preset === 'all' ? 'bg-giallo text-carbon font-bold' : 'bg-steel/30 text-silver-dim hover:text-silver-bright' }}">Semua</a>
            </div>

            {{-- Search --}}
            <div>
                <label class="block text-[10px] font-heading uppercase tracking-wider text-silver-dim mb-1">Cari No. Order / Customer</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nomor atau customer..."
                       class="w-full bg-steel/20 border border-gunmetal rounded px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none font-mono">
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-[10px] font-heading uppercase tracking-wider text-silver-dim mb-1">Status Penjualan</label>
                <select name="status" class="w-full bg-steel/20 border border-gunmetal rounded px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                    <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Lunas (Paid)</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>

            {{-- Date From --}}
            <div>
                <label class="block text-[10px] font-heading uppercase tracking-wider text-silver-dim mb-1">Mulai Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate?->format('Y-m-d') }}"
                       class="w-full bg-steel/20 border border-gunmetal rounded px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none font-mono">
            </div>

            {{-- Date To --}}
            <div>
                <label class="block text-[10px] font-heading uppercase tracking-wider text-silver-dim mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate?->format('Y-m-d') }}"
                       class="w-full bg-steel/20 border border-gunmetal rounded px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none font-mono">
            </div>

            {{-- Filter Actions --}}
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-giallo hover:bg-giallo/90 text-carbon font-heading text-xs uppercase font-bold rounded transition-premium">
                    Filter
                </button>
                <a href="{{ route('admin.reports.sales') }}" class="px-3 py-2 bg-steel/40 hover:bg-steel border border-gunmetal text-silver-dim hover:text-silver-bright text-xs rounded transition-premium" title="Reset filter">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Official Print Letterhead (Only visible in Print) --}}
    <div class="hidden print:block border-b-2 border-black pb-4 mb-6">
        <table class="w-full text-black">
            <tr>
                <td style="width: 60%; vertical-align: top;">
                    <h2 class="text-2xl font-bold uppercase tracking-wider">PT AUDRIVE SUPERCAR INDONESIA</h2>
                    <p class="text-xs font-semibold uppercase text-gray-600">Divisi Penjualan Supercar & Showroom Komersial</p>
                    <p class="text-[10px] text-gray-600">Graha AUDrive Kav. 88, SCBD, Jl. Jend. Sudirman, Jakarta Selatan 12190 &bull; Telp: +62 21 8088 9900</p>
                </td>
                <td style="width: 40%; text-align: right; vertical-align: top;">
                    <h3 class="text-lg font-bold uppercase">LAPORAN PENJUALAN SUPERCAR</h3>
                    <p class="text-xs font-mono">No. Dokumen: RPT-SLS-{{ date('Ymd-His') }}</p>
                    <p class="text-xs">Periode: {{ $startDate ? $startDate->format('d/m/Y') : 'Awal' }} s/d {{ $endDate ? $endDate->format('d/m/Y') : 'Sekarang' }}</p>
                    <p class="text-[10px] text-gray-500">Dicetak: {{ now()->format('d/m/Y H:i') }} WIB oleh {{ auth()->user()->name }}</p>
                </td>
            </tr>
        </table>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="panel border border-gunmetal bg-carbon p-5 relative overflow-hidden group hover:border-giallo/40 transition-premium">
            <span class="text-silver-dim text-xs font-heading uppercase tracking-wider">Total Revenue Unit Terjual</span>
            <p class="font-mono text-xl sm:text-2xl font-bold text-giallo mt-2">
                Rp {{ number_format($totalCarRevenue, 0, ',', '.') }}
            </p>
            <p class="text-[10px] text-silver-dim font-mono mt-1">Status Paid & Completed</p>
        </div>
        <div class="panel border border-gunmetal bg-carbon p-5 relative overflow-hidden group hover:border-arancio/40 transition-premium">
            <span class="text-silver-dim text-xs font-heading uppercase tracking-wider">Total Unit Terjual</span>
            <p class="font-mono text-2xl font-bold text-arancio mt-2">
                {{ $unitsSold }} <span class="text-xs font-normal text-silver-dim">Kendaraan</span>
            </p>
            <p class="text-[10px] text-silver-dim font-mono mt-1">Diskon: Rp {{ number_format($totalDiscountGiven, 0, ',', '.') }}</p>
        </div>
        <div class="panel border border-gunmetal bg-carbon p-5 relative overflow-hidden group hover:border-blue-400/40 transition-premium">
            <span class="text-silver-dim text-xs font-heading uppercase tracking-wider">Rata-rata Harga Unit (ASP)</span>
            <p class="font-mono text-xl sm:text-2xl font-bold text-blue-400 mt-2">
                Rp {{ number_format($averageSellingPrice, 0, ',', '.') }}
            </p>
            <p class="text-[10px] text-silver-dim font-mono mt-1">Average Selling Price per unit</p>
        </div>
        <div class="panel border border-gunmetal bg-carbon p-5 relative overflow-hidden group hover:border-verde/40 transition-premium">
            <span class="text-silver-dim text-xs font-heading uppercase tracking-wider">Ready Stock Showroom</span>
            <p class="font-mono text-2xl font-bold text-verde mt-2">
                {{ $availableUnits }} <span class="text-xs font-normal text-silver-dim">Unit Siap Jual</span>
            </p>
            <p class="text-[10px] text-silver-dim font-mono mt-1">Tersedia di lantai showroom</p>
        </div>
    </div>

    {{-- Orders Table --}}
    <div class="panel border border-gunmetal bg-carbon overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-steel/30 text-silver-dim font-heading uppercase text-[10px] border-b border-gunmetal">
                        <th class="py-3 px-4">No. Order & Ref</th>
                        <th class="py-3 px-4">Customer</th>
                        <th class="py-3 px-4">Unit Supercar Terkait</th>
                        <th class="py-3 px-4">Tanggal Transaksi</th>
                        <th class="py-3 px-4 text-right">Harga Unit</th>
                        <th class="py-3 px-4 text-right">Diskon</th>
                        <th class="py-3 px-4 text-right">Total Transaksi</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center print:hidden">Dokumen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/30">
                    @forelse($salesOrders as $order)
                        @php
                            $carItem = $order->items->firstWhere('car_unit_id');
                            $carUnit = $carItem?->carUnit ?? null;
                            $isPaid = in_array($order->status->value ?? (string)$order->status, ['paid', 'completed'], true);
                        @endphp
                        <tr class="hover:bg-steel/10 transition-premium">
                            <td class="py-3.5 px-4 font-mono font-bold text-silver-bright">
                                {{ $order->code }}
                                @if($order->invoice)
                                    <p class="text-[10px] text-giallo font-normal">{{ $order->invoice->number }}</p>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="font-bold text-silver-bright">{{ $order->customer->user->name ?? 'Walk-in Customer' }}</p>
                                <p class="text-[10px] font-mono text-silver-dim">{{ $order->customer->user->phone ?? '-' }}</p>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($carUnit)
                                    <p class="font-bold text-silver-bright">{{ $carUnit->brand }} {{ $carUnit->model }} ({{ $carUnit->year }})</p>
                                    <p class="text-[10px] font-mono text-silver-dim">VIN: {{ $carUnit->vin ?? '-' }} &bull; Plat: {{ $carUnit->plate_number ?? '-' }}</p>
                                @else
                                    <p class="text-silver-dim italic">Unit Supercar Showroom</p>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-mono text-silver-dim">
                                {{ $order->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3.5 px-4 font-mono text-silver-dim text-right">
                                Rp {{ number_format($order->subtotal_idr, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 font-mono text-emerald-400 text-right">
                                {{ $order->discount_idr > 0 ? '- Rp ' . number_format($order->discount_idr, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-giallo text-right">
                                Rp {{ number_format($order->total_idr, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($isPaid)
                                    <span class="inline-flex px-2 py-0.5 rounded text-[9px] font-mono uppercase font-bold bg-verde/15 text-verde border border-verde/40">
                                        ✓ Lunas
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded text-[9px] font-mono uppercase font-bold bg-amber-500/15 text-amber-400 border border-amber-500/40">
                                        {{ $order->status->value ?? $order->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center print:hidden">
                                @if($order->invoice)
                                    <a href="{{ route('admin.invoices.show', $order->invoice) }}" class="px-2.5 py-1 bg-steel/50 hover:bg-steel border border-gunmetal text-silver-bright rounded text-[10px] font-heading uppercase tracking-wider transition-premium">
                                        Invoice
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-silver-dim">
                                <span class="text-3xl block mb-2 opacity-50">🏎</span>
                                <p class="font-display font-bold text-silver-bright text-sm tracking-wide">Tidak Ada Data Penjualan</p>
                                <p class="text-xs text-silver-dim mt-1">Tidak ditemukan transaksi penjualan mobil pada periode atau filter yang dipilih.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                {{-- Accumulator Grand Total Row --}}
                @if(count($salesOrders) > 0)
                    <tfoot>
                        <tr class="bg-steel/40 border-t-2 border-gunmetal font-mono font-bold text-silver-bright text-xs">
                            <td colspan="4" class="py-3 px-4 text-right uppercase font-heading text-[11px] text-giallo">
                                REKAPITULASI TOTAL PENJUALAN:
                            </td>
                            <td class="py-3 px-4 text-right">
                                Rp {{ number_format($salesOrders->sum('subtotal_idr'), 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right text-emerald-400">
                                - Rp {{ number_format($salesOrders->sum('discount_idr'), 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right text-giallo text-sm">
                                Rp {{ number_format($salesOrders->sum('total_idr'), 0, ',', '.') }}
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        @if(!$isPrint && method_exists($salesOrders, 'hasPages') && $salesOrders->hasPages())
            <div class="p-4 border-t border-gunmetal bg-steel/20 print:hidden">
                {{ $salesOrders->links() }}
            </div>
        @endif
    </div>

    {{-- Official Report Sign-off Section (Print view) --}}
    <div class="hidden print:block pt-12">
        <table class="w-full text-center text-black text-xs">
            <tr>
                <td style="width: 33%;">
                    <p class="text-[10px] font-semibold text-gray-600 mb-14">Dibuat Oleh (Sales Executive):</p>
                    <p class="font-bold underline">{{ auth()->user()->name }}</p>
                    <p class="text-[9px] text-gray-500">Showroom Sales Desk</p>
                </td>
                <td style="width: 33%;">
                    <p class="text-[10px] font-semibold text-gray-600 mb-14">Diperiksa Oleh (Finance Supervisor):</p>
                    <p class="font-bold underline">Finance Controller</p>
                    <p class="text-[9px] text-gray-500">Divisi Keuangan & Pajak</p>
                </td>
                <td style="width: 33%;">
                    <p class="text-[10px] font-semibold text-gray-600 mb-14">Disetujui Oleh (Direktur):</p>
                    <p class="font-bold underline">Super Admin AUDrive</p>
                    <p class="text-[9px] text-gray-500">Direktur Utama</p>
                </td>
            </tr>
        </table>
    </div>
</div>
@endsection
