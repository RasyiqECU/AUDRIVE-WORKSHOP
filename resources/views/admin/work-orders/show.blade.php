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
    $canQc = auth()->user()->can('qualityControl', $workOrder);
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
            @if($workOrder->is_warranty)
                <span class="text-xs text-verde font-heading uppercase">Klaim Garansi (Rp 0)</span>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <x-ui.status-pill :status="$statusStr" />

            {{-- Aksi sesuai tahap servis dan role --}}
            @php
                $isSuper = $role === 'super-admin';
                $hasPending = $workOrder->hasPendingParts();
                $statusButtons = [];
                if ($isMechanic && in_array($statusStr, ['queued', 'booking'], true)) {
                    $statusButtons[] = ['in_progress', 'Mulai Pengerjaan', 'bg-giallo hover:bg-arancio text-nero'];
                }
                if (($isMechanic || $isSuper) && $statusStr === 'in_progress' && ! $hasPending) {
                    $statusButtons[] = ['quality_control', 'Ajukan Quality Control', 'bg-arancio hover:bg-arancio/80 text-nero'];
                }
                if ($canQc && $statusStr === 'quality_control') {
                    $statusButtons[] = ['in_progress', 'Gagal QC, Kembalikan', 'bg-steel border border-danger/50 text-danger'];
                }
            @endphp

            @if($hasPending)
                <span class="px-3 py-2 bg-giallo/10 border border-giallo/30 text-giallo font-heading text-xs uppercase rounded">Menunggu Konfirmasi Part</span>
            @endif

            @foreach($statusButtons as [$target, $label, $class])
                <form action="{{ route('admin.work-orders.status', $workOrder) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="{{ $target }}">
                    <button type="submit" class="px-4 py-2 {{ $class }} font-heading font-bold text-xs uppercase tracking-wider rounded transition-premium">{{ $label }}</button>
                </form>
            @endforeach

            @if($canQc && $statusStr === 'quality_control')
                <form action="{{ route('admin.work-orders.complete', $workOrder) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-verde hover:bg-verde/80 text-nero font-heading font-bold text-xs uppercase tracking-wider rounded transition-premium">Lolos QC &amp; Selesaikan</button>
                </form>
            @endif

            @if($statusStr === 'completed' && ! $workOrder->paid_at)
                @if($isCashier || $isSuper)
                    <a href="{{ route('admin.pos.index', ['work_order_id' => $workOrder->id]) }}"
                       class="px-5 py-2.5 bg-giallo hover:bg-arancio text-nero font-heading font-bold text-xs uppercase tracking-wider rounded transition-premium">Proses Pembayaran Kasir (POS)</a>
                @else
                    <span class="px-3 py-2 border border-giallo/30 text-giallo font-heading text-xs uppercase rounded">Menunggu Pembayaran di Kasir</span>
                @endif
            @elseif($statusStr === 'completed')
                <span class="px-3 py-2 border border-verde/30 text-verde font-heading text-xs uppercase rounded">Lunas</span>
                @can('deliver', $workOrder)
                    <form action="{{ route('admin.work-orders.deliver', $workOrder) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright font-heading font-bold text-xs uppercase tracking-wider rounded transition-premium">Serahkan ke Pelanggan</button>
                    </form>
                @endcan
            @elseif($statusStr === 'delivered')
                <span class="px-3 py-2 border border-verde/30 text-verde font-heading text-xs uppercase rounded">Mobil Sudah Diambil</span>
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
            <h3 class="font-heading text-[11px] uppercase tracking-wider text-silver-dim">Kepala Mekanik yang Bertugas</h3>
            @if($workOrder->estimated_completion_at)
                <p class="text-silver-dim">Estimasi selesai: {{ $workOrder->estimated_completion_at->format('d M Y H:i') }}</p>
            @endif
            @if($workOrder->mechanic)
                <p class="font-bold text-silver-bright text-sm">👨‍🔧 {{ $workOrder->mechanic->name }}</p>
                <p class="font-mono text-silver-dim">{{ $workOrder->mechanic->phone ?? '-' }}</p>
            @else
                <p class="text-silver-dim italic">Belum ditugaskan ke kepala mekanik.</p>
            @endif

            @can('assignMechanic', $workOrder)
                @if(isset($mechanics) && $mechanics->isNotEmpty())
                    <form action="{{ route('admin.work-orders.assign', $workOrder) }}" method="POST" class="pt-2 border-t border-gunmetal/40 space-y-2">
                        @csrf
                        @method('PATCH')
                        <div class="flex items-center gap-2">
                            <select name="mechanic_id" required class="flex-1 bg-nero border border-gunmetal px-2 py-1 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                                <option value="">-- Pilih Kepala Mekanik --</option>
                                @foreach($mechanics as $m)
                                    <option value="{{ $m->id }}" {{ $workOrder->assigned_mechanic_id === $m->id ? 'selected' : '' }}>
                                        {{ $m->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="px-3 py-1 bg-giallo text-nero font-heading text-[10px] uppercase font-bold hover:bg-arancio transition rounded">
                                {{ $workOrder->mechanic ? 'Tugaskan Ulang' : 'Tugaskan' }}
                            </button>
                        </div>
                    </form>
                @endif
            @endcan

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
                                    <span class="px-2 py-0.5 text-[9px] uppercase font-bold rounded {{ $item->type === 'service' ? 'bg-giallo/10 text-giallo' : 'bg-verde/10 text-verde' }}">
                                        {{ $item->type }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 text-silver-bright font-sans">
                                    {{ $item->description ?: ($item->sparePart ? $item->sparePart->name : '-') }}
                                    @if($item->approval_status === 'pending')
                                        <span class="block text-[10px] text-giallo">Menunggu {{ $item->inventory_checked_at ? 'konfirmasi customer' : 'cek gudang' }} (belum ditagih)</span>
                                    @elseif($item->approval_status === 'rejected')
                                        <span class="block text-[10px] text-danger">Tidak diganti: {{ $item->decision_note }}</span>
                                    @elseif($item->part_source === 'customer')
                                        <span class="block text-[10px] text-silver-dim">Part bawaan customer, tanpa garansi part</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-3 text-center">{{ $item->qty }}</td>
                                <td class="py-2.5 px-3 text-right text-silver-dim">Rp {{ number_format($item->unit_price_idr, 0, ',', '.') }}</td>
                                <td class="py-2.5 px-3 text-right font-bold text-silver-bright">Rp {{ number_format($item->subtotal_idr, 0, ',', '.') }}</td>
                                <td class="py-2.5 px-3 text-right">
                                    @can('update', $workOrder)
                                        @if($statusStr !== 'delivered' && (! $item->part_source || $role === 'super-admin'))
                                            <form action="{{ route('admin.work-orders.items.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus item ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-danger hover:underline text-[10px] font-sans uppercase">Hapus</button>
                                            </form>
                                        @else
                                            <span class="text-silver-dim text-[10px] font-sans">-</span>
                                        @endif
                                    @else
                                        <span class="text-silver-dim text-[10px] font-sans">-</span>
                                    @endcan
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
                    @can('update', $workOrder)
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
                            <h4 class="font-heading text-xs uppercase tracking-wider text-verde font-semibold">+ Usulkan Suku Cadang</h4>
                            <form action="{{ route('admin.work-orders.part-items.store', $workOrder) }}" method="POST" class="space-y-3">
                                @csrf
                                <div>
                                    <select name="spare_part_id" required class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                                        <option value="">Pilih Spare Part...</option>
                                        @if(isset($spareParts))
                                            @foreach($spareParts as $part)
                                                <option value="{{ $part->id }}">
                                                    {{ $part->name }} (Rp {{ number_format($part->price_idr, 0, ',', '.') }}) — Stok: {{ $part->stock }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div>
                                    <input type="number" name="qty" required min="1" value="1" placeholder="Jumlah Qty" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                                </div>
                                <button type="submit" class="w-full py-2 bg-verde text-nero font-heading font-bold text-xs uppercase tracking-wider hover:bg-verde/80 transition">
                                    Kirim Usulan
                                </button>
                            </form>
                        </div>
                    @endcan

                    @if($role === 'super-admin')
                        {{-- Form 3: Status Change --}}
                        <div class="p-4 bg-steel/20 border border-gunmetal rounded-sm space-y-3">
                            <h4 class="font-heading text-xs uppercase tracking-wider text-silver-bright font-semibold">⚙ Ubah Status Work Order</h4>
                            <form action="{{ route('admin.work-orders.status', $workOrder) }}" method="POST" class="space-y-3">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                                    @foreach(\App\Enums\WorkOrderStatus::cases() as $sCase)
                                        <option value="{{ $sCase->value }}" {{ $statusStr === $sCase->value ? 'selected' : '' }}>
                                            {{ $sCase->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="w-full py-2 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright font-heading font-semibold text-xs uppercase tracking-wider transition">
                                    Perbarui Status
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
