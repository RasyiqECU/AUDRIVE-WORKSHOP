@extends(auth()->check() && auth()->user()->role === 'customer' ? 'layouts.customer' : (auth()->check() ? 'layouts.app' : 'layouts.public'))

@section('title', 'Detail Booking Servis #' . $booking->id . ' — AUDrive')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <a href="{{ route('workshop.booking.index') }}" class="text-xs text-giallo hover:underline uppercase tracking-wider font-heading">
                &larr; Kembali ke Daftar Booking
            </a>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright mt-2">
                BOOKING SERVIS #{{ str_pad((string)$booking->id, 5, '0', STR_PAD_LEFT) }}
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 text-xs font-semibold rounded uppercase tracking-wider bg-giallo/20 text-giallo border border-giallo/40">
                Status: {{ $booking->status->value ?? $booking->status }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            {{-- Info Servis --}}
            <div class="bg-nero-dark border border-white/10 rounded-lg p-6">
                <h3 class="font-heading text-sm font-bold text-silver-bright uppercase tracking-wider mb-4 border-b border-white/5 pb-2">
                    Informasi Layanan Servis
                </h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <dt class="text-silver-dim">Jenis Layanan</dt>
                        <dd class="text-silver-bright font-semibold mt-0.5 uppercase">{{ str_replace('_', ' ', $booking->service_type) }}</dd>
                    </div>
                    <div>
                        <dt class="text-silver-dim">Jadwal Kedatangan</dt>
                        <dd class="text-silver-bright font-semibold mt-0.5">
                            {{ $booking->preferred_date?->format('l, d F Y') }} pk {{ $booking->preferred_time }} WIB
                        </dd>
                    </div>
                    <div>
                        <dt class="text-silver-dim">Estimasi Biaya</dt>
                        <dd class="text-giallo font-semibold mt-0.5">
                            {{ $booking->estimated_cost_idr ? 'Rp ' . number_format($booking->estimated_cost_idr, 0, ',', '.') : 'Menunggu Pengecekan' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-silver-dim">Dibuat Pada</dt>
                        <dd class="text-silver-bright mt-0.5">{{ $booking->created_at?->format('d/m/Y H:i') }}</dd>
                    </div>
                </dl>

                @if($booking->notes)
                    <div class="mt-4 pt-4 border-t border-white/5">
                        <p class="text-xs text-silver-dim">Catatan / Keluhan Pengguna:</p>
                        <p class="text-xs text-silver-bright mt-1 italic bg-nero p-3 rounded border border-white/5">
                            "{{ $booking->notes }}"
                        </p>
                    </div>
                @endif
            </div>

            {{-- Status Work Order --}}
            @if($booking->workOrder)
                <div class="bg-nero-dark border border-white/10 rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4 border-b border-white/5 pb-2">
                        <h3 class="font-heading text-sm font-bold text-silver-bright uppercase tracking-wider">
                            Work Order (WO) Aktif
                        </h3>
                        <a href="{{ route('workshop.tracking.show', $booking->workOrder) }}" class="text-xs text-giallo hover:underline">
                            Lacak Realtime &rarr;
                        </a>
                    </div>
                    <p class="text-xs text-silver-dim">
                        Nomor WO: <span class="text-silver-bright font-mono font-semibold">{{ $booking->workOrder->code }}</span>
                    </p>
                    <p class="text-xs text-silver-dim mt-1">
                        Status Pengerjaan: <span class="font-semibold {{ ($booking->workOrder->status->value ?? $booking->workOrder->status) === 'delivered' ? 'text-green-400' : 'text-giallo' }}">{{ $booking->workOrder->status instanceof \App\Enums\WorkOrderStatus ? $booking->workOrder->status->label() : ucfirst(str_replace('_', ' ', $booking->workOrder->status)) }}</span>
                    </p>
                </div>
            @endif
        </div>

        {{-- Info Kendaraan --}}
        <div>
            <div class="bg-nero-dark border border-white/10 rounded-lg p-6">
                <h3 class="font-heading text-sm font-bold text-silver-bright uppercase tracking-wider mb-4 border-b border-white/5 pb-2">
                    Kendaraan Servis
                </h3>
                <div class="text-xs space-y-3">
                    <div>
                        <span class="text-silver-dim block">Nomor Polisi</span>
                        <span class="text-silver-bright font-mono font-bold text-sm">{{ $booking->vehicle?->license_plate ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-silver-dim block">Tipe Kendaraan</span>
                        <span class="text-silver-bright font-semibold">{{ $booking->vehicle?->brand }} {{ $booking->vehicle?->model }} ({{ $booking->vehicle?->year }})</span>
                    </div>
                    <div>
                        <span class="text-silver-dim block">Nomor Rangka (VIN)</span>
                        <span class="text-silver-bright font-mono">{{ $booking->vehicle?->vin ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
