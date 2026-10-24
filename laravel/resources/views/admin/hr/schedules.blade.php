@extends('layouts.admin')

@section('title', 'Jadwal Kerja Mekanik')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div>
        <a href="{{ route('admin.hr.index') }}" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1 mb-2">
            ← Kembali ke SDM & Staff
        </a>
        <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Shift & Stall Roster</p>
        <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
            JADWAL KERJA & SHIFT MEKANIK
        </h1>
    </div>

    <div class="panel border border-gunmetal/70 bg-carbon p-8 text-center text-silver-dim">
        <span class="text-4xl block mb-2">📅</span>
        <p class="font-heading text-sm text-silver-bright uppercase tracking-wider">Penjadwalan Shift Teknisi</p>
        <p class="text-xs mt-1">Pengaturan jadwal operasional workshop dan penugasan stall servis.</p>
    </div>
</div>
@endsection
