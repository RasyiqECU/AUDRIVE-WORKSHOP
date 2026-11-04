@extends('layouts.admin')

@section('title', 'Detail Invoice #' . $invoice->number)

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    {{-- Header & Actions (Screen only) --}}
    <div class="no-print flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.invoices.index') }}" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1 mb-1">
                ← Kembali ke Daftar Invoice
            </a>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide">
                DETAIL INVOICE & BUKTI BAYAR
            </h1>
            <p class="font-mono text-xs text-giallo">{{ $invoice->number }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.pos.print', $salesOrder) }}" target="_blank" class="px-3.5 py-2 bg-steel/60 hover:bg-steel border border-gunmetal text-silver-bright font-heading text-xs uppercase tracking-wider rounded flex items-center gap-1.5 transition-premium" title="Cetak struk kasir thermal 80mm">
                <span>🖨️</span> Struk Thermal
            </a>
            <button onclick="window.print()" class="px-3.5 py-2 bg-steel/60 hover:bg-steel border border-gunmetal text-silver-bright font-heading text-xs uppercase tracking-wider rounded flex items-center gap-1.5 transition-premium" title="Cetak Faktur A4">
                <span>🖨️</span> Cetak A4
            </button>
            <a href="{{ route('admin.invoices.pdf', $invoice) }}" class="px-4 py-2 bg-giallo hover:bg-giallo/90 text-carbon font-heading text-xs font-bold uppercase tracking-wider rounded flex items-center gap-1.5 transition-premium shadow-lg shadow-giallo/10">
                <span>📄</span> Unduh PDF
            </a>
        </div>
    </div>

    @php
        $isPaid = $invoice->status->value === 'paid' || $invoice->status === 'paid';
        $payment = $salesOrder->payments->first();
        $customer = $salesOrder->customer;
        $items = $salesOrder->items;
        $carItem = $items->firstWhere('car_unit_id');
        $carUnit = $carItem?->carUnit ?? null;
    @endphp

    {{-- Official Invoice Paper View --}}
    <div class="panel border border-gunmetal bg-carbon p-6 md:p-10 space-y-6 relative overflow-hidden shadow-2xl">
        {{-- Corporate Letterhead --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gunmetal pb-6">
            <div class="flex items-center gap-3">
                <img src="{{ \App\Services\SettingService::getLogo() }}" alt="AUDrive" class="w-12 h-12 object-contain drop-shadow-[0_0_10px_rgba(245,196,0,0.3)]" />
                <div>
                    <h2 class="font-display text-xl md:text-2xl font-bold tracking-wider text-silver-bright">
                        AU<span class="text-giallo">DRIVE</span> MOTORS
                    </h2>
                    <p class="text-[11px] text-silver-dim">Luxury Dealership & Performance Workshop Indonesia</p>
                    <p class="text-[10px] text-silver-dim font-mono">SCBD Sudirman Kav. 88, Jakarta Selatan &bull; Hotline: +62 21 8088 9900</p>
                </div>
            </div>

            <div class="text-right">
                <span class="font-mono text-xs text-silver-dim block">DOKUMEN FAKTUR RESMI</span>
                <span class="font-mono text-sm md:text-base font-bold text-silver-bright">{{ $invoice->number }}</span>
                <div class="mt-2">
                    @if($isPaid)
                        <span class="inline-flex items-center px-3 py-1 rounded text-xs font-mono font-bold uppercase tracking-wider bg-emerald-950/60 text-emerald-400 border border-emerald-700/60">
                            ✓ LUNAS / VERIFIED PAID
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded text-xs font-mono font-bold uppercase tracking-wider bg-amber-950/60 text-amber-400 border border-amber-700/60">
                            MENUNGGU PEMBAYARAN
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Meta Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div class="bg-steel/15 border border-gunmetal/60 p-4 rounded space-y-1.5">
                <p class="font-heading text-[10px] uppercase tracking-wider text-giallo font-bold border-b border-gunmetal/40 pb-1">
                    Customer / Billed To
                </p>
                <p class="font-bold text-silver-bright text-sm">{{ $customer?->user?->name ?? 'Walk-in Customer' }}</p>
                <p class="text-silver-dim">Telepon: <span class="text-silver-bright font-mono">{{ $customer?->user?->phone ?? '-' }}</span></p>
                <p class="text-silver-dim">Email: <span class="text-silver-bright font-mono">{{ $customer?->user?->email ?? '-' }}</span></p>
                <p class="text-silver-dim">Alamat: <span class="text-silver-bright">{{ $customer?->address ?? 'Showroom Client' }}</span></p>
            </div>

            <div class="bg-steel/15 border border-gunmetal/60 p-4 rounded space-y-1.5">
                <p class="font-heading text-[10px] uppercase tracking-wider text-giallo font-bold border-b border-gunmetal/40 pb-1">
                    Detail Administrasi & Kasir
                </p>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <p class="text-silver-dim text-[11px]">No. Order Ref:</p>
                        <p class="font-mono text-silver-bright font-bold">#{{ $salesOrder->code }}</p>
                    </div>
                    <div>
                        <p class="text-silver-dim text-[11px]">Tanggal Terbit:</p>
                        <p class="font-mono text-silver-bright">{{ $salesOrder->created_at->format('d M Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-silver-dim text-[11px]">Tipe Transaksi:</p>
                        <p class="font-heading uppercase text-silver-bright font-bold">{{ str_replace('_', ' ', $salesOrder->type->value ?? (string)$salesOrder->type) }}</p>
                    </div>
                    <div>
                        <p class="text-silver-dim text-[11px]">Petugas Kasir:</p>
                        <p class="text-silver-bright font-semibold">{{ $salesOrder->creator?->name ?? 'Kasir POS' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Vehicle Information if any --}}
        @if($carUnit)
            <div class="bg-steel/20 border border-giallo/30 p-4 rounded text-xs flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">🏎</span>
                    <div>
                        <span class="text-[10px] font-heading uppercase tracking-wider text-giallo font-bold">Unit Supercar Terkait:</span>
                        <p class="font-display text-sm font-bold text-silver-bright">{{ $carUnit->brand }} {{ $carUnit->model }} ({{ $carUnit->year }})</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 text-xs font-mono">
                    <div>
                        <span class="text-silver-dim block text-[10px]">No. Polisi</span>
                        <span class="text-silver-bright font-bold">{{ $carUnit->plate_number ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-silver-dim block text-[10px]">VIN (No. Rangka)</span>
                        <span class="text-silver-bright">{{ $carUnit->vin ?? '-' }}</span>
                    </div>
                </div>
            </div>
        @endif

        {{-- Items Table --}}
        <div class="overflow-x-auto border border-gunmetal rounded">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-steel/40 text-silver-dim font-heading uppercase text-[10px] border-b border-gunmetal">
                        <th class="py-3 px-4 text-center w-12">No</th>
                        <th class="py-3 px-4">Deskripsi Item / Layanan</th>
                        <th class="py-3 px-4 text-center w-20">Kuantitas</th>
                        <th class="py-3 px-4 text-right">Harga Satuan</th>
                        <th class="py-3 px-4 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/30 font-mono">
                    @forelse($items as $idx => $item)
                        <tr class="hover:bg-steel/10 transition-premium">
                            <td class="py-3 px-4 text-center text-silver-dim">{{ $idx + 1 }}</td>
                            <td class="py-3 px-4 font-sans">
                                <p class="font-bold text-silver-bright">{{ $item->description ?: ($item->sparePart?->name ?? 'Item Transaksi') }}</p>
                                @if($item->sparePart)
                                    <p class="text-[10px] text-silver-dim font-mono">SKU / Part Number: {{ $item->sparePart->part_number ?? '-' }}</p>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center text-silver-bright">{{ $item->qty }}</td>
                            <td class="py-3 px-4 text-right text-silver-dim">Rp {{ number_format($item->unit_price_idr, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-bold text-silver-bright">Rp {{ number_format($item->subtotal_idr, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-silver-dim font-sans">Detail item transaksi tidak tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Totals & Payment Proof Box --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start pt-2">
            <div class="space-y-3">
                @if($isPaid && $payment)
                    <div class="panel border border-emerald-700/60 bg-emerald-950/20 p-4 rounded text-xs space-y-2">
                        <div class="flex items-center gap-2 text-emerald-400 font-heading font-bold uppercase tracking-wider text-xs">
                            <span>✓</span> Bukti Pelunasan Resmi Terverifikasi (Settled)
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <span class="text-silver-dim block text-[10px]">Metode Pembayaran:</span>
                                <span class="text-silver-bright font-bold uppercase">{{ $payment->method }}</span>
                            </div>
                            <div>
                                <span class="text-silver-dim block text-[10px]">Waktu Pelunasan:</span>
                                <span class="text-silver-bright font-mono">{{ ($payment->paid_at ?? $salesOrder->updated_at)->format('d M Y H:i') }} WIB</span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-silver-dim block text-[10px]">No. Referensi / Trx ID:</span>
                                <span class="text-giallo font-mono font-bold">{{ $payment->reference ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="panel border border-amber-700/60 bg-amber-950/20 p-4 rounded text-xs space-y-2">
                        <div class="text-amber-400 font-heading font-bold uppercase tracking-wider text-xs">
                            Status Tagihan: Belum Lunas
                        </div>
                        <p class="text-silver-dim text-[11px]">Silakan proses pembayaran melalui menu Point of Sales (POS).</p>
                    </div>
                @endif

                <div class="bg-steel/10 border border-gunmetal/60 p-3 rounded text-[11px] text-silver-dim italic">
                    <span class="font-bold text-silver-bright not-italic">Terbilang:</span>
                    "{{ \App\Support\Terbilang::make($salesOrder->total_idr) }}"
                </div>
            </div>

            <div class="panel border border-gunmetal bg-steel/20 p-5 rounded space-y-2 text-xs">
                <div class="flex justify-between text-silver-dim">
                    <span>Subtotal Tagihan:</span>
                    <span class="font-mono text-silver-bright">Rp {{ number_format($salesOrder->subtotal_idr, 0, ',', '.') }}</span>
                </div>
                @if($salesOrder->discount_idr > 0)
                    <div class="flex justify-between text-emerald-400">
                        <span>Potongan / Diskon:</span>
                        <span class="font-mono">- Rp {{ number_format($salesOrder->discount_idr, 0, ',', '.') }}</span>
                    </div>
                @endif
                @if($salesOrder->tax_idr > 0)
                    <div class="flex justify-between text-silver-dim">
                        <span>Pajak (PPN 11%):</span>
                        <span class="font-mono text-silver-bright">Rp {{ number_format($salesOrder->tax_idr, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-silver-bright text-base font-bold border-t border-gunmetal pt-3">
                    <span class="font-heading uppercase tracking-wide">Total Akhir:</span>
                    <span class="text-giallo font-mono text-lg">Rp {{ number_format($salesOrder->total_idr, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Legal / Footer Disclaimer --}}
        <div class="border-t border-gunmetal pt-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-[10px] text-silver-dim">
            <div>
                <p class="text-silver-bright font-bold">PT AUDRIVE SUPERCAR INDONESIA</p>
                <p>Dokumen ini adalah bukti transaksi sah yang diterbitkan melalui sistem otomasi dealer AUDrive.</p>
            </div>
            <div class="text-right">
                <span class="font-mono text-giallo font-bold">DIGITAL SIGNATURE & VERIFIED SEAL</span>
                <p>SCBD Sudirman Jakarta &bull; www.audrive.id</p>
            </div>
        </div>

        {{-- Official Signatures for Print Mode --}}
        <div class="hidden print:block pt-6 border-t border-gray-300">
            <table class="w-full text-center text-black text-xs">
                <tr>
                    <td style="width: 50%;">
                        <p class="text-[10px] font-semibold text-gray-600 mb-14">Diterbitkan Oleh (Finance / Kasir):</p>
                        <p class="font-bold underline">{{ $salesOrder->creator?->name ?? 'Kasir AUDrive' }}</p>
                        <p class="text-[9px] text-gray-500">PT AUDRIVE SUPERCAR INDONESIA</p>
                    </td>
                    <td style="width: 50%;">
                        <p class="text-[10px] font-semibold text-gray-600 mb-14">Diterima / Pelanggan:</p>
                        <p class="font-bold underline">{{ $customer?->user?->name ?? 'Pelanggan Terhormat' }}</p>
                        <p class="text-[9px] text-gray-500">Tanda Tangan & Nama Terang</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection

