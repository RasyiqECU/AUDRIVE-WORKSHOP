@extends('layouts.admin')

@section('title', 'Detail Voucher ' . $voucher->code)

@section('breadcrumb')
    <a href="{{ route('admin.vouchers.index') }}" class="text-silver-dim hover:text-giallo font-heading uppercase tracking-wider">Voucher</a>
    <span class="text-silver-dim">/</span>
    <span class="text-giallo font-heading uppercase tracking-wider">{{ $voucher->code }}</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('admin.vouchers.index') }}" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1 mb-2 transition-colors">
            ← Kembali ke Daftar Voucher
        </a>
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <h1 class="font-display text-2xl text-silver-bright font-bold tracking-wide">
                DETAIL VOUCHER: {{ $voucher->code }}
            </h1>
            <span class="px-3 py-1 rounded text-[10px] font-heading font-bold uppercase tracking-wider border {{ $voucher->is_active ? 'bg-verde/15 border-verde/40 text-verde' : 'bg-steel/40 border-gunmetal text-silver-dim' }}">
                {{ $voucher->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
        </div>
    </div>

    <div class="panel border border-gunmetal/70 bg-carbon p-6 space-y-4">
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div>
                <dt class="text-silver-dim font-heading uppercase tracking-wider">Kode</dt>
                <dd class="text-silver-bright font-mono font-bold mt-1">{{ $voucher->code }}</dd>
            </div>
            <div>
                <dt class="text-silver-dim font-heading uppercase tracking-wider">Tipe Potongan</dt>
                <dd class="text-silver-bright mt-1">{{ ($voucher->type->value ?? $voucher->type) === 'fixed' ? 'Nominal Tetap' : 'Persentase' }}</dd>
            </div>
            <div>
                <dt class="text-silver-dim font-heading uppercase tracking-wider">Nilai Potongan</dt>
                <dd class="text-giallo font-bold mt-1">
                    {{ ($voucher->type->value ?? $voucher->type) === 'fixed' ? 'Rp ' . number_format($voucher->value, 0, ',', '.') : $voucher->value . '%' }}
                </dd>
            </div>
            <div>
                <dt class="text-silver-dim font-heading uppercase tracking-wider">Biaya Tukar Poin Loyalty</dt>
                <dd class="text-silver-bright font-mono mt-1">{{ number_format($voucher->points_cost ?? 0, 0, ',', '.') }}</dd>
            </div>
            <div>
                <dt class="text-silver-dim font-heading uppercase tracking-wider">Minimal Pembelian</dt>
                <dd class="text-silver-bright font-mono mt-1">Rp {{ number_format($voucher->min_purchase_idr ?? 0, 0, ',', '.') }}</dd>
            </div>
            <div>
                <dt class="text-silver-dim font-heading uppercase tracking-wider">Kuota Penggunaan</dt>
                <dd class="text-silver-bright font-mono mt-1">{{ $voucher->used_count }} / {{ $voucher->max_usage }}</dd>
            </div>
            <div>
                <dt class="text-silver-dim font-heading uppercase tracking-wider">Kadaluarsa</dt>
                <dd class="text-silver-bright mt-1">{{ $voucher->expires_at ? $voucher->expires_at->translatedFormat('d M Y H:i') : '—' }}</dd>
            </div>
        </dl>

        @if($voucher->description)
            <div class="border-t border-gunmetal/60 pt-4">
                <dt class="text-silver-dim font-heading uppercase tracking-wider text-xs">Deskripsi & Syarat</dt>
                <dd class="text-silver-dim text-xs mt-1 leading-relaxed whitespace-pre-line">{{ $voucher->description }}</dd>
            </div>
        @endif
    </div>
</div>
@endsection
