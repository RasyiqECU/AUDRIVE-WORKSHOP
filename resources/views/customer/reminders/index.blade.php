@extends('layouts.customer')

@section('title', 'Jadwal & Pengingat Servis')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Maintenance Alert</p>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                PENGINGAT & JADWAL SERVIS
            </h1>
            <p class="text-xs text-silver-dim mt-1">
                Pantau rekomendasi perawatan berkala dan riwayat kendaraan Anda agar selalu dalam performa optimal.
            </p>
        </div>
        <a href="{{ route('workshop.booking') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-giallo hover:bg-giallo-bright text-carbon font-heading text-xs uppercase tracking-wider font-bold rounded clip-angular-button transition-premium shrink-0">
            🔧 Booking Servis Baru
        </a>
    </div>

    {{-- Alert Reminders list --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($vehicles as $vehicle)
            <div class="panel border border-gunmetal/70 bg-carbon p-5 space-y-4 relative overflow-hidden">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="inline-block px-2 py-0.5 text-[10px] font-heading uppercase tracking-wider bg-giallo/10 border border-giallo/30 text-giallo rounded">
                            {{ $vehicle->license_plate }}
                        </span>
                        <h3 class="font-display text-lg text-silver-bright font-bold mt-2">
                            {{ $vehicle->brand }} {{ $vehicle->model }}
                        </h3>
                        <p class="text-xs text-silver-dim">Tahun: {{ $vehicle->year ?? '-' }} | Odometer: {{ number_format($vehicle->current_mileage ?? 0) }} km</p>
                    </div>
                    <span class="text-3xl opacity-50">🚗</span>
                </div>

                <div class="border-t border-gunmetal/50 pt-3 space-y-2 text-xs">
                    <div class="flex justify-between text-silver-muted">
                        <span>Servis Terakhir:</span>
                        <span class="text-silver-bright font-mono">{{ $vehicle->serviceBookings->where('status', 'completed')->last()?->scheduled_date?->format('d M Y') ?? 'Belum ada data' }}</span>
                    </div>
                    <div class="flex justify-between text-silver-muted">
                        <span>Estimasi Servis Rutin:</span>
                        <span class="text-giallo font-mono font-semibold">Tiap 6 Bulan / 10.000 KM</span>
                    </div>
                </div>

                <div class="pt-2 flex items-center justify-between">
                    <form action="{{ route('customer.reminders.dismiss', $vehicle->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="text-[11px] text-silver-dim hover:text-silver-bright underline">
                            Abaikan Notifikasi
                        </button>
                    </form>
                    <a href="{{ route('workshop.booking') }}?vehicle_id={{ $vehicle->id }}" class="px-3 py-1.5 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright text-xs font-heading uppercase tracking-wider rounded">
                        Jadwalkan Servis
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full panel border border-gunmetal/70 bg-carbon p-8 text-center text-silver-dim">
                <span class="text-4xl block mb-2">🚗</span>
                <p class="font-heading text-sm text-silver-bright uppercase tracking-wider">Belum Ada Kendaraan Terdaftar</p>
                <p class="text-xs mt-1 mb-4">Daftarkan mobil Anda untuk mendapatkan pengingat servis otomatis.</p>
                <a href="{{ route('customer.vehicles.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-giallo text-carbon font-heading text-xs uppercase font-bold rounded">
                    + Tambah Kendaraan
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection
