@extends('layouts.admin')

@section('title', 'Work Order ' . $workOrder->code)

@section('content')
@php
    $statusStr = $workOrder->status instanceof \App\Enums\WorkOrderStatus 
        ? $workOrder->status->value 
        : (string) $workOrder->status;
    $role = auth()->user()->role;
    $isMechanic = ($role === 'mechanic');
    $isCashier = ($role === 'cashier');
    $isAdminOrManager = in_array($role, ['super-admin', 'manager'], true);
@endphp

<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.work-orders.index') }}" class="font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider">&larr; Daftar Work Order</a>
                <span class="text-silver-dim text-xs">/</span>
                <span class="font-mono text-xs text-silver-dim">{{ $workOrder->code }}</span>
            </div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">
                WORK ORDER: {{ $workOrder->code }}
            </h1>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <x-ui.status-pill :status="$statusStr" />

            {{-- Action buttons for MECHANIC --}}
            @if($isMechanic)
                @if($statusStr === 'queued' || $statusStr === 'booking')
                    <form action="{{ route('admin.work-orders.status', $workOrder) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="in_progress">
                        <button type="submit" class="px-4 py-2 bg-giallo hover:bg-arancio text-nero font-heading font-bold text-xs uppercase tracking-wider rounded transition-premium">
                            ▶ Mulai Pengerjaan
                        </button>
                    </form>
                @elseif($statusStr === 'in_progress')
                    <form action="{{ route('admin.work-orders.status', $workOrder) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="quality_control">
                        <button type="submit" class="px-4 py-2 bg-arancio hover:bg-arancio/80 text-nero font-heading font-bold text-xs uppercase tracking-wider rounded transition-premium">
                            🔍 Ajukan Quality Control
                        </button>
                    </form>
                    <form action="{{ route('admin.work-orders.complete', $workOrder) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-verde hover:bg-verde/80 text-nero font-heading font-bold text-xs uppercase tracking-wider rounded transition-premium">
                            ✓ Selesaikan Pengerjaan
                        </button>
                    </form>
                @elseif($statusStr === 'quality_control')
                    <form action="{{ route('admin.work-orders.complete', $workOrder) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-verde hover:bg-verde/80 text-nero font-heading font-bold text-xs uppercase tracking-wider rounded transition-premium">
                            ✓ Lolos QC & Selesaikan Servis
                        </button>
                    </form>
                @elseif($statusStr === 'completed')
                    <div class="px-3.5 py-2 bg-giallo/10 border border-giallo/30 text-giallo font-heading text-xs uppercase tracking-wider rounded flex items-center gap-2">
                        <span>⏳</span> Servis Selesai &bull; Notifikasi Dikirim ke Kasir untuk Pembayaran
                    </div>
                @elseif($statusStr === 'delivered')
                    <div class="px-3.5 py-2 bg-verde/10 border border-verde/30 text-verde font-heading text-xs uppercase tracking-wider rounded flex items-center gap-2">
                        <span>✓</span> Pembayaran Lunas & Unit Telah Diserahkan
                    </div>
                @endif
            @endif

            {{-- Action buttons for CASHIER --}}
            @if($isCashier)
                @if($statusStr !== 'delivered' && $workOrder->total_idr > 0)
                    <a href="{{ route('admin.pos.index', ['work_order_id' => $workOrder->id]) }}"
                       class="px-5 py-2.5 bg-giallo hover:bg-arancio text-nero font-heading font-bold text-xs uppercase tracking-wider rounded shadow-lg shadow-giallo/20 transition-premium flex items-center gap-2">
                        💳 Proses Pembayaran Kasir (POS)
                    </a>
                @elseif($statusStr === 'delivered')
                    <div class="px-3.5 py-2 bg-verde/10 border border-verde/30 text-verde font-heading text-xs uppercase tracking-wider rounded flex items-center gap-2">
                        <span>✓</span> Lunas & Unit Diserahkan
                    </div>
                @endif
            @endif

            {{-- Action buttons for SUPER ADMIN & MANAGER --}}
            @if($isAdminOrManager)
                @if($statusStr !== 'delivered' && $workOrder->total_idr > 0)
                    <a href="{{ route('admin.pos.index', ['work_order_id' => $workOrder->id]) }}"
                       class="px-4 py-2 bg-giallo hover:bg-arancio text-nero font-heading font-bold text-xs uppercase tracking-wider rounded shadow-lg shadow-giallo/20 transition-premium flex items-center gap-1.5">
                        💳 Bayar di Kasir (POS)
                    </a>
                @endif

                @if($statusStr === 'in_progress')
                    <form action="{{ route('admin.work-orders.complete', $workOrder) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-verde hover:bg-verde/80 text-nero font-heading font-bold text-xs uppercase tracking-wider rounded transition-premium">
                            ✓ Selesaikan Pengerjaan
                        </button>
                    </form>
                @elseif($statusStr === 'completed')
                    <form action="{{ route('admin.work-orders.deliver', $workOrder) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright font-heading font-bold text-xs uppercase tracking-wider rounded transition-premium">
                            🏎 Serahkan ke Pelanggan (Manual)
                        </button>
                    </form>
                @endif
            @endif
        </div>
    </div>

    {{-- Info Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="panel border border-gunmetal bg-carbon p-5 space-y-2 text-xs">
            <h3 class="font-heading text-[11px] uppercase tracking-wider text-silver-dim">Customer & Kendaraan</h3>
            <p class="font-bold text-silver-bright text-sm">{{ $workOrder->vehicle?->customer?->user?->name ?? 'Customer' }}</p>
            <p class="font-mono text-silver-dim">{{ $workOrder->vehicle?->customer?->user?->phone ?? '-' }}</p>
            <div class="pt-2 border-t border-gunmetal/40">
                <p class="font-bold text-giallo">{{ $workOrder->vehicle ? "{$workOrder->vehicle->brand} {$workOrder->vehicle->model} ({$workOrder->vehicle->year})" : '-' }}</p>
                <p class="font-mono text-silver-dim">{{ $workOrder->vehicle?->plate_number ?: 'Tanpa Plat' }}</p>
            </div>
        </div>

        <div class="panel border border-gunmetal bg-carbon p-5 space-y-2 text-xs">
            <h3 class="font-heading text-[11px] uppercase tracking-wider text-silver-dim">Mekanik yang Bertugas</h3>
            @if($workOrder->mechanic)
                <p class="font-bold text-silver-bright text-sm">👨‍🔧 {{ $workOrder->mechanic->name }}</p>
                <p class="font-mono text-silver-dim">{{ $workOrder->mechanic->phone ?? '-' }}</p>
            @else
                <p class="text-silver-dim italic">Belum ditugaskan ke mekanik.</p>
            @endif
            <div class="pt-2 border-t border-gunmetal/40 font-mono text-[10px] text-silver-dim">
                <p>Mulai: {{ $workOrder->started_at ? $workOrder->started_at->format('d M Y H:i') : '-' }}</p>
                <p>Selesai: {{ $workOrder->completed_at ? $workOrder->completed_at->format('d M Y H:i') : '-' }}</p>
            </div>
        </div>

        <div class="panel border border-gunmetal bg-carbon p-5 space-y-2 text-xs">
            <h3 class="font-heading text-[11px] uppercase tracking-wider text-silver-dim">Ringkasan Biaya</h3>
            <div class="space-y-1 font-mono">
                <div class="flex justify-between text-silver-dim">
                    <span>Biaya Jasa:</span>
                    <span>Rp {{ number_format($workOrder->total_jasa_idr, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-silver-dim">
                    <span>Suku Cadang:</span>
                    <span>Rp {{ number_format($workOrder->total_part_idr, 0, ',', '.') }}</span>
                </div>
                <div class="pt-2 border-t border-gunmetal flex justify-between font-bold text-giallo text-sm">
                    <span>Total IDR:</span>
                    <span>Rp {{ number_format($workOrder->total_idr, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Items List (Jasa & Spare Parts) --}}
    <div class="panel border border-gunmetal bg-carbon overflow-hidden">
        <div class="bg-steel/30 px-6 py-4 border-b border-gunmetal flex items-center justify-between">
            <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright">
                Rincian Item Pekerjaan & Suku Cadang
            </h3>
        </div>

        <div class="p-6 space-y-6">
            {{-- Items Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gunmetal text-[10px] font-heading uppercase tracking-wider text-silver-dim">
                            <th class="py-2 px-3">Tipe</th>
                            <th class="py-2 px-3">Deskripsi / Item</th>
                            <th class="py-2 px-3 text-center">Qty</th>
                            <th class="py-2 px-3 text-right">Harga Satuan</th>
                            <th class="py-2 px-3 text-right">Subtotal</th>
                            <th class="py-2 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gunmetal/30 text-xs font-mono">
                        @forelse($workOrder->items as $item)
                            <tr class="hover:bg-steel/10">
                                <td class="py-2.5 px-3">
                                    <span class="px-2 py-0.5 text-[9px] uppercase font-bold rounded {{ $item->item_type === 'service' ? 'bg-giallo/10 text-giallo' : 'bg-verde/10 text-verde' }}">
                                        {{ $item->item_type }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 text-silver-bright font-sans">
                                    {{ $item->description ?: ($item->sparePart ? $item->sparePart->name : '-') }}
                                </td>
                                <td class="py-2.5 px-3 text-center">{{ $item->qty }}</td>
                                <td class="py-2.5 px-3 text-right text-silver-dim">Rp {{ number_format($item->unit_price_idr, 0, ',', '.') }}</td>
                                <td class="py-2.5 px-3 text-right font-bold text-silver-bright">Rp {{ number_format($item->subtotal_idr, 0, ',', '.') }}</td>
                                <td class="py-2.5 px-3 text-right">
                                    @if($statusStr !== 'delivered')
                                        <form action="{{ route('admin.work-orders.items.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus item ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-danger hover:underline text-[10px] font-sans uppercase">Hapus</button>
                                        </form>
                                    @else
                                        <span class="text-silver-dim text-[10px] font-sans">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-silver-dim font-sans text-xs italic">
                                    Belum ada item jasa atau spare part yang ditambahkan ke Work Order ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Action Forms (Service, Parts, Status) --}}
            @if($statusStr !== 'delivered')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-gunmetal">
                    {{-- Form 1: Add Service Item --}}
                    <div class="p-4 bg-steel/20 border border-gunmetal rounded-sm space-y-3">
                        <h4 class="font-heading text-xs uppercase tracking-wider text-giallo font-semibold">+ Tambah Jasa Servis</h4>
                        <form action="{{ route('admin.work-orders.service-items.store', $workOrder) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <input type="text" name="description" required placeholder="Deskripsi Jasa (cth: Tune-up)" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" name="qty" required min="1" value="1" placeholder="Qty" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                                <input type="number" name="unit_price_idr" required min="0" placeholder="Biaya IDR" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                            </div>
                            <button type="submit" class="w-full py-2 bg-giallo text-nero font-heading font-bold text-xs uppercase tracking-wider hover:bg-arancio transition">
                                Tambah Jasa
                            </button>
                        </form>
                    </div>

                    {{-- Form 2: Add Spare Part Item --}}
                    <div class="p-4 bg-steel/20 border border-gunmetal rounded-sm space-y-3">
                        <h4 class="font-heading text-xs uppercase tracking-wider text-verde font-semibold">+ Tambah Suku Cadang</h4>
                        <form action="{{ route('admin.work-orders.part-items.store', $workOrder) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <select name="spare_part_id" required class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                                    <option value="">Pilih Spare Part...</option>
                                    @if(isset($spareParts))
                                        @foreach($spareParts as $part)
                                            <option value="{{ $part->id }}">
                                                {{ $part->name }} (Rp {{ number_format($part->price_idr, 0, ',', '.') }}) — Stok: {{ $part->stock_qty }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div>
                                <input type="number" name="qty" required min="1" value="1" placeholder="Jumlah Qty" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                            </div>
                            <button type="submit" class="w-full py-2 bg-verde text-nero font-heading font-bold text-xs uppercase tracking-wider hover:bg-verde/80 transition">
                                Tambah Spare Part
                            </button>
                        </form>
                    </div>

                    {{-- Form 3: Status Change --}}
                    <div class="p-4 bg-steel/20 border border-gunmetal rounded-sm space-y-3">
                        <h4 class="font-heading text-xs uppercase tracking-wider text-silver-bright font-semibold">⚙ Ubah Status Work Order</h4>
                        <form action="{{ route('admin.work-orders.status', $workOrder) }}" method="POST" class="space-y-3">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                                @foreach(\App\Enums\WorkOrderStatus::cases() as $sCase)
                                    @if($isMechanic && $sCase->value === 'delivered')
                                        {{-- Mekanik tidak boleh memilih status diserahkan/delivered secara langsung --}}
                                        @continue
                                    @endif
                                    <option value="{{ $sCase->value }}" {{ $statusStr === $sCase->value ? 'selected' : '' }}>
                                        {{ $sCase->label() }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="w-full py-2 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright font-heading font-semibold text-xs uppercase tracking-wider transition">
                                Perbarui Status
                            </button>
                        </form>
                        @if($isMechanic)
                            <p class="text-[10px] text-silver-dim italic">
                                * Catatan: Status Diserahkan (Delivered) akan otomatis terbarui saat pembayaran diselesaikan oleh Kasir.
                            </p>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
