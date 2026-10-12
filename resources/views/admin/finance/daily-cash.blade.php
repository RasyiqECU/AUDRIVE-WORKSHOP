@extends('layouts.admin')

@section('title', 'Laporan Kas Harian')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <a href="{{ route('admin.finance.index') }}" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1 mb-2">
                ← Kembali ke Finansial
            </a>
            <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Daily Cash Register</p>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                LAPORAN KAS HARIAN: {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
            </h1>
        </div>
        <form action="{{ route('admin.finance.daily-cash') }}" method="GET" class="flex items-center gap-2">
            <input type="date" name="date" value="{{ $date }}" class="bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2 focus:border-giallo focus:outline-none">
            <button type="submit" class="px-4 py-2 bg-giallo text-carbon font-heading text-xs uppercase font-bold rounded">
                Tampilkan
            </button>
        </form>
    </div>

    {{-- Report Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="panel border border-gunmetal/70 bg-carbon p-6 space-y-4">
            <h3 class="font-heading text-xs uppercase tracking-wider text-giallo border-b border-gunmetal/40 pb-2">
                Rincian Arus Kas
            </h3>
            <div class="space-y-3 font-mono text-xs">
                <div class="flex justify-between text-silver-bright">
                    <span>Total Pendapatan:</span>
                    <span class="text-emerald-400 font-bold">Rp {{ number_format($report['total_all'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-silver-dim">
                    <span>Workshop / Servis:</span>
                    <span>Rp {{ number_format($report['workshop_total'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-silver-dim">
                    <span>Suku Cadang / POS:</span>
                    <span>Rp {{ number_format($report['parts_total'] ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
