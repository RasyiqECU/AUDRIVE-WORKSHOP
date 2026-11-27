@extends('layouts.admin')

@section('title', 'Detail Booking Servis')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.service-bookings.index') }}" class="inline-flex items-center gap-2 font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider transition-premium">
            <span>&larr;</span> Kembali ke Daftar Booking
        </a>
    </div>

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">
                DETAIL BOOKING SERVIS #{{ $serviceBooking->id }}
            </h1>
            <p class="text-xs text-silver-dim mt-1">
                Dibuat pada {{ $serviceBooking->created_at->format('d M Y H:i') }}
            </p>
        </div>
        <div>
            <x-ui.status-pill :status="$serviceBooking->status->value ?? $serviceBooking->status" />
        </div>
    </div>

    {{-- Booking Detail Panel --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Customer & Vehicle --}}
        <div class="panel border border-gunmetal bg-carbon p-5 space-y-4">
            <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright border-b border-gunmetal pb-2">
                Informasi Pelanggan & Kendaraan
            </h3>
            <dl class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <dt class="text-silver-dim">Nama Pelanggan:</dt>
                    <dd class="font-bold text-silver-bright">{{ $serviceBooking->customer->user->name ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-silver-dim">No. Telepon / WA:</dt>
                    <dd class="font-mono text-silver-bright">{{ $serviceBooking->customer->user->phone ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-silver-dim">Email:</dt>
                    <dd class="text-silver-bright">{{ $serviceBooking->customer->user->email ?? '-' }}</dd>
                </div>
                <div class="pt-2 border-t border-gunmetal/40 flex justify-between">
                    <dt class="text-silver-dim">Kendaraan:</dt>
                    <dd class="font-bold text-giallo">
                        @if($serviceBooking->vehicle)
                            {{ $serviceBooking->vehicle->year }} {{ $serviceBooking->vehicle->brand }} {{ $serviceBooking->vehicle->model }}
                        @else
                            -
                        @endif
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-silver-dim">Nomor Polisi:</dt>
                    <dd class="font-mono text-silver-bright">{{ $serviceBooking->vehicle->plate_number ?? '-' }}</dd>
                </div>
            </dl>
        </div>

        {{-- Service & Schedule --}}
        <div class="panel border border-gunmetal bg-carbon p-5 space-y-4">
            <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright border-b border-gunmetal pb-2">
                Jadwal & Layanan
            </h3>
            <dl class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <dt class="text-silver-dim">Jenis Layanan:</dt>
                    <dd class="font-heading font-semibold text-silver-bright uppercase">{{ ucfirst(str_replace('_', ' ', $serviceBooking->service_type)) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-silver-dim">Tanggal Booking:</dt>
                    <dd class="font-mono font-bold text-silver-bright">{{ $serviceBooking->preferred_date ? $serviceBooking->preferred_date->format('d M Y') : '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-silver-dim">Waktu Pilihan:</dt>
                    <dd class="font-mono text-silver-bright">{{ $serviceBooking->preferred_time ?? '-' }}</dd>
                </div>
                <div class="pt-2 border-t border-gunmetal/40">
                    <dt class="text-silver-dim mb-1">Catatan Keluhan:</dt>
                    <dd class="bg-nero/50 p-2.5 border border-gunmetal text-silver italic">
                        "{{ $serviceBooking->notes ?: 'Tidak ada catatan khusus.' }}"
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- Check-in (Sales) → Work Order (Kepala Mekanik) --}}
    @php
        $viewerRole = auth()->user()->role ?? '';
        $cStatus = $serviceBooking->status->value ?? $serviceBooking->status;
        $awaiting = in_array($cStatus, ['pending', 'confirmed'], true) && ! $serviceBooking->workOrder;
        $field = 'w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none';
    @endphp
    <div class="panel border border-gunmetal bg-carbon p-6 space-y-4">
        <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright">Check-in &amp; Work Order</h3>

        @if($serviceBooking->checked_in_at)
            <div class="text-xs text-silver space-y-1">
                <p class="text-verde">Check-in {{ $serviceBooking->checked_in_at->format('d M Y H:i') }}</p>
                <p>Kilometer: {{ number_format($serviceBooking->odometer_km, 0, ',', '.') }} km</p>
                <p>Kondisi bodi: {{ $serviceBooking->body_condition }}</p>
                <p>Barang berharga: {{ $serviceBooking->belongings ?: '-' }}</p>
            </div>
        @endif

        @if($serviceBooking->workOrder)
            @can('view', $serviceBooking->workOrder)
            <a href="{{ route('admin.work-orders.show', $serviceBooking->workOrder) }}" class="inline-block px-3 py-1.5 bg-giallo text-nero font-heading text-xs font-bold uppercase rounded">
                Lihat WO #{{ $serviceBooking->workOrder->code }}
            </a>
            @else
                <p class="text-xs text-silver-dim">Work Order {{ $serviceBooking->workOrder->code }} ditangani kepala mekanik lain.</p>
            @endcan
        @elseif($awaiting && in_array($viewerRole, ['super-admin', 'sales'], true))
            <form action="{{ route('admin.service-bookings.check-in', $serviceBooking) }}" method="POST" class="space-y-3 text-xs text-silver-dim">
                @csrf
                <label class="block">Kilometer saat ini<input type="number" name="odometer_km" min="0" required value="{{ old('odometer_km') }}" class="{{ $field }}"></label>
                <label class="block">Kondisi bodi / baret<textarea name="body_condition" rows="2" required class="{{ $field }}">{{ old('body_condition') }}</textarea></label>
                <label class="block">Barang berharga di dalam mobil<textarea name="belongings" rows="2" class="{{ $field }}">{{ old('belongings') }}</textarea></label>
                <button type="submit" class="px-5 py-2 bg-giallo text-nero font-heading font-bold uppercase rounded">Check-in</button>
            </form>
        @elseif($cStatus === 'checked_in' && in_array($viewerRole, ['super-admin', 'mechanic'], true))
            <form action="{{ route('admin.service-bookings.convert', $serviceBooking) }}" method="POST" class="grid md:grid-cols-3 gap-3 items-end text-xs text-silver-dim">
                @csrf
                <label>Kepala Mekanik
                    <select name="mechanic_id" required class="{{ $field }}">
                        <option value="">Pilih kepala mekanik...</option>
                        @foreach($mechanics as $mechanic)
                            <option value="{{ $mechanic->id }}">{{ $mechanic->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Estimasi selesai<input type="datetime-local" name="estimated_completion_at" required class="{{ $field }}"></label>
                <button type="submit" class="px-5 py-2 bg-giallo text-nero font-heading font-bold uppercase rounded">Buat Work Order</button>
            </form>
        @else
            <p class="text-xs text-silver-dim italic">
                {{ $cStatus === 'checked_in' ? 'Menunggu Kepala Mekanik membuat Work Order.' : 'Menunggu kedatangan dan check-in oleh Sales.' }}
            </p>
        @endif

        @if(! $serviceBooking->workOrder && in_array($viewerRole, ['super-admin', 'cashier', 'sales'], true))
            <form action="{{ route('admin.service-bookings.status', $serviceBooking) }}" method="POST" class="flex items-center justify-end gap-2 pt-3 border-t border-gunmetal/40">
                @csrf
                @method('PATCH')
                <select name="status" class="bg-nero border border-gunmetal px-3 py-1.5 text-xs text-silver-bright">
                    <option value="pending" @selected($cStatus === 'pending')>Pending</option>
                    <option value="confirmed" @selected($cStatus === 'confirmed')>Confirmed</option>
                    <option value="cancelled" @selected($cStatus === 'cancelled')>Cancelled</option>
                </select>
                <button type="submit" class="px-3 py-1.5 bg-steel border border-gunmetal text-silver-bright font-heading text-xs uppercase rounded">Update Status</button>
            </form>
        @endif
    </div>
</div>
@endsection
