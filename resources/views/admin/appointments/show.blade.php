@extends('layouts.admin')

@section('title', 'Detail Jadwal Test Drive')

@section('breadcrumb')
    <a href="{{ route('admin.appointments.index') }}" class="text-silver-dim hover:text-giallo transition-premium font-heading uppercase tracking-wider">
        Jadwal Test Drive
    </a>
    <span class="text-gunmetal">/</span>
    <span class="text-giallo font-heading uppercase tracking-wider">Detail #{{ $appointment->id }}</span>
@endsection

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <a href="{{ route('admin.appointments.index') }}" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1 mb-2">
                ← Kembali ke Jadwal Uji Kendaraan
            </a>
            <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Showroom Concierge</p>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                DETAIL PERMOHONAN TEST DRIVE #{{ $appointment->id }}
            </h1>
            <p class="text-xs text-silver-dim mt-1">
                Diajukan pada {{ $appointment->created_at->format('d M Y, H:i') }} ({{ $appointment->created_at->diffForHumans() }})
            </p>
        </div>

        <div class="flex items-center gap-2">
            @php $st = $appointment->status->value; @endphp
            <span class="px-3 py-1 text-xs font-heading uppercase tracking-wider rounded font-bold
                @if($st === 'pending') bg-giallo/20 text-giallo border border-giallo/40
                @elseif($st === 'confirmed') bg-emerald-950/60 text-emerald-400 border border-emerald-800
                @elseif($st === 'completed') bg-blue-950/60 text-blue-400 border border-blue-800
                @else bg-gray-800 text-silver-dim border border-gunmetal @endif">
                Status: {{ $appointment->status->label() }}
            </span>
        </div>
    </div>

    {{-- Grid Details --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Car Info Card --}}
        <div class="panel border border-gunmetal/70 bg-carbon p-6 space-y-4">
            <h2 class="font-heading text-sm uppercase tracking-wider text-silver-bright border-b border-gunmetal pb-2">
                Unit Kendaraan Diuji
            </h2>
            @if($appointment->carUnit)
                <div class="aspect-video w-full rounded bg-nero border border-gunmetal overflow-hidden">
                    @if($appointment->carUnit->primaryPhoto)
                        <img src="{{ asset('storage/' . $appointment->carUnit->primaryPhoto->path) }}" alt="{{ $appointment->carUnit->model }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-3xl">🏎️</div>
                    @endif
                </div>
                <div>
                    <h3 class="font-heading text-base font-bold text-silver-bright">
                        {{ $appointment->carUnit->brand }} {{ $appointment->carUnit->model }}
                    </h3>
                    <p class="font-mono text-xs text-giallo mt-0.5">Kode Unit: {{ $appointment->carUnit->code }}</p>
                    <p class="font-mono text-sm text-silver-bright font-bold mt-1">
                        Rp {{ number_format($appointment->carUnit->price_sell_idr, 0, ',', '.') }}
                    </p>
                </div>
                <div class="pt-3 border-t border-gunmetal text-xs space-y-1.5 text-silver-dim">
                    <p>Tahun: <strong class="text-silver-bright">{{ $appointment->carUnit->year }}</strong></p>
                    <p>Transmisi: <strong class="text-silver-bright">{{ $appointment->carUnit->transmission }}</strong></p>
                    <p>Kilometer: <strong class="text-silver-bright">{{ number_format($appointment->carUnit->mileage_km) }} KM</strong></p>
                    <p>Warna: <strong class="text-silver-bright">{{ $appointment->carUnit->color }}</strong></p>
                </div>
                <a href="{{ route('admin.cars.show', $appointment->carUnit) }}" class="block text-center py-2 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright text-xs font-heading uppercase rounded transition">
                    Lihat Profil Mobil →
                </a>
            @else
                <p class="text-xs text-silver-dim">Data unit mobil tidak tersedia.</p>
            @endif
        </div>

        {{-- Customer & Schedule Info --}}
        <div class="md:col-span-2 space-y-6">
            {{-- Customer Card --}}
            <div class="panel border border-gunmetal/70 bg-carbon p-6 space-y-4">
                <h3 class="font-heading text-sm uppercase tracking-wider text-silver-bright border-b border-gunmetal pb-2">
                    Informasi Calon Pembeli (Customer)
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <p class="text-silver-dim">Nama Lengkap</p>
                        <p class="font-medium text-silver-bright text-sm mt-0.5">{{ $appointment->customer?->user?->name ?? 'Customer' }}</p>
                    </div>
                    <div>
                        <p class="text-silver-dim">Nomor WhatsApp / HP</p>
                        <p class="font-mono text-emerald-400 text-sm mt-0.5 flex items-center gap-1.5">
                            <span>💬</span> {{ $appointment->customer?->user?->phone ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-silver-dim">Alamat Email</p>
                        <p class="text-silver-bright mt-0.5 font-mono">{{ $appointment->customer?->user?->email ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-silver-dim">Waktu / Jadwal yang Diminta</p>
                        <p class="text-giallo font-mono text-sm font-bold mt-0.5">
                            {{ $appointment->scheduled_at->format('d F Y, H:i') }} WIB
                        </p>
                    </div>
                </div>

                @if($appointment->notes)
                    <div class="pt-3 border-t border-gunmetal">
                        <p class="text-xs text-silver-dim mb-1">Catatan Tambahan Customer:</p>
                        <div class="bg-steel/30 border border-gunmetal/60 rounded p-3 text-silver text-xs">
                            {{ $appointment->notes }}
                        </div>
                    </div>
                @endif

                {{-- WhatsApp Button --}}
                @if($appointment->customer?->user?->phone)
                    @php
                        $cleanPhone = preg_replace('/[^0-9]/', '', $appointment->customer->user->phone);
                        $waNumber = str_starts_with($cleanPhone, '0') ? '62' . substr($cleanPhone, 1) : $cleanPhone;
                        $carTitle = $appointment->carUnit ? ($appointment->carUnit->brand . ' ' . $appointment->carUnit->model) : 'unit kendaraan';
                        $waText = urlencode("Halo {$appointment->customer->user->name}, kami dari tim Sales AUDrive mengenai jadwal Test Drive {$carTitle} pada tanggal " . $appointment->scheduled_at->format('d M Y, H:i') . " WIB. Kami ingin mengonfirmasi kesediaan waktu Anda.");
                    @endphp
                    <div class="pt-3 border-t border-gunmetal flex gap-3">
                        <a href="https://wa.me/{{ $waNumber }}?text={{ $waText }}" target="_blank" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-heading text-xs uppercase font-bold tracking-wider rounded flex items-center gap-2 transition shadow">
                            <span>💬 Hubungi Customer via WhatsApp</span>
                        </a>
                    </div>
                @endif
            </div>

            {{-- Status Update Form --}}
            <div class="panel border border-gunmetal/70 bg-carbon p-6 space-y-4">
                <div class="border-b border-gunmetal pb-2">
                    <h3 class="font-heading text-xs uppercase tracking-wider text-giallo font-bold">
                        Kelola Status Permohonan Test Drive
                    </h3>
                    <p class="text-[11px] text-silver-dim mt-0.5">
                        Mengubah status ke <strong>Dikonfirmasi</strong> atau <strong>Selesai</strong> akan otomatis mengirimkan notifikasi ke pusat notifikasi customer.
                    </p>
                </div>

                <form action="{{ route('admin.appointments.status', $appointment) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="status" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1 font-semibold">
                                Perbarui Status
                            </label>
                            <select name="status" id="status" class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 focus:border-giallo focus:outline-none">
                                <option value="pending" {{ $appointment->status->value === 'pending' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                                <option value="confirmed" {{ $appointment->status->value === 'confirmed' ? 'selected' : '' }}>Dikonfirmasi (Jadwal Disetujui)</option>
                                <option value="completed" {{ $appointment->status->value === 'completed' ? 'selected' : '' }}>Selesai (Uji Kendaraan Terlaksana)</option>
                                <option value="cancelled" {{ $appointment->status->value === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </div>
                        <div>
                            <label for="notes" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1 font-semibold">
                                Catatan Tambahan Sales / Showroom
                            </label>
                            <input type="text" name="notes" id="notes" value="{{ old('notes', $appointment->notes) }}" placeholder="Contoh: Dikonfirmasi via telepon, rute test drive BSD toll..." class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 focus:border-giallo focus:outline-none">
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-3 border-t border-gunmetal">
                        <button type="submit" class="px-5 py-2.5 bg-giallo hover:bg-giallo-bright text-carbon font-heading text-xs uppercase font-bold tracking-wider rounded clip-angular-button transition-premium shadow">
                            ✓ Simpan Perubahan Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
