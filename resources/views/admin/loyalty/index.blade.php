@extends('layouts.admin')

@section('title', 'Manajemen Program Loyalitas & Reward')

@section('breadcrumb')
    <span class="text-giallo font-heading uppercase tracking-wider">Program Loyalitas</span>
@endsection

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">VIP Retention & Rewards</p>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                PROGRAM POIN LOYALITAS PELANGGAN
            </h1>
            <p class="text-xs text-silver-dim mt-1">
                Monitoring saldo poin customer, riwayat akumulasi dari kasir POS, dan penyesuaian bonus poin VIP.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.loyalty.history') }}" class="px-4 py-2.5 bg-steel hover:bg-gunmetal border border-gunmetal text-silver-bright font-heading text-xs uppercase font-medium rounded transition-colors flex items-center gap-2">
                <span>📜</span>
                <span>Riwayat Transaksi Poin</span>
            </a>
            <a href="{{ route('admin.vouchers.create') }}" class="px-4 py-2.5 bg-giallo hover:bg-giallo-bright text-carbon font-heading text-xs uppercase font-bold rounded clip-angular-button transition-colors flex items-center gap-1.5 shadow-[0_0_15px_rgba(245,196,0,0.2)]">
                <span class="text-sm">+</span>
                <span>Buat Voucher Reward Baru</span>
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="panel border border-giallo/40 bg-carbon p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Poin Beredar (Saldo)</p>
                    <p class="font-display text-2xl font-bold text-giallo mt-1">{{ number_format($stats['total_points']) }}</p>
                </div>
                <div class="w-10 h-10 rounded bg-giallo/10 border border-giallo/30 flex items-center justify-center text-lg">
                    💎
                </div>
            </div>
        </div>

        <div class="panel border border-emerald-800/40 bg-carbon p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Total Poin Didapat (All-Time)</p>
                    <p class="font-display text-2xl font-bold text-emerald-400 mt-1">{{ number_format($stats['total_earned']) }}</p>
                </div>
                <div class="w-10 h-10 rounded bg-emerald-950/40 border border-emerald-800/40 flex items-center justify-center text-lg">
                    📈
                </div>
            </div>
        </div>

        <div class="panel border border-purple-500/40 bg-carbon p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Total Poin Ditukarkan</p>
                    <p class="font-display text-2xl font-bold text-purple-400 mt-1">{{ number_format($stats['total_redeemed']) }}</p>
                </div>
                <div class="w-10 h-10 rounded bg-purple-500/10 border border-purple-500/30 flex items-center justify-center text-lg">
                    🎁
                </div>
            </div>
        </div>

        <div class="panel border border-blue-500/40 bg-carbon p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Katalog Reward Aktif</p>
                    <p class="font-display text-2xl font-bold text-blue-400 mt-1">{{ $stats['reward_vouchers'] }}</p>
                </div>
                <div class="w-10 h-10 rounded bg-blue-500/10 border border-blue-500/30 flex items-center justify-center text-lg">
                    🏷️
                </div>
            </div>
        </div>
    </div>

    {{-- Info Mekanisme Loyalty --}}
    <div class="panel border border-gunmetal/80 bg-carbon p-4 rounded-lg flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="text-2xl">⚙️</span>
            <div>
                <p class="font-heading text-xs uppercase text-silver-bright font-bold">Mekanisme Otomatis Loyalty AUDrive</p>
                <p class="text-[11px] text-silver-dim">Setiap transaksi di kasir POS otomatis memberikan <strong>1 Poin per kelipatan Rp 10.000</strong> pembelanjaan bersih.</p>
            </div>
        </div>
        <div class="text-[11px] text-silver-dim font-mono">
            Rumus: <span class="text-giallo">floor(Total Bayar / 10.000)</span>
        </div>
    </div>

    {{-- Katalog Reward Vouchers Mini Carousel --}}
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright">Katalog Reward Penukaran Poin Customer</h3>
            <a href="{{ route('admin.vouchers.index') }}?type=&status=active" class="text-xs text-giallo hover:underline">
                Kelola Semua Voucher →
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            @forelse($rewardVouchers as $rv)
                <div class="panel border border-gunmetal/80 bg-carbon p-3.5 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-mono font-bold text-xs text-giallo px-2 py-0.5 bg-giallo/10 border border-giallo/30 rounded">
                            {{ $rv->code }}
                        </span>
                        <span class="font-mono text-xs font-bold text-silver-bright">
                            👑 {{ number_format($rv->points_cost) }} Poin
                        </span>
                    </div>
                    <p class="text-xs font-bold text-silver-bright truncate">
                        @if(($rv->type->value ?? $rv->type) === 'percent')
                            Diskon {{ $rv->value }}%
                        @else
                            Potongan Rp {{ number_format($rv->value, 0, ',', '.') }}
                        @endif
                    </p>
                    <p class="text-[10px] text-silver-dim truncate">{{ $rv->description ?: 'Kupon reward penukaran poin.' }}</p>
                </div>
            @empty
                <div class="sm:col-span-3 text-center py-6 text-xs text-silver-dim border border-dashed border-gunmetal rounded">
                    Belum ada voucher yang diset sebagai reward poin. Buat voucher dengan biaya poin > 0.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Customer Points Table & Search --}}
    <div class="space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright">Saldo Poin Pelanggan</h3>
            <form method="GET" action="{{ route('admin.loyalty.index') }}" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode customer..." 
                       class="bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2 focus:border-giallo focus:outline-none placeholder-silver-dim w-64">
                <button type="submit" class="bg-giallo hover:bg-giallo-bright text-carbon font-heading text-xs uppercase font-bold px-3 py-2 rounded">
                    Cari
                </button>
            </form>
        </div>

        <div class="panel border border-gunmetal/60 bg-carbon overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-steel/40 text-silver-dim font-heading uppercase text-[10px] tracking-wider border-b border-gunmetal">
                        <tr>
                            <th class="py-3 px-4">Customer</th>
                            <th class="py-3 px-4">Kontak</th>
                            <th class="py-3 px-4">Saldo Poin</th>
                            <th class="py-3 px-4">Estimasi Nilai Kupon</th>
                            <th class="py-3 px-4 text-right">Penyesuaian Manual Poin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gunmetal/40">
                        @forelse($customers as $c)
                            <tr class="hover:bg-steel/20 transition-colors">
                                <td class="py-3.5 px-4">
                                    <p class="font-bold text-silver-bright">{{ $c->user->name ?? 'Customer' }}</p>
                                    <p class="text-[10px] font-mono text-giallo">#{{ $c->code }}</p>
                                </td>
                                <td class="py-3.5 px-4 text-silver-dim text-[11px]">
                                    <p>{{ $c->user->email ?? '-' }}</p>
                                    <p>{{ $c->phone ?? '-' }}</p>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-mono text-base font-bold text-giallo">
                                        {{ number_format($c->loyalty_points) }}
                                    </span>
                                    <span class="text-[10px] text-silver-dim font-mono">pts</span>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-silver-dim text-[11px]">
                                    ≈ Rp {{ number_format($c->loyalty_points * 100, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    {{-- Quick Action Adjust Points Form --}}
                                    <form action="{{ route('admin.loyalty.adjust', $c) }}" method="POST" class="inline-flex items-center gap-1.5" onsubmit="return confirm('Proses penyesuaian poin untuk customer ini?');">
                                        @csrf
                                        <select name="action" class="bg-steel border border-gunmetal text-silver-bright text-[11px] rounded px-2 py-1 focus:border-giallo focus:outline-none">
                                            <option value="add">+ Tambah (Bonus)</option>
                                            <option value="deduct">- Potong</option>
                                        </select>
                                        <input type="number" name="points" min="1" required placeholder="Jumlah" class="w-20 bg-steel border border-gunmetal text-silver-bright font-mono text-[11px] rounded px-2 py-1 focus:border-giallo focus:outline-none">
                                        <input type="text" name="description" required placeholder="Alasan / Keterangan..." class="w-36 bg-steel border border-gunmetal text-silver-bright text-[11px] rounded px-2 py-1 focus:border-giallo focus:outline-none">
                                        <button type="submit" class="bg-steel hover:bg-giallo hover:text-carbon text-silver-bright text-[11px] font-heading font-medium px-2.5 py-1 rounded transition-colors">
                                            Kirim
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-10 text-silver-dim">
                                    Tidak ada data customer yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($customers->hasPages())
                <div class="p-4 border-t border-gunmetal">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
