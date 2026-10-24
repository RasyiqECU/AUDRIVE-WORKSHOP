@extends('layouts.public')

@section('title', $sparePart->name . ' — Spare Part AUDrive')
@section('meta_description', 'Beli ' . $sparePart->name . ' (' . $sparePart->sku . ') di AUDrive. Kualitas orisinal, garansi resmi, dan kompatibel dengan berbagai kendaraan premium.')

@section('content')
{{-- Breadcrumbs & Top Bar --}}
<div class="bg-carbon border-b border-gunmetal/40 py-3">
    <div class="container mx-auto px-4 flex items-center justify-between text-xs">
        <div class="flex items-center gap-2 font-mono text-silver-dim">
            <a href="{{ route('home') }}" class="hover:text-giallo">Home</a>
            <span>/</span>
            <a href="{{ route('parts.index') }}" class="hover:text-giallo">Spare Parts</a>
            <span>/</span>
            @if($sparePart->category)
                <a href="{{ route('parts.category', $sparePart->category->slug) }}" class="hover:text-giallo">{{ $sparePart->category->name }}</a>
                <span>/</span>
            @endif
            <span class="text-silver-bright truncate max-w-[200px] sm:max-w-xs">{{ $sparePart->name }}</span>
        </div>

        <a href="{{ route('parts.index') }}" class="font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider flex items-center gap-1">
            ← Kembali ke Katalog
        </a>
    </div>
</div>

{{-- Main Product Detail --}}
<section class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- Left: Image Gallery & Badges (5 cols) --}}
        <div class="lg:col-span-5">
            <div class="bg-carbon border border-gunmetal rounded overflow-hidden sticky top-24 shadow-xl">
                <div class="relative aspect-square bg-steel/30 flex items-center justify-center p-6">
                    @if($sparePart->image)
                        <img src="{{ asset('storage/' . $sparePart->image) }}" alt="{{ $sparePart->name }}"
                             class="max-h-full max-w-full object-contain" />
                    @else
                        <div class="text-center text-gunmetal">
                            <span class="text-8xl block mb-2">⚙️</span>
                            <span class="font-mono text-xs text-silver-dim uppercase">AUDrive Certified Genuine Part</span>
                        </div>
                    @endif

                    @if($sparePart->isLowStock())
                        <div class="absolute top-4 right-4">
                            <x-ui.badge type="warning">Stok Terbatas (Tersisa {{ $sparePart->stock }})</x-ui.badge>
                        </div>
                    @elseif($sparePart->stock <= 0)
                        <div class="absolute top-4 right-4">
                            <x-ui.badge type="danger">Stok Habis</x-ui.badge>
                        </div>
                    @else
                        <div class="absolute top-4 right-4">
                            <x-ui.badge type="success">Tersedia (Ready Stock)</x-ui.badge>
                        </div>
                    @endif
                </div>

                {{-- Quick specs footer in image box --}}
                <div class="p-4 bg-nero/50 border-t border-gunmetal/40 grid grid-cols-2 gap-3 font-mono text-[11px]">
                    <div>
                        <span class="text-silver-dim block">Kode SKU:</span>
                        <span class="text-giallo font-bold">{{ $sparePart->sku }}</span>
                    </div>
                    <div>
                        <span class="text-silver-dim block">Merek / Manufaktur:</span>
                        <span class="text-silver-bright font-semibold">{{ $sparePart->brand ?? 'OEM Original' }}</span>
                    </div>
                    @if($sparePart->weight_kg)
                        <div>
                            <span class="text-silver-dim block">Berat Pengiriman:</span>
                            <span class="text-silver-bright">{{ number_format((float)$sparePart->weight_kg, 2) }} kg</span>
                        </div>
                    @endif
                    <div>
                        <span class="text-silver-dim block">Status:</span>
                        <span class="text-verde font-semibold">{{ strtoupper($sparePart->status->value) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Product Details, Add to Cart & Actions (7 cols) --}}
        <div class="lg:col-span-7 space-y-6">
            {{-- Header info --}}
            <div class="bg-carbon border border-gunmetal p-6 rounded shadow-lg">
                <div class="flex items-center gap-2 mb-2">
                    @if($sparePart->category)
                        <a href="{{ route('parts.category', $sparePart->category->slug) }}" class="font-heading text-[11px] uppercase tracking-wider text-giallo bg-giallo/10 border border-giallo/30 px-2 py-0.5 rounded">
                            {{ $sparePart->category->name }}
                        </a>
                    @endif
                    <span class="font-mono text-xs text-silver-dim">SKU: {{ $sparePart->sku }}</span>
                </div>

                <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright mb-4">
                    {{ $sparePart->name }}
                </h1>

                {{-- Pricing Box --}}
                <div class="bg-nero border border-gunmetal/60 p-4 rounded mb-6 flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <span class="font-mono text-[10px] text-silver-dim uppercase block">Harga Satuan (Inc. PPN)</span>
                        <span class="font-mono text-2xl md:text-3xl font-bold text-giallo">
                            Rp {{ number_format($sparePart->price_idr, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="text-right">
                        <span class="font-mono text-xs block text-silver-dim">Ketersediaan Gudang:</span>
                        <span class="font-mono text-sm font-bold {{ $sparePart->stock > 0 ? 'text-verde' : 'text-danger' }}">
                            {{ $sparePart->stock > 0 ? $sparePart->stock . ' Unit Siap Kirim' : 'Habis (Pre-Order via Konsultan)' }}
                        </span>
                    </div>
                </div>

                {{-- Order Form / Add to Cart --}}
                @if($sparePart->stock > 0)
                    <form action="{{ route('parts.cart.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="spare_part_id" value="{{ $sparePart->id }}">

                        <div class="flex flex-wrap items-center gap-4">
                            <div class="w-32">
                                <label class="block font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1 font-semibold">Jumlah (Qty)</label>
                                <div class="flex items-center border border-gunmetal rounded bg-nero">
                                    <button type="button" onclick="const q = document.getElementById('qty'); if(q.value > 1) q.value--;" class="px-3 py-2 text-silver hover:text-giallo focus:outline-none">-</button>
                                    <input type="number" id="qty" name="qty" value="1" min="1" max="{{ $sparePart->stock }}"
                                           class="w-full bg-transparent text-center text-xs text-silver-bright font-mono focus:outline-none py-2" />
                                    <button type="button" onclick="const q = document.getElementById('qty'); if(q.value < {{ $sparePart->stock }}) q.value++;" class="px-3 py-2 text-silver hover:text-giallo focus:outline-none">+</button>
                                </div>
                            </div>

                            <div class="flex-1 min-w-[200px] pt-4">
                                <button type="submit" class="w-full py-3 bg-giallo hover:bg-giallo/90 text-nero font-heading font-bold text-xs uppercase tracking-wider rounded transition-all shadow-lg shadow-giallo/20 flex items-center justify-center gap-2">
                                    <span>🛒 Masukkan ke Keranjang</span>
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="p-4 bg-danger/10 border border-danger/30 rounded text-center">
                        <p class="font-heading text-xs text-danger uppercase font-semibold mb-2">Stok Saat Ini Sedang Habis</p>
                        <p class="font-body text-xs text-silver-dim mb-3">Hubungi konsultan kami untuk pemesanan khusus / indent komponen langka.</p>
                        <x-ui.button href="{{ route('contact') }}" variant="secondary" size="sm">
                            Ajukan Permintaan Part (Indent)
                        </x-ui.button>
                    </div>
                @endif

                {{-- Additional Actions --}}
                <div class="mt-6 pt-4 border-t border-gunmetal/40 flex flex-wrap gap-3">
                    <x-ui.button href="{{ route('workshop.index') }}" variant="ghost" size="sm" class="flex-1 text-center justify-center">
                        🔧 Booking Pemasangan di Bengkel
                    </x-ui.button>
                    <a href="https://wa.me/6281122334455?text=Halo%20AUDrive,%20saya%20ingin%20tanya%20tentang%20spare%20part%20{{ urlencode($sparePart->name) }}%20(SKU:%20{{ $sparePart->sku }})" target="_blank"
                       class="px-4 py-2 bg-verde/10 hover:bg-verde/20 border border-verde/30 text-verde font-heading text-xs uppercase tracking-wider rounded transition flex items-center gap-2">
                        <span>💬 Konsultasi WhatsApp</span>
                    </a>
                </div>
            </div>

            {{-- Description & Specifications --}}
            <div class="bg-carbon border border-gunmetal p-6 rounded shadow-lg">
                <h2 class="font-display text-base font-bold text-silver-bright uppercase tracking-wider mb-4 border-b border-gunmetal/40 pb-2">
                    Deskripsi & Spesifikasi Produk
                </h2>
                <div class="font-body text-xs md:text-sm text-silver-dim leading-relaxed whitespace-pre-line space-y-3">
                    @if($sparePart->description)
                        {{ $sparePart->description }}
                    @else
                        <p>Komponen pengganti resmi dengan standar presisi tinggi, memastikan performa maksimal serta daya tahan optimum bagi kendaraan premium Anda.</p>
                    @endif
                </div>
            </div>

            {{-- Compatibility Table --}}
            @if($sparePart->compatibilities->count() > 0)
                <div class="bg-carbon border border-gunmetal p-6 rounded shadow-lg">
                    <h2 class="font-display text-base font-bold text-silver-bright uppercase tracking-wider mb-4 border-b border-gunmetal/40 pb-2 flex items-center justify-between">
                        <span>Daftar Kompatibilitas Kendaraan</span>
                        <span class="font-mono text-xs text-giallo font-normal">{{ $sparePart->compatibilities->count() }} Model Terverifikasi</span>
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left font-body text-xs">
                            <thead>
                                <tr class="border-b border-gunmetal text-silver-dim font-heading uppercase text-[10px]">
                                    <th class="py-2.5 px-3">Merek Mobil</th>
                                    <th class="py-2.5 px-3">Model / Tipe</th>
                                    <th class="py-2.5 px-3 text-right">Rentang Tahun</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gunmetal/30">
                                @foreach($sparePart->compatibilities as $comp)
                                    <tr class="hover:bg-steel/10">
                                        <td class="py-2.5 px-3 font-heading font-semibold text-silver-bright">{{ $comp->vehicle_brand }}</td>
                                        <td class="py-2.5 px-3 text-silver">{{ $comp->vehicle_model }}</td>
                                        <td class="py-2.5 px-3 text-right font-mono text-giallo">{{ $comp->year_from }} - {{ $comp->year_to }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Related Products --}}
    @if(isset($relatedParts) && $relatedParts->count() > 0)
        <div class="mt-16 pt-8 border-t border-gunmetal/40">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="font-display text-xl font-bold text-silver-bright">PRODUK TERKAIT LAINNYA</h2>
                    <p class="font-heading text-xs text-silver-dim uppercase tracking-wider mt-0.5">Suku cadang dalam kategori serupa</p>
                </div>
                <a href="{{ route('parts.index') }}" class="font-heading text-xs text-giallo hover:underline uppercase tracking-wider">
                    Lihat Semua Part →
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($relatedParts as $relPart)
                    <x-parts.part-card :part="$relPart" />
                @endforeach
            </div>
        </div>
    @endif
</section>
@endsection
