@extends('layouts.admin')

@section('title', 'Daftar Invoice')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <a href="{{ route('admin.finance.index') }}" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1 mb-2">
                ← Kembali ke Finansial
            </a>
            <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Billing & Invoicing</p>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                INVOICE & TAGIHAN
            </h1>
        </div>
    </div>

    {{-- Invoices Table --}}
    <div class="panel border border-gunmetal/70 bg-carbon overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-silver-muted">
                <thead class="bg-steel/50 text-silver-bright font-heading uppercase text-[10px] tracking-wider border-b border-gunmetal">
                    <tr>
                        <th class="p-3">No. Invoice</th>
                        <th class="p-3">Customer / Entitas</th>
                        <th class="p-3 text-right">Total Tagihan</th>
                        <th class="p-3 text-right">Sisa Tagihan</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Jatuh Tempo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/40 font-mono">
                    @forelse($invoices as $inv)
                        <tr class="hover:bg-steel/20 transition-colors">
                            <td class="p-3 text-giallo font-semibold">{{ $inv->invoice_number ?? '#INV-' . $inv->id }}</td>
                            <td class="p-3 font-sans text-silver-bright">{{ $inv->customer?->user?->name ?? 'Walk-in Customer' }}</td>
                            <td class="p-3 text-right text-silver-bright">Rp {{ number_format($inv->total_amount_idr ?? 0, 0, ',', '.') }}</td>
                            <td class="p-3 text-right text-red-400">Rp {{ number_format($inv->remaining_balance_idr ?? 0, 0, ',', '.') }}</td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 text-[10px] uppercase rounded font-bold
                                    @if($inv->status === 'paid') bg-emerald-950/60 text-emerald-400 border border-emerald-800
                                    @else bg-amber-950/60 text-amber-400 border border-amber-800 @endif">
                                    {{ $inv->status }}
                                </span>
                            </td>
                            <td class="p-3 text-silver-dim text-[11px]">{{ $inv->due_date?->format('d M Y') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-silver-dim font-sans">
                                Belum ada riwayat tagihan invoice.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
