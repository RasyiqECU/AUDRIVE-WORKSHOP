@extends('layouts.admin')

@section('title', 'Laporan Keuangan & Finansial')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Financial Accounting</p>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                RINGKASAN KEUANGAN
            </h1>
            <p class="text-xs text-silver-dim mt-1">
                Laporan kas harian, tren pendapatan bulanan, invoice piutang, dan laba rugi.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.finance.daily-cash') }}" class="px-3 py-2 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright text-xs font-heading uppercase rounded">
                💵 Kas Harian
            </a>
            <a href="{{ route('admin.finance.invoices') }}" class="px-3 py-2 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright text-xs font-heading uppercase rounded">
                🧾 Invoices
            </a>
            <a href="{{ route('admin.finance.profit-loss') }}" class="px-3 py-2 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright text-xs font-heading uppercase rounded">
                📊 Laba Rugi
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="panel border border-gunmetal/70 bg-carbon p-5 space-y-2">
            <p class="font-heading text-xs uppercase tracking-wider text-silver-dim">Kas Masuk Hari Ini</p>
            <p class="font-mono text-2xl text-emerald-400 font-bold">
                Rp {{ number_format($dailyReport['total_all'] ?? 0, 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-silver-dim">Total transaksi cash, transfer & QRIS hari ini</p>
        </div>

        <div class="panel border border-gunmetal/70 bg-carbon p-5 space-y-2">
            <p class="font-heading text-xs uppercase tracking-wider text-silver-dim">Pendapatan Workshop Hari Ini</p>
            <p class="font-mono text-2xl text-giallo font-bold">
                Rp {{ number_format($dailyReport['workshop_total'] ?? 0, 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-silver-dim">Dari jasa servis & perbaikan unit</p>
        </div>

        <div class="panel border border-gunmetal/70 bg-carbon p-5 space-y-2">
            <p class="font-heading text-xs uppercase tracking-wider text-silver-dim">Pendapatan Spare Parts Hari Ini</p>
            <p class="font-mono text-2xl text-blue-400 font-bold">
                Rp {{ number_format($dailyReport['parts_total'] ?? 0, 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-silver-dim">Dari penjualan POS & work order parts</p>
        </div>
    </div>
</div>
@endsection
