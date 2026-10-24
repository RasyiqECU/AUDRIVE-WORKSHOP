@extends('layouts.admin')

@section('title', 'Riwayat Transaksi Poin Loyalitas')

@section('breadcrumb')
    <a href="{{ route('admin.loyalty.index') }}" class="text-silver-dim hover:text-giallo font-heading uppercase tracking-wider">Program Loyalitas</a>
    <span class="text-silver-dim">/</span>
    <span class="text-giallo font-heading uppercase tracking-wider">Riwayat Poin</span>
@endsection

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <a href="{{ route('admin.loyalty.index') }}" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1 mb-2 transition-colors">
                ← Kembali ke Manajemen Loyalitas
            </a>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide">
                LOG & AUDIT RIWAYAT POIN LOYALITAS
            </h1>
            <p class="text-xs text-silver-dim mt-1">
                Seluruh rekam jejak poin yang didapat dari kasir POS, ditukarkan menjadi kupon, atau disesuaikan manual oleh tim internal.
            </p>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="panel border border-gunmetal/60 bg-carbon p-4">
        <form method="GET" action="{{ route('admin.loyalty.history') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari keterangan atau nama customer..." 
                       class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 focus:border-giallo focus:outline-none placeholder-silver-dim">
            </div>
            <div>
                <select name="type" class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 focus:border-giallo focus:outline-none">
                    <option value="">Semua Jenis Transaksi</option>
                    <option value="earned" {{ request('type') === 'earned' ? 'selected' : '' }}>Poin Bertambah (+) / Didapat</option>
                    <option value="redeemed" {{ request('type') === 'redeemed' ? 'selected' : '' }}>Poin Berkurang (-) / Ditukar</option>
                    <option value="bonus" {{ request('type') === 'bonus' ? 'selected' : '' }}>Bonus Admin</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-giallo hover:bg-giallo-bright text-carbon font-heading text-xs uppercase font-bold py-2.5 px-4 rounded transition-premium">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'type']))
                    <a href="{{ route('admin.loyalty.history') }}" class="bg-steel hover:bg-gunmetal text-silver-bright font-heading text-xs uppercase py-2.5 px-3 rounded flex items-center justify-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- History Table --}}
    <div class="panel border border-gunmetal/60 bg-carbon overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-steel/40 text-silver-dim font-heading uppercase text-[10px] tracking-wider border-b border-gunmetal">
                    <tr>
                        <th class="py-3 px-4">Waktu Transaksi</th>
                        <th class="py-3 px-4">Customer</th>
                        <th class="py-3 px-4">Jenis Transaksi</th>
                        <th class="py-3 px-4">Perubahan Poin</th>
                        <th class="py-3 px-4">Keterangan / Referensi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/40">
                    @forelse($transactions as $t)
                        <tr class="hover:bg-steel/20 transition-colors">
                            <td class="py-3.5 px-4 font-mono text-[11px] text-silver-dim whitespace-nowrap">
                                {{ $t->created_at->format('d M Y, H:i:s') }}
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="font-bold text-silver-bright">{{ $t->customer->user->name ?? 'Customer' }}</p>
                                <p class="text-[10px] font-mono text-giallo">#{{ $t->customer->code ?? '-' }}</p>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono uppercase font-bold
                                    @if($t->points > 0) bg-emerald-950/60 text-emerald-400 border border-emerald-800
                                    @else bg-purple-950/60 text-purple-400 border border-purple-800 @endif">
                                    {{ $t->type }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-sm font-bold {{ $t->points > 0 ? 'text-emerald-400' : 'text-purple-400' }}">
                                {{ $t->points > 0 ? '+' : '' }}{{ number_format($t->points) }} pts
                            </td>
                            <td class="py-3.5 px-4 text-silver-bright text-xs">
                                {{ $t->description }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 text-silver-dim">
                                Belum ada riwayat transaksi poin yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transactions->hasPages())
            <div class="p-4 border-t border-gunmetal">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
