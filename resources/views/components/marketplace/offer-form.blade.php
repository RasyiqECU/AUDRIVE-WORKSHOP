{{--
  x-marketplace.offer-form — Direct Car Purchase Order Form
  Usage: <x-marketplace.offer-form :car="$car" />
--}}
@props(['car'])

<form method="POST" action="{{ route('marketplace.offer.store', $car->slug) }}" {{ $attributes->merge(['class' => 'space-y-4']) }}>
    @csrf

    <div class="bg-steel/30 p-3.5 rounded border border-gunmetal/60 space-y-1">
        <span class="text-silver-dim text-[10px] font-heading uppercase tracking-wider block">Total Harga Pembelian</span>
        <div class="text-xl font-display font-bold text-giallo">
            {{ $car->formatted_price }}
        </div>
        <p class="text-[11px] text-silver-dim">
            Harga On The Road (OTR) resmi AUDrive dengan garansi 150+ titik inspeksi.
        </p>
    </div>

    <div>
        <label for="order-note" class="block font-heading text-xs uppercase text-silver-dim tracking-wider mb-1">
            Catatan / Preferensi Pembeli (Opsional)
        </label>
        <textarea id="order-note"
                  name="note"
                  rows="3"
                  placeholder="Contoh: Rencana bayar cash / transfer, perkiraan jadwal pelunasan atau pengambilan unit..."
                  class="w-full bg-steel/40 border border-gunmetal rounded p-2.5 text-xs text-silver-bright placeholder:text-silver-dim/50 focus:border-giallo focus:outline-none transition"></textarea>
    </div>

    <x-ui.button type="submit" variant="primary" fullWidth class="py-3 text-xs tracking-wider uppercase font-bold flex items-center justify-center gap-2">
        <span>⚡</span> Beli / Pesan Mobil Sekarang
    </x-ui.button>

    <p class="text-[10px] text-silver-dim text-center leading-normal">
        Unit akan langsung berstatus <strong class="text-arancio font-semibold">Dipesan (Reserved)</strong> untuk Anda setelah pesanan dikirim.
    </p>
</form>
