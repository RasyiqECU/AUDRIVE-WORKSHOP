@extends('layouts.customer')

@section('title', 'Kendaraan Saya')

@section('content')
<div class="space-y-6">
    {{-- Top Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Garasi Pelanggan</p>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                KENDARAAN SAYA
            </h1>
            <p class="text-xs text-silver-dim mt-1">
                Kelola daftar armada kendaraan pribadi Anda untuk kemudahan booking servis dan inspeksi.
            </p>
        </div>
        <a href="{{ route('customer.vehicles.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-giallo hover:bg-arancio text-nero font-heading font-bold text-xs uppercase tracking-wider transition-premium shadow-lg shadow-giallo/20 clip-angular-button shrink-0">
            <span>+</span> Tambah Kendaraan
        </a>
    </div>

    {{-- Vehicles Grid --}}
    @if($vehicles->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($vehicles as $vehicle)
                <div class="panel border border-gunmetal bg-carbon/80 hover:border-giallo/40 transition-premium overflow-hidden flex flex-col justify-between">
                    <div>
                        {{-- Card Header --}}
                        <div class="bg-steel/30 px-5 py-3 border-b border-gunmetal flex items-center justify-between">
                            <span class="font-mono text-xs font-bold text-giallo tracking-wider uppercase">
                                {{ $vehicle->plate_number ?: 'Tanpa Plat' }}
                            </span>
                            <span class="font-mono text-[10px] text-silver-dim bg-nero px-2 py-0.5 border border-gunmetal/60">
                                {{ $vehicle->year }}
                            </span>
                        </div>

                        {{-- Card Content --}}
                        <div class="p-5 space-y-3">
                            <div>
                                <h3 class="font-display text-lg text-silver-bright font-bold tracking-wide">
                                    {{ $vehicle->brand }} {{ $vehicle->model }}
                                </h3>
                                @if($vehicle->color)
                                    <p class="font-body text-xs text-silver-dim mt-0.5 flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-silver-dim/60 inline-block"></span>
                                        {{ $vehicle->color }}
                                    </p>
                                @endif
                            </div>

                            @if($vehicle->vin)
                                <div class="bg-nero/60 border border-gunmetal/40 p-2.5 rounded-sm">
                                    <span class="block font-heading text-[9px] uppercase tracking-widest text-silver-dim">Nomor Rangka (VIN)</span>
                                    <span class="font-mono text-[11px] text-silver-bright select-all">{{ $vehicle->vin }}</span>
                                </div>
                            @endif

                            @if($vehicle->notes)
                                <p class="text-xs text-silver-dim/80 italic line-clamp-2 bg-steel/10 p-2 border border-gunmetal/20">
                                    "{{ $vehicle->notes }}"
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Card Footer Actions --}}
                    <div class="p-4 bg-steel/20 border-t border-gunmetal flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('workshop.booking.create', ['vehicle_id' => $vehicle->id]) }}" class="px-3 py-1.5 bg-giallo/10 hover:bg-giallo text-giallo hover:text-nero border border-giallo/30 font-heading text-[10px] font-semibold uppercase tracking-wider transition-premium">
                                🔧 Booking Servis
                            </a>
                            <a href="{{ route('customer.ai-scan.create') }}" class="px-3 py-1.5 bg-verde/10 hover:bg-verde text-verde hover:text-nero border border-verde/30 font-heading text-[10px] font-semibold uppercase tracking-wider transition-premium">
                                🔊 Scan AI
                            </a>
                        </div>
                        <div class="flex items-center gap-1">
                            <a href="{{ route('customer.vehicles.edit', $vehicle) }}" class="p-1.5 text-silver-dim hover:text-silver-bright transition-premium" title="Edit Kendaraan">
                                ✏️
                            </a>
                            <form action="{{ route('customer.vehicles.destroy', $vehicle) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data kendaraan ini?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-silver-dim hover:text-danger transition-premium" title="Hapus Kendaraan">
                                    🗑
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="panel border border-gunmetal bg-carbon/50 p-12 text-center">
            <div class="w-16 h-16 bg-steel/40 border border-gunmetal clip-hexagon flex items-center justify-center mx-auto mb-4">
                <span class="text-3xl">🚗</span>
            </div>
            <h3 class="font-display text-lg text-silver-bright font-bold tracking-wide">Belum Ada Kendaraan Terdaftar</h3>
            <p class="text-xs text-silver-dim max-w-md mx-auto mt-2 mb-6 leading-relaxed">
                Tambahkan kendaraan pertama Anda untuk menikmati kemudahan booking servis berkala, konsultasi montir, dan diagnosa suara mesin dengan AI.
            </p>
            <a href="{{ route('customer.vehicles.create') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-giallo hover:bg-arancio text-nero font-heading font-bold text-xs uppercase tracking-wider transition-premium shadow-lg shadow-giallo/20 clip-angular-button">
                <span>+</span> Daftarkan Kendaraan Sekarang
            </a>
        </div>
    @endif
</div>
@endsection
