@extends('layouts.public')

@section('title', 'Checkout Suku Cadang — AUDrive')
@section('meta_description', 'Penyelesaian pesanan suku cadang dan instruksi pengiriman AUDrive.')

@section('content')
<section class="container mx-auto px-4 py-10">
    <div class="max-w-5xl mx-auto">
        <div class="mb-8 pb-4 border-b border-gunmetal/40">
            <a href="{{ route('parts.cart') }}" class="font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider mb-2 inline-block">
                ← Kembali ke Keranjang
            </a>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright">
                CHECKOUT <span class="text-giallo">PESANAN</span>
            </h1>
            <p class="font-body text-xs text-silver-dim mt-1">Lengkapi metode pengiriman dan data penerima suku cadang Anda.</p>
        </div>

        <form action="{{ route('parts.checkout.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                {{-- Left: Shipping & Details (7 cols) --}}
                <div class="lg:col-span-7 space-y-6">
                    {{-- Metode Pengiriman --}}
                    <div class="bg-carbon border border-gunmetal p-6 rounded shadow-xl">
                        <h2 class="font-display text-sm font-bold text-silver-bright uppercase tracking-wider mb-4 border-b border-gunmetal/40 pb-2">
                            1. Pilih Metode Pengiriman
                        </h2>

                        <div class="space-y-3">
                            <label class="flex items-start gap-3 p-4 bg-nero border border-gunmetal/60 rounded cursor-pointer hover:border-giallo/50 transition">
                                <input type="radio" name="shipping_method" value="delivery" checked class="mt-1 text-giallo focus:ring-0"
                                       onchange="document.getElementById('address-section').classList.remove('hidden')">
                                <div>
                                    <span class="font-heading text-xs font-semibold text-silver-bright uppercase tracking-wider block">Kirim ke Alamat (Delivery Ekspedisi)</span>
                                    <span class="font-body text-xs text-silver-dim">Pengiriman menggunakan kurir resmi dengan proteksi asuransi penuh langsung ke lokasi Anda.</span>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-4 bg-nero border border-gunmetal/60 rounded cursor-pointer hover:border-giallo/50 transition">
                                <input type="radio" name="shipping_method" value="pickup" class="mt-1 text-giallo focus:ring-0"
                                       onchange="document.getElementById('address-section').classList.add('hidden')">
                                <div>
                                    <span class="font-heading text-xs font-semibold text-silver-bright uppercase tracking-wider block">Ambil Sendiri di Showroom / Bengkel (Self-Pickup)</span>
                                    <span class="font-body text-xs text-silver-dim">Ambil langsung di Showroom Utama AUDrive Jakarta Utara atau jadwalkan pemasangan langsung di tempat.</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Alamat Pengiriman --}}
                    <div id="address-section" class="bg-carbon border border-gunmetal p-6 rounded shadow-xl">
                        <h2 class="font-display text-sm font-bold text-silver-bright uppercase tracking-wider mb-4 border-b border-gunmetal/40 pb-2">
                            2. Alamat Lengkap Pengiriman
                        </h2>

                        <div>
                            <label class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2 font-semibold">Alamat Penerima / Catatan Kurir</label>
                            <textarea name="shipping_address" rows="4" placeholder="Tuliskan nama penerima, no. HP aktif, nama jalan, nomor rumah/kantor, RT/RW, kelurahan, kecamatan, kota, dan kode pos..."
                                      class="w-full bg-steel/30 border border-gunmetal/60 rounded p-3 text-xs text-silver-bright focus:border-giallo focus:outline-none"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Right: Order Review & Submit (5 cols) --}}
                <div class="lg:col-span-5">
                    <div class="bg-carbon border border-gunmetal p-6 rounded shadow-xl sticky top-24">
                        <h2 class="font-display text-sm font-bold text-silver-bright uppercase tracking-wider mb-4 border-b border-gunmetal/40 pb-2">
                            Daftar Barang ({{ $cart->items->count() }})
                        </h2>

                        @php $subtotal = 0; @endphp
                        <div class="space-y-3 mb-6 max-h-60 overflow-y-auto divide-y divide-gunmetal/30 pr-2">
                            @foreach($cart->items as $item)
                                @php
                                    $itemTotal = ($item->sparePart->price_idr ?? 0) * $item->qty;
                                    $subtotal += $itemTotal;
                                @endphp
                                <div class="pt-2 first:pt-0 flex items-center justify-between text-xs">
                                    <div class="pr-2">
                                        <p class="font-heading font-semibold text-silver-bright line-clamp-1">{{ $item->sparePart->name ?? 'Part' }}</p>
                                        <p class="font-mono text-[10px] text-silver-dim">{{ $item->qty }}x @ Rp {{ number_format($item->sparePart->price_idr ?? 0, 0, ',', '.') }}</p>
                                    </div>
                                    <span class="font-mono text-silver-bright font-bold whitespace-nowrap">Rp {{ number_format($itemTotal, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>

                        @php
                            $tax = (int)($subtotal * 0.11);
                            $grandTotal = $subtotal + $tax;
                        @endphp

                        <div class="space-y-2 pt-4 border-t border-gunmetal/40 font-body text-xs mb-6">
                            <div class="flex justify-between text-silver-dim">
                                <span>Subtotal</span>
                                <span class="font-mono text-silver-bright">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-silver-dim">
                                <span>PPN (11%)</span>
                                <span class="font-mono text-silver-bright">Rp {{ number_format($tax, 0, ',', '.') }}</span>
                            </div>
                            <div class="pt-3 border-t border-gunmetal/40 flex justify-between items-end">
                                <span class="font-heading font-semibold text-silver-bright uppercase">Total Tagihan</span>
                                <span class="font-mono text-lg font-bold text-giallo">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-3 bg-giallo hover:bg-giallo/90 text-nero font-heading font-bold text-xs uppercase tracking-wider rounded transition-all shadow-lg shadow-giallo/20">
                            Konfirmasi & Buat Tagihan Pesanan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection
