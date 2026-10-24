@extends('layouts.admin')

@section('title', 'Manajemen SDM & Karyawan')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Human Resources</p>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                MANAJEMEN SDM & STAFF
            </h1>
            <p class="text-xs text-silver-dim mt-1">
                Kelola jadwal kerja mekanik, kehadiran staff, dan komisi tim sales showroom.
            </p>
        </div>
    </div>

    {{-- HR Modules --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="panel border border-gunmetal/70 bg-carbon p-6 space-y-3">
            <span class="text-3xl">📅</span>
            <h3 class="font-display text-base text-silver-bright font-bold">Jadwal Kerja Mekanik</h3>
            <p class="text-xs text-silver-dim leading-relaxed">
                Pengaturan shift teknisi workshop dan alokasi stall servis mobil.
            </p>
        </div>

        <div class="panel border border-gunmetal/70 bg-carbon p-6 space-y-3">
            <span class="text-3xl">⏱️</span>
            <h3 class="font-display text-base text-silver-bright font-bold">Absensi & Kehadiran</h3>
            <p class="text-xs text-silver-dim leading-relaxed">
                Rekap absensi harian karyawan workshop dan showroom AUDrive.
            </p>
        </div>

        <div class="panel border border-gunmetal/70 bg-carbon p-6 space-y-3">
            <span class="text-3xl">💰</span>
            <h3 class="font-display text-base text-silver-bright font-bold">Komisi & Insentif</h3>
            <p class="text-xs text-silver-dim leading-relaxed">
                Perhitungan insentif penjualan unit mobil dan bonus produktivitas servis.
            </p>
        </div>
    </div>
</div>
@endsection
