@extends('layouts.customer')

@section('title', 'Detail Kendaraan')

@section('content')
<div class="space-y-6">
    {{-- Top Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Garasi Pelanggan</p>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                DETAIL KENDARAAN
            </h1>
        </div>
        <a href="{{ route('customer.vehicles.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-giallo hover:bg-arancio text-nero font-heading font-bold text-xs uppercase tracking-wider transition-premium shadow-lg shadow-giallo/20 clip-angular-button shrink-0">
            &larr; Kembali ke Daftar
        </a>
    </div>

    <div class="panel border border-gunmetal bg-carbon/80 overflow-hidden">
        <div class="bg-steel/30 px-5 py-3 border-b border-gunmetal flex items-center justify-between">
            <span class="font-display text-lg text-silver-bright font-bold tracking-wide">
                {{ $vehicle->brand }} {{ $vehicle->model }}
            </span>
            <span class="font-mono text-[10px] text-silver-dim bg-nero px-2 py-0.5 border border-gunmetal/60">
                {{ $vehicle->year }}
            </span>
        </div>

        <div class="p-5 space-y-3">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-nero/60 border border-gunmetal/40 p-3 rounded-sm">
                    <dt class="font-heading text-[9px] uppercase tracking-widest text-silver-dim">Nomor Polisi</dt>
                    <dd class="font-mono text-sm text-silver-bright mt-1">{{ $vehicle->plate_number ?: '-' }}</dd>
                </div>
                <div class="bg-nero/60 border border-gunmetal/40 p-3 rounded-sm">
                    <dt class="font-heading text-[9px] uppercase tracking-widest text-silver-dim">Warna</dt>
                    <dd class="font-body text-sm text-silver-bright mt-1">{{ $vehicle->color ?: '-' }}</dd>
                </div>
                <div class="bg-nero/60 border border-gunmetal/40 p-3 rounded-sm sm:col-span-2">
                    <dt class="font-heading text-[9px] uppercase tracking-widest text-silver-dim">Nomor Rangka (VIN)</dt>
                    <dd class="font-mono text-[11px] text-silver-bright mt-1 select-all">{{ $vehicle->vin ?: '-' }}</dd>
                </div>
                @if($vehicle->notes)
                    <div class="bg-nero/60 border border-gunmetal/40 p-3 rounded-sm sm:col-span-2">
                        <dt class="font-heading text-[9px] uppercase tracking-widest text-silver-dim">Catatan</dt>
                        <dd class="font-body text-sm text-silver-dim mt-1">{{ $vehicle->notes }}</dd>
                    </div>
                @endif
            </dl>

            <div class="flex flex-wrap gap-3 pt-2">
                <a href="{{ route('customer.vehicles.edit', $vehicle) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-steel/40 hover:bg-steel/60 text-silver-bright font-heading font-bold text-xs uppercase tracking-wider transition-premium border border-gunmetal">
                    Edit Kendaraan
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
