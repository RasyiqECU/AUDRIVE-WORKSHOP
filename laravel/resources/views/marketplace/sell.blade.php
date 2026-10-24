@extends('layouts.public')

@section('title', 'Jual Mobil Anda ke AUDrive — Harga Terbaik & Inspeksi Cepat')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('marketplace.index') }}" class="text-xs text-giallo hover:underline uppercase tracking-wider font-heading">
            &larr; Kembali ke Marketplace
        </a>
        <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright mt-2">
            JUAL MOBIL ANDA KE AUDRIVE
        </h1>
        <p class="text-xs text-silver-dim mt-1">Isi formulir di bawah ini untuk mendapatkan estimasi penawaran beli tunai instan dari tim appraiser kami.</p>
    </div>

    <div class="bg-carbon border border-gunmetal rounded-lg p-6 md:p-8 shadow-xl">
        <form action="{{ route('marketplace.sell.store') }}" method="POST" class="space-y-4">
            @csrf

            <x-forms.input name="brand" label="Merek Mobil" required placeholder="Contoh: Porsche, BMW, Mercedes-Benz" />
            <x-forms.input name="model" label="Model / Tipe" required placeholder="Contoh: 911 Carrera S, M3 Competition" />
            <x-forms.input name="year" label="Tahun Pembuatan" type="number" min="1990" max="{{ date('Y') + 1 }}" required placeholder="{{ date('Y') - 3 }}" />
            <x-forms.input name="expected_price" label="Ekspektasi Harga Jual (Rp)" type="number" required placeholder="1500000000" />
            <x-forms.textarea name="notes" label="Kondisi & Catatan Tambahan" placeholder="Kilometer, riwayat servis, modifikasi, atau catatan lainnya..." rows="3" />

            <div class="pt-4 border-t border-gunmetal/40 flex justify-end gap-3">
                <a href="{{ route('marketplace.index') }}" class="px-5 py-2.5 border border-gunmetal text-silver-dim hover:text-silver-bright font-heading text-xs uppercase tracking-wider rounded">
                    Batal
                </a>
                <x-ui.button type="submit" variant="primary">Kirim Pengajuan Jual Mobil</x-ui.button>
            </div>
        </form>
    </div>
</div>
@endsection
