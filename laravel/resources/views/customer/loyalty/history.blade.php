@extends('layouts.customer')

@section('title', 'Riwayat Poin Loyalitas')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <div class="flex items-center justify-between">
        <a href="{{ route('customer.loyalty.index') }}" class="font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider">&larr; Kembali ke Loyalty Point</a>
    </div>

    <div>
        <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">RIWAYAT MUTASI POIN</h1>
        <p class="text-xs text-silver-dim mt-1">Catatan perolehan dan penukaran poin loyalitas akun Anda</p>
    </div>

    <div class="panel border border-gunmetal bg-carbon overflow-hidden">
        <div class="divide-y divide-gunmetal/40 text-xs">
            @forelse($transactions as $trx)
                <div class="p-4 flex items-center justify-between hover:bg-steel/20 transition-premium">
                    <div>
                        <p class="font-bold text-silver-bright">{{ $trx->description ?: ucfirst($trx->type) }}</p>
                        <p class="font-mono text-[10px] text-silver-dim mt-0.5">{{ $trx->created_at->format('d M Y H:i') }}</p>
                    </div>
                    <div class="font-mono font-bold text-sm {{ $trx->points > 0 ? 'text-verde' : 'text-danger' }}">
                        {{ $trx->points > 0 ? '+' : '' }}{{ number_format($trx->points) }} PTS
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-silver-dim text-xs">
                    Belum ada riwayat transaksi poin.
                </div>
            @endforelse
        </div>

        @if(method_exists($transactions, 'hasPages') && $transactions->hasPages())
            <div class="p-4 border-t border-gunmetal bg-steel/20">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
