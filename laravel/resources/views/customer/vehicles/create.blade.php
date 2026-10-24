@extends('layouts.customer')

@section('title', 'Tambah Kendaraan Baru')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    {{-- Breadcrumb & Back --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('customer.vehicles.index') }}" class="inline-flex items-center gap-2 font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider transition-premium">
            <span>&larr;</span> Kembali ke Daftar Kendaraan
        </a>
    </div>

    {{-- Header --}}
    <div>
        <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Garasi Pelanggan</p>
        <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
            TAMBAH KENDARAAN BARU
        </h1>
        <p class="text-xs text-silver-dim mt-1">
            Daftarkan kendaraan Anda untuk mempermudah booking servis, riwayat inspeksi, dan analisis AI sound scan.
        </p>
    </div>

    {{-- Main Form Card --}}
    <div class="panel border border-gunmetal bg-carbon/90 overflow-hidden shadow-panel">
        <div class="bg-steel/30 px-6 py-4 border-b border-gunmetal flex items-center justify-between">
            <h2 class="font-heading text-xs uppercase tracking-wider text-silver-bright flex items-center gap-2">
                <span class="text-giallo">🏎</span> Informasi Spesifikasi Kendaraan
            </h2>
            <span class="text-[10px] font-mono text-silver-dim tracking-wider uppercase">* Wajib Diisi</span>
        </div>

        <form action="{{ route('customer.vehicles.store') }}" method="POST" class="p-6 md:p-8 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Brand --}}
                <div>
                    <label for="brand" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">
                        Merk / Brand <span class="text-arancio">*</span>
                    </label>
                    <input type="text"
                           name="brand"
                           id="brand"
                           required
                           value="{{ old('brand') }}"
                           placeholder="Contoh: Lamborghini, Porsche, BMW"
                           class="w-full bg-nero border border-gunmetal px-4 py-2.5 text-silver-bright text-sm focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium placeholder:text-silver-dim/40 @error('brand') border-danger @enderror">
                    @error('brand')
                        <p class="text-danger font-mono text-[10px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Model --}}
                <div>
                    <label for="model" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">
                        Model / Tipe <span class="text-arancio">*</span>
                    </label>
                    <input type="text"
                           name="model"
                           id="model"
                           required
                           value="{{ old('model') }}"
                           placeholder="Contoh: Huracán EVO, 911 GT3 RS, M4"
                           class="w-full bg-nero border border-gunmetal px-4 py-2.5 text-silver-bright text-sm focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium placeholder:text-silver-dim/40 @error('model') border-danger @enderror">
                    @error('model')
                        <p class="text-danger font-mono text-[10px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Year --}}
                <div>
                    <label for="year" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">
                        Tahun Pembuatan <span class="text-arancio">*</span>
                    </label>
                    <input type="number"
                           name="year"
                           id="year"
                           required
                           min="1950"
                           max="{{ date('Y') + 1 }}"
                           value="{{ old('year', date('Y')) }}"
                           placeholder="Contoh: {{ date('Y') }}"
                           class="w-full bg-nero border border-gunmetal px-4 py-2.5 text-silver-bright text-sm focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium placeholder:text-silver-dim/40 @error('year') border-danger @enderror">
                    @error('year')
                        <p class="text-danger font-mono text-[10px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Plate Number --}}
                <div>
                    <label for="plate_number" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">
                        Nomor Polisi (Plat Nomor)
                    </label>
                    <input type="text"
                           name="plate_number"
                           id="plate_number"
                           value="{{ old('plate_number') }}"
                           placeholder="Contoh: B 1234 AUD"
                           class="w-full bg-nero border border-gunmetal px-4 py-2.5 text-silver-bright text-sm uppercase tracking-wider focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium placeholder:text-silver-dim/40 @error('plate_number') border-danger @enderror">
                    @error('plate_number')
                        <p class="text-danger font-mono text-[10px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Color --}}
                <div>
                    <label for="color" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">
                        Warna Kendaraan
                    </label>
                    <input type="text"
                           name="color"
                           id="color"
                           value="{{ old('color') }}"
                           placeholder="Contoh: Giallo Orion, Nero Noctis, Bianco"
                           class="w-full bg-nero border border-gunmetal px-4 py-2.5 text-silver-bright text-sm focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium placeholder:text-silver-dim/40 @error('color') border-danger @enderror">
                    @error('color')
                        <p class="text-danger font-mono text-[10px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- VIN --}}
                <div>
                    <label for="vin" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">
                        Nomor Rangka (VIN / Chasis)
                    </label>
                    <input type="text"
                           name="vin"
                           id="vin"
                           maxlength="17"
                           value="{{ old('vin') }}"
                           placeholder="17 Digit VIN (Opsional)"
                           class="w-full bg-nero border border-gunmetal px-4 py-2.5 text-silver-bright text-sm uppercase font-mono tracking-wider focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium placeholder:text-silver-dim/40 @error('vin') border-danger @enderror">
                    @error('vin')
                        <p class="text-danger font-mono text-[10px] mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Notes --}}
            <div>
                <label for="notes" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">
                    Catatan Tambahan / Modifikasi Kendaraan
                </label>
                <textarea name="notes"
                          id="notes"
                          rows="3"
                          placeholder="Catatan kondisi mesin, riwayat modifikasi exhaust/ECU, atau preferensi servis..."
                          class="w-full bg-nero border border-gunmetal px-4 py-2.5 text-silver-bright text-sm focus:border-giallo focus:outline-none focus:ring-1 focus:ring-giallo/30 transition-premium placeholder:text-silver-dim/40 @error('notes') border-danger @enderror">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-danger font-mono text-[10px] mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-between pt-4 border-t border-gunmetal">
                <a href="{{ route('customer.vehicles.index') }}" class="px-5 py-2.5 border border-gunmetal text-silver hover:border-silver-dim hover:text-silver-bright font-heading text-xs uppercase tracking-wider transition-premium">
                    Batal
                </a>
                <button type="submit" class="px-7 py-2.5 bg-giallo hover:bg-arancio text-nero font-heading font-bold text-xs uppercase tracking-wider transition-premium shadow-lg shadow-giallo/20 clip-angular-button">
                    Simpan Kendaraan
                </button>
            </div>
        </form>
    </div>

    {{-- Information Note --}}
    <div class="p-4 bg-steel/20 border border-gunmetal/40 flex items-start gap-3">
        <span class="text-giallo text-lg">💡</span>
        <div class="text-xs text-silver-dim leading-relaxed">
            <strong class="text-silver-bright">Keuntungan Mendaftarkan Kendaraan:</strong> Anda dapat langsung memilih kendaraan ini saat melakukan <a href="{{ route('workshop.booking.create') }}" class="text-giallo hover:underline">Booking Servis Workshop</a>, melakukan analisis audio <a href="{{ route('customer.ai-scan.create') }}" class="text-giallo hover:underline">AI Sound Scan</a>, dan memantau riwayat perawatan secara terpusat.
        </div>
    </div>
</div>
@endsection
