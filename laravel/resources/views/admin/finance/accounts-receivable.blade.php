@extends('layouts.admin')

@section('title', 'Piutang Usaha (Accounts Receivable)')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div>
        <a href="{{ route('admin.finance.index') }}" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1 mb-2">
            ← Kembali ke Finansial
        </a>
        <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Piutang & Pelanggan</p>
        <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
            DAFTAR PIUTANG USAHA (ACCOUNTS RECEIVABLE)
        </h1>
    </div>

    {{-- Receivable Stat --}}
    <div class="panel border border-amber-950/60 bg-carbon p-6 space-y-2 max-w-xl">
        <p class="font-heading text-xs uppercase tracking-wider text-silver-dim">Total Piutang Berjalan</p>
        <p class="font-mono text-3xl text-amber-400 font-bold">
            Rp {{ number_format($receivable['total_receivable'] ?? 0, 0, ',', '.') }}
        </p>
        <p class="text-xs text-silver-dim">Dari {{ $receivable['outstanding_count'] ?? 0 }} invoice outstanding/belum lunas.</p>
    </div>
</div>
@endsection
