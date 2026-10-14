@extends('layouts.admin')

@section('title', 'Detail Spare Part - ' . $sparePart->name)

@section('breadcrumb')
    <a href="{{ route('admin.spare-parts.index') }}" class="text-silver-dim hover:text-giallo transition-premium font-heading uppercase tracking-wider">
        Katalog Spare Part
    </a>
    <span class="text-gunmetal">/</span>
    <span class="text-giallo font-heading uppercase tracking-wider">{{ $sparePart->sku }}</span>
@endsection

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <a href="{{ route('admin.spare-parts.index') }}" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1 mb-2">
                ← Kembali ke Katalog Spare Part
            </a>
            <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Informasi Suku Cadang</p>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                {{ $sparePart->name }}
            </h1>
            <p class="font-mono text-xs text-giallo mt-1">SKU: {{ $sparePart->sku }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.inventory.movements', $sparePart->id) }}" class="px-3 py-2 bg-steel/60 border border-gunmetal text-silver-bright hover:border-giallo font-heading text-xs uppercase tracking-wider rounded transition-premium">
                📊 Kartu Stok
            </a>
            <a href="{{ route('admin.spare-parts.edit', $sparePart) }}" class="px-4 py-2 bg-giallo hover:bg-giallo-bright text-carbon font-heading text-xs uppercase tracking-wider font-bold rounded clip-angular-button transition-premium">
                Edit Data
            </a>
        </div>
    </div>

    {{-- Grid Details --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Card Spesifikasi --}}
        <div class="md:col-span-2 panel border border-gunmetal/70 bg-carbon p-6 space-y-4">
            <h2 class="font-heading text-sm uppercase tracking-wider text-silver-bright border-b border-gunmetal pb-2">
                Spesifikasi & Informasi Part
            </h2>
            <div class="grid grid-cols-2 gap-4 text-xs">
                <div>
                    <p class="text-silver-dim">Nama Suku Cadang</p>
                    <p class="font-medium text-silver-bright mt-0.5">{{ $sparePart->name }}</p>
                </div>
                <div>
                    <p class="text-silver-dim">SKU / Part Number</p>
                    <p class="font-mono text-giallo mt-0.5">{{ $sparePart->sku }}</p>
                </div>
                <div>
                    <p class="text-silver-dim">Kategori</p>
                    <p class="text-silver-bright mt-0.5">{{ $sparePart->category?->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-silver-dim">Merk / Brand</p>
                    <p class="text-silver-bright mt-0.5">{{ $sparePart->brand ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-silver-dim">Status Katalog</p>
                    <p class="text-silver-bright mt-0.5 uppercase">{{ is_string($sparePart->status) ? $sparePart->status : ($sparePart->status?->value ?? 'active') }}</p>
                </div>
                <div>
                    <p class="text-silver-dim">Berat (Kg)</p>
                    <p class="text-silver-bright mt-0.5">{{ $sparePart->weight_kg ? $sparePart->weight_kg . ' kg' : '-' }}</p>
                </div>
            </div>

            @if($sparePart->description)
                <div class="pt-4 border-t border-gunmetal">
                    <p class="text-xs text-silver-dim mb-1">Deskripsi & Catatan</p>
                    <p class="text-xs text-silver leading-relaxed bg-steel/30 p-3 rounded border border-gunmetal/40">
                        {{ $sparePart->description }}
                    </p>
                </div>
            @endif
        </div>

        {{-- Card Pricing & Stock --}}
        <div class="panel border border-gunmetal/70 bg-carbon p-6 space-y-6">
            <div>
                <p class="font-heading text-xs uppercase tracking-wider text-silver-dim">Harga Jual Satuan</p>
                <p class="font-display text-2xl font-bold text-silver-bright mt-1">
                    Rp {{ number_format($sparePart->price_idr, 0, ',', '.') }}
                </p>
            </div>

            <div class="pt-4 border-t border-gunmetal">
                <p class="font-heading text-xs uppercase tracking-wider text-silver-dim">Level Stok Saat Ini</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="font-display text-3xl font-bold {{ $sparePart->stock <= $sparePart->low_stock_threshold ? 'text-red-400' : 'text-emerald-400' }}">
                        {{ $sparePart->stock }}
                    </span>
                    <span class="text-xs text-silver-dim">pcs tersedia</span>
                </div>
                <p class="text-[11px] text-silver-dim mt-2">
                    Batas Minimum: <span class="font-mono text-silver-bright">{{ $sparePart->low_stock_threshold }} pcs</span>
                </p>
            </div>

            @if($sparePart->compatibilities->count() > 0)
                <div class="pt-4 border-t border-gunmetal">
                    <p class="font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">Kompatibilitas Kendaraan</p>
                    <ul class="space-y-1 text-xs text-silver-bright font-mono">
                        @foreach($sparePart->compatibilities as $comp)
                            <li class="bg-steel/40 px-2.5 py-1 rounded border border-gunmetal/40">
                                {{ $comp->vehicle_brand }} {{ $comp->vehicle_model }} ({{ $comp->year_from }} - {{ $comp->year_to }})
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
