@extends('layouts.admin')

@section('title', 'Manajemen Banner & Event Promosi')

@section('breadcrumb')
    <a href="{{ route('admin.vouchers.index') }}" class="text-silver-dim hover:text-giallo font-heading uppercase tracking-wider">Voucher</a>
    <span class="text-silver-dim">/</span>
    <span class="text-giallo font-heading uppercase tracking-wider">Banner Promo</span>
@endsection

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Marketing Campaigns</p>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                KAMPANYE & BANNER PROMO AUDrive
            </h1>
            <p class="text-xs text-silver-dim mt-1">
                Kelola penawaran khusus dan event promosi yang tampil di katalog beranda dan portal customer.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.vouchers.index') }}" class="px-4 py-2.5 bg-steel hover:bg-gunmetal border border-gunmetal text-silver-bright font-heading text-xs uppercase font-medium rounded transition-colors flex items-center gap-2">
                <span>🎟️</span>
                <span>Daftar Kode Voucher</span>
            </a>
            <a href="{{ route('admin.promotions.create') }}" class="px-4 py-2.5 bg-giallo hover:bg-giallo-bright text-carbon font-heading text-xs uppercase font-bold rounded clip-angular-button transition-colors flex items-center gap-1.5 shadow-[0_0_15px_rgba(245,196,0,0.2)]">
                <span class="text-sm">+</span>
                <span>Buat Kampanye Promo</span>
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="panel border border-gunmetal/60 bg-carbon p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Total Promo</p>
                    <p class="font-display text-2xl font-bold text-silver-bright mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="w-10 h-10 rounded bg-steel/40 border border-gunmetal flex items-center justify-center text-lg">
                    📢
                </div>
            </div>
        </div>

        <div class="panel border border-emerald-800/40 bg-carbon p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Promo Sedang Berjalan</p>
                    <p class="font-display text-2xl font-bold text-emerald-400 mt-1">{{ $stats['active'] }}</p>
                </div>
                <div class="w-10 h-10 rounded bg-emerald-950/40 border border-emerald-800/40 flex items-center justify-center text-lg">
                    🔥
                </div>
            </div>
        </div>

        <div class="panel border border-blue-500/40 bg-carbon p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Akan Datang (Upcoming)</p>
                    <p class="font-display text-2xl font-bold text-blue-400 mt-1">{{ $stats['upcoming'] }}</p>
                </div>
                <div class="w-10 h-10 rounded bg-blue-500/10 border border-blue-500/30 flex items-center justify-center text-lg">
                    ⏳
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="panel border border-gunmetal/60 bg-carbon overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-steel/40 text-silver-dim font-heading uppercase text-[10px] tracking-wider border-b border-gunmetal">
                    <tr>
                        <th class="py-3 px-4">Nama Kampanye Promo</th>
                        <th class="py-3 px-4">Nilai Promo</th>
                        <th class="py-3 px-4">Berlaku Untuk</th>
                        <th class="py-3 px-4">Periode Tayang</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/40">
                    @forelse($promotions as $promo)
                        <tr class="hover:bg-steel/20 transition-colors">
                            <td class="py-3.5 px-4">
                                <p class="font-bold text-silver-bright text-sm">{{ $promo->name }}</p>
                                @if($promo->description)
                                    <p class="text-[10px] text-silver-dim mt-0.5 line-clamp-1 max-w-sm">{{ $promo->description }}</p>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold">
                                @if(($promo->type->value ?? $promo->type) === 'percent')
                                    <span class="text-amber-400">{{ $promo->value }}%</span>
                                @else
                                    <span class="text-emerald-400">Rp {{ number_format($promo->value, 0, ',', '.') }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-silver-dim">
                                <span class="px-2 py-0.5 rounded bg-steel border border-gunmetal text-[10px] text-silver-bright font-mono">
                                    {{ $promo->applicable_to ?? 'Semua Layanan' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-[11px] text-silver-dim">
                                <div>{{ $promo->starts_at->format('d M Y') }} s/d</div>
                                <div class="text-silver-bright">{{ $promo->ends_at->format('d M Y') }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                @if(!$promo->is_active)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-steel text-silver-dim border border-gunmetal">Nonaktif</span>
                                @elseif($promo->ends_at->isPast())
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-danger/20 text-danger border border-danger/40">Berakhir</span>
                                @elseif($promo->starts_at->isFuture())
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-500/20 text-blue-300 border border-blue-500/40">Mendatang</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-950/60 text-emerald-400 border border-emerald-800">Aktif</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <form action="{{ route('admin.promotions.toggle', $promo) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" title="{{ $promo->is_active ? 'Nonaktifkan' : 'Aktifkan' }}" class="p-1.5 bg-steel hover:bg-gunmetal text-silver-bright rounded text-xs transition-colors">
                                            {{ $promo->is_active ? '⏸️' : '▶️' }}
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.promotions.edit', $promo) }}" class="p-1.5 bg-steel hover:bg-giallo hover:text-carbon text-silver-bright rounded text-xs transition-colors" title="Edit">
                                        ✏️
                                    </a>
                                    <form action="{{ route('admin.promotions.destroy', $promo) }}" method="POST" onsubmit="return confirm('Hapus promo {{ $promo->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-steel hover:bg-danger text-silver-bright rounded text-xs transition-colors" title="Hapus">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-silver-dim">
                                Belum ada kampanye promo. Klik tombol "+ Buat Kampanye Promo" di atas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($promotions->hasPages())
            <div class="p-4 border-t border-gunmetal">
                {{ $promotions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
