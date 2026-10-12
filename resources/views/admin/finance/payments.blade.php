@extends('layouts.admin')

@section('title', 'Riwayat Pembayaran Masuk')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div>
        <a href="{{ route('admin.finance.index') }}" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1 mb-2">
            ← Kembali ke Finansial
        </a>
        <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Transactions</p>
        <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
            RIWAYAT PEMBAYARAN
        </h1>
    </div>

    {{-- Payments Table --}}
    <div class="panel border border-gunmetal/70 bg-carbon overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-silver-muted">
                <thead class="bg-steel/50 text-silver-bright font-heading uppercase text-[10px] tracking-wider border-b border-gunmetal">
                    <tr>
                        <th class="p-3">ID Pembayaran</th>
                        <th class="p-3">Metode</th>
                        <th class="p-3 text-right">Nominal</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/40 font-mono">
                    @forelse($payments as $p)
                        <tr class="hover:bg-steel/20 transition-colors">
                            <td class="p-3 text-giallo font-semibold">#PAY-{{ $p->id }}</td>
                            <td class="p-3 uppercase">{{ $p->payment_method }}</td>
                            <td class="p-3 text-right text-emerald-400 font-bold">Rp {{ number_format($p->amount_idr ?? 0, 0, ',', '.') }}</td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 text-[10px] uppercase rounded font-bold bg-emerald-950/60 text-emerald-400 border border-emerald-800">
                                    {{ $p->status }}
                                </span>
                            </td>
                            <td class="p-3 text-silver-dim text-[11px]">{{ $p->created_at->format('d M Y, H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-silver-dim font-sans">
                                Belum ada riwayat pembayaran masuk.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
