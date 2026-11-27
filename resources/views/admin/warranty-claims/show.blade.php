@extends('layouts.admin')

@section('title', 'Klaim ' . $claim->code)

@section('content')
@php
    $role = auth()->user()->role;
    $field = 'w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none';
    $button = 'px-4 py-2 bg-giallo text-nero font-heading font-bold text-xs uppercase rounded';
    $repair = $claim->repairWorkOrder;
@endphp
<div class="space-y-6 max-w-3xl mx-auto">
    <a href="{{ route('admin.warranty-claims.index') }}" class="font-heading text-xs text-giallo uppercase tracking-wider">&larr; Daftar Klaim</a>
    <div class="flex flex-wrap justify-between gap-3">
        <h1 class="font-display text-2xl font-bold text-silver-bright">{{ $claim->code }}</h1>
        <span class="text-xs text-silver-bright">{{ $claim->statusLabel() }}</span>
    </div>

    <div class="panel border border-gunmetal bg-carbon p-5 text-xs text-silver space-y-1">
        <p>Customer: {{ $claim->customer?->user?->name }} ({{ $claim->customer?->user?->phone }})</p>
        <p>Kendaraan: {{ $claim->vehicle?->brand }} {{ $claim->vehicle?->model }} {{ $claim->vehicle?->plate_number }}</p>
        <p>Sumber: {{ $claim->sourceItem ? $claim->sourceItem->workOrder?->code . ' — ' . $claim->sourceItem->description : 'Pembelian mobil ' . $claim->sourceOrder?->code }}</p>
        <p>Serah terima: {{ $claim->reference_date->format('d M Y') }} &bull; {{ number_format($claim->reference_km, 0, ',', '.') }} km &rarr; sekarang {{ number_format($claim->current_km, 0, ',', '.') }} km</p>
        <p>Keluhan: {{ $claim->complaint }}</p>
        @if($claim->diagnosis)
            <p class="pt-2 border-t border-gunmetal/40">Diagnosa {{ $claim->diagnoser?->name }}: {{ $claim->diagnosis }}</p>
            <p class="{{ $claim->is_covered ? 'text-verde' : 'text-danger' }}">
                {{ $claim->is_covered ? 'Cacat produk / kesalahan pemasangan' : 'Bukan tanggungan garansi (pemakaian)' }}
            </p>
        @endif
        @if($claim->rejection_reason)
            <p class="text-danger">Alasan ditolak: {{ $claim->rejection_reason }}</p>
        @endif
    </div>

    @if($claim->status === 'submitted' && in_array($role, ['super-admin', 'mechanic'], true))
        <form method="POST" action="{{ route('admin.warranty-claims.diagnose', $claim) }}" class="panel border border-gunmetal bg-carbon p-5 space-y-3 text-xs">
            @csrf
            <select name="is_covered" required class="{{ $field }}">
                <option value="">Hasil pengecekan...</option>
                <option value="1">Cacat produk / kesalahan pemasangan</option>
                <option value="0">Kelalaian pemakaian (bukan garansi)</option>
            </select>
            <textarea name="diagnosis" rows="3" required placeholder="Hasil diagnosa" class="{{ $field }}"></textarea>
            <button type="submit" class="{{ $button }}">Simpan Diagnosa</button>
        </form>
    @endif

    @if($claim->status === 'diagnosed' && in_array($role, ['super-admin', 'manager'], true))
        <form method="POST" action="{{ route('admin.warranty-claims.decide', $claim) }}" class="panel border border-gunmetal bg-carbon p-5 space-y-3 text-xs">
            @csrf
            <input type="text" name="rejection_reason" placeholder="Alasan (wajib bila ditolak)" class="{{ $field }}">
            <button type="submit" name="decision" value="approve" class="{{ $button }}">Approve Klaim</button>
            <button type="submit" name="decision" value="reject" class="px-4 py-2 border border-danger/50 text-danger font-heading font-bold text-xs uppercase rounded">Tolak</button>
        </form>
    @endif

    @if($claim->status === 'rejected' && in_array($role, ['super-admin', 'sales'], true))
        <a href="{{ route('admin.service-bookings.create') }}" class="inline-block {{ $button }}">Lanjutkan sebagai Servis Berbayar</a>
    @endif

    @if($repair)
        <div class="panel border border-gunmetal bg-carbon p-5 space-y-3 text-xs">
            <div class="flex justify-between">
                <span class="text-silver-bright">Perbaikan {{ $repair->code }}</span>
                <x-ui.status-pill :status="$repair->status->value" />
            </div>
            @foreach($repair->items as $item)
                <p class="text-silver-dim">{{ $item->description }} x{{ $item->qty }} — Rp 0</p>
            @endforeach
            @if(in_array($role, ['super-admin', 'cashier', 'sales', 'mechanic'], true))
                <a href="{{ route('admin.work-orders.show', $repair) }}" class="text-giallo underline">Buka Work Order</a>
            @endif

            @if(in_array($role, ['super-admin', 'inventory'], true) && in_array($repair->status->value, ['queued', 'in_progress'], true))
                <form method="POST" action="{{ route('admin.warranty-claims.parts', $claim) }}" class="flex flex-wrap gap-2 pt-3 border-t border-gunmetal/40">
                    @csrf
                    <select name="spare_part_id" required class="flex-1 bg-nero border border-gunmetal px-2 py-2 text-silver-bright">
                        <option value="">Suku cadang pengganti...</option>
                        @foreach($spareParts as $part)
                            <option value="{{ $part->id }}">{{ $part->name }} (stok {{ $part->available_stock }})</option>
                        @endforeach
                    </select>
                    <input type="number" name="qty" value="1" min="1" required class="w-20 bg-nero border border-gunmetal px-2 py-2 text-silver-bright">
                    <button type="submit" class="{{ $button }}">Keluarkan (Garansi)</button>
                </form>
            @endif
        </div>
    @endif
</div>
@endsection
