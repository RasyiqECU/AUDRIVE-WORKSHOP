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

    {{-- Actions & Work Order Conversion --}}
    <div class="panel border border-gunmetal bg-carbon p-6 space-y-4">
        <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright">
            Tindakan & Konversi Work Order
        </h3>

        <div class="flex flex-wrap items-center gap-4">
            @if($serviceBooking->workOrder)
                <div class="flex items-center gap-2">
                    <span class="text-xs text-verde">✓ Sudah dikonversi ke Work Order:</span>
                    <a href="{{ route('admin.work-orders.show', $serviceBooking->workOrder) }}" class="px-3 py-1.5 bg-giallo text-nero font-heading text-xs font-bold uppercase rounded">
                        Lihat WO #{{ $serviceBooking->workOrder->code }}
                    </a>
                </div>
            @else
                <form action="{{ route('admin.service-bookings.convert', $serviceBooking) }}" method="POST" class="flex flex-wrap items-center gap-3">
                    @csrf
                    <button type="submit" class="px-5 py-2.5 bg-giallo hover:bg-arancio text-nero font-heading font-bold text-xs uppercase tracking-wider rounded shadow-lg shadow-giallo/20 transition-premium">
                        🔧 Konversi ke Work Order (Mulai Pengerjaan)
                    </button>
                </form>
            @endif

            {{-- Update Status --}}
            <form action="{{ route('admin.service-bookings.status', $serviceBooking) }}" method="POST" class="flex items-center gap-2 ml-auto">
                @csrf
                @method('PATCH')
                <select name="status" class="bg-nero border border-gunmetal px-3 py-1.5 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                    @php $cStatus = $serviceBooking->status->value ?? $serviceBooking->status; @endphp
                    <option value="pending" {{ $cStatus === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ $cStatus === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="in_progress" {{ $cStatus === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ $cStatus === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $cStatus === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <button type="submit" class="px-3 py-1.5 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright font-heading text-xs uppercase rounded transition-premium">
                    Update Status
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
