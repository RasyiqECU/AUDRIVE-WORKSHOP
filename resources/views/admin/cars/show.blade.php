@extends('layouts.admin')

@section('title', 'Detail Unit — ' . $car->brand . ' ' . $car->model)

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.cars.index') }}" class="font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider">&larr; Daftar Unit</a>
                <span class="text-silver-dim text-xs">/</span>
                <span class="font-mono text-xs text-silver-dim">{{ $car->code }}</span>
            </div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">
                {{ $car->brand }} {{ $car->model }} <span class="text-silver-dim text-xl">({{ $car->year }})</span>
            </h1>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if($car->featured_at)
                <form action="{{ route('admin.cars.featured.remove', $car) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-2 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-dim text-xs font-heading uppercase rounded">
                        Hapus Featured
                    </button>
                </form>
            @else
                <form action="{{ route('admin.cars.featured', $car) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-3 py-2 bg-giallo/20 hover:bg-giallo text-giallo hover:text-nero border border-giallo/40 text-xs font-heading uppercase rounded font-bold transition-premium">
                        ⭐ Jadikan Featured
                    </button>
                </form>
            @endif

            <a href="{{ route('admin.cars.edit', $car) }}" class="px-4 py-2 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright text-xs font-heading uppercase tracking-wider rounded transition-premium">
                ✏️ Edit
            </a>

            <form action="{{ route('admin.cars.destroy', $car) }}" method="POST" onsubmit="return confirm('Hapus unit mobil ini secara permanen?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-danger/10 hover:bg-danger text-danger hover:text-white border border-danger/30 text-xs font-heading uppercase tracking-wider rounded transition-premium">
                    🗑 Hapus
                </button>
            </form>
        </div>
    </div>

    {{-- Main Info Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Photos & Specs --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Photos Gallery --}}
            <div class="panel border border-gunmetal bg-carbon p-4">
                <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright mb-3">Galeri Foto</h3>
                @if($car->photos && $car->photos->count() > 0)
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($car->photos as $photo)
                            <div class="aspect-video bg-nero border border-gunmetal overflow-hidden rounded">
                                <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="{{ $car->model }}" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 bg-nero border border-gunmetal text-center text-silver-dim text-xs">
                        Belum ada foto yang diunggah untuk unit ini.
                    </div>
                @endif
            </div>

            {{-- Description --}}
            <div class="panel border border-gunmetal bg-carbon p-5">
                <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Deskripsi & Kelengkapan</h3>
                <p class="text-xs text-silver leading-relaxed whitespace-pre-line">
                    {{ $car->description ?: 'Tidak ada deskripsi tambahan.' }}
                </p>
            </div>

            {{-- Offers / Penawaran --}}
            <div class="panel border border-gunmetal bg-carbon p-5">
                <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright mb-3">Penawaran Masuk ({{ $car->offers ? $car->offers->count() : 0 }})</h3>
                @if($car->offers && $car->offers->count() > 0)
                    <div class="space-y-2">
                        @foreach($car->offers as $offer)
                            <div class="flex items-center justify-between p-3 bg-steel/20 border border-gunmetal text-xs">
                                <div>
                                    <p class="font-bold text-silver-bright">{{ $offer->customer->user->name ?? 'Customer' }}</p>
                                    <p class="font-mono text-[10px] text-silver-dim">{{ $offer->created_at->format('d M Y H:i') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-mono font-bold text-giallo">Rp {{ number_format($offer->offered_price_idr, 0, ',', '.') }}</p>
                                    <span class="text-[10px] uppercase text-silver-dim">{{ $offer->status }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-silver-dim">Belum ada penawaran harga untuk unit ini.</p>
                @endif
            </div>
        </div>

        {{-- Sidebar Specs & Actions --}}
        <div class="space-y-6">
            {{-- Price & Status Panel --}}
            <div class="panel border border-gunmetal bg-carbon p-5 space-y-4">
                <div>
                    <span class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Harga Jual Listing</span>
                    <p class="font-display text-2xl font-bold text-giallo mt-1">
                        Rp {{ number_format($car->price_sell_idr, 0, ',', '.') }}
                    </p>
                    @if($car->price_buy_idr)
                        <p class="font-mono text-xs text-silver-dim mt-1">Harga Beli: Rp {{ number_format($car->price_buy_idr, 0, ',', '.') }}</p>
                    @endif
                </div>

                <div class="pt-3 border-t border-gunmetal">
                    <span class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Status Unit</span>
                    <div class="mt-2">
                        <x-ui.status-pill :status="$car->status->value ?? $car->status" />
                    </div>
                </div>

                {{-- Status Change Form --}}
                <form action="{{ route('admin.cars.status', $car) }}" method="POST" class="pt-3 border-t border-gunmetal space-y-2">
                    @csrf
                    @method('PATCH')
                    <label class="block font-heading text-[10px] uppercase tracking-wider text-silver-dim">Ubah Status</label>
                    <div class="flex gap-2">
                        <select name="status" class="flex-1 bg-nero border border-gunmetal px-2 py-1.5 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                            <option value="available" {{ ($car->status->value ?? $car->status) === 'available' ? 'selected' : '' }}>Tersedia</option>
                            <option value="reserved" {{ ($car->status->value ?? $car->status) === 'reserved' ? 'selected' : '' }}>Dipesan</option>
                            <option value="sold" {{ ($car->status->value ?? $car->status) === 'sold' ? 'selected' : '' }}>Terjual</option>
                            <option value="draft" {{ ($car->status->value ?? $car->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                        </select>
                        <button type="submit" class="px-3 py-1.5 bg-steel hover:bg-giallo hover:text-nero border border-gunmetal text-xs uppercase font-heading font-semibold transition">
                            Update
                        </button>
                    </div>
                </form>
            </div>

            {{-- Technical Specs --}}
            <div class="panel border border-gunmetal bg-carbon p-5">
                <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright mb-3">Spesifikasi Unit</h3>
                <dl class="divide-y divide-gunmetal/40 text-xs font-mono">
                    <div class="py-2 flex justify-between">
                        <dt class="text-silver-dim">Kode Unit</dt>
                        <dd class="text-silver-bright font-bold">{{ $car->code }}</dd>
                    </div>
                    <div class="py-2 flex justify-between">
                        <dt class="text-silver-dim">Merk & Model</dt>
                        <dd class="text-silver-bright">{{ $car->brand }} {{ $car->model }}</dd>
                    </div>
                    <div class="py-2 flex justify-between">
                        <dt class="text-silver-dim">Tahun</dt>
                        <dd class="text-silver-bright">{{ $car->year }}</dd>
                    </div>
                    <div class="py-2 flex justify-between">
                        <dt class="text-silver-dim">Transmisi</dt>
                        <dd class="text-silver-bright">{{ $car->transmission }}</dd>
                    </div>
                    <div class="py-2 flex justify-between">
                        <dt class="text-silver-dim">Odometer</dt>
                        <dd class="text-silver-bright">{{ number_format($car->mileage_km) }} KM</dd>
                    </div>
                    <div class="py-2 flex justify-between">
                        <dt class="text-silver-dim">Warna</dt>
                        <dd class="text-silver-bright">{{ $car->color ?: '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
