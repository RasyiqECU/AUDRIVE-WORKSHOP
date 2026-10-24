@extends('layouts.admin')

@section('title', 'Laporan Laba Rugi')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div>
        <a href="{{ route('admin.finance.index') }}" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1 mb-2">
            ← Kembali ke Finansial
        </a>
        <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Income Statement</p>
        <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
            LAPORAN LABA / RUGI (PROFIT & LOSS)
        </h1>
        <p class="text-xs text-silver-dim mt-1">
            Periode: {{ $startDate->format('d M Y') }} s/d {{ $endDate->format('d M Y') }}
        </p>
    </div>

    {{-- Report Summary --}}
    <div class="panel border border-gunmetal/70 bg-carbon p-6 space-y-4 max-w-2xl">
        <h3 class="font-heading text-xs uppercase tracking-wider text-giallo border-b border-gunmetal/40 pb-2">
            Ikhtisar Laba Rugi
        </h3>
        <div class="space-y-3 font-mono text-xs">
            <div class="flex justify-between text-silver-bright">
                <span>Total Pendapatan (Gross Revenue):</span>
                <span class="text-emerald-400 font-bold">Rp {{ number_format($report['gross_revenue'] ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-silver-dim">
                <span>Beban Pokok Pendapatan (COGS Parts):</span>
                <span class="text-red-400 font-bold">Rp {{ number_format($report['cogs'] ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-silver-bright border-t border-gunmetal/40 pt-3 text-sm font-bold">
                <span>Laba Kotor (Gross Profit):</span>
                <span class="text-giallo font-bold">Rp {{ number_format($report['gross_profit'] ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
