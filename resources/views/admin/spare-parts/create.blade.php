@extends('layouts.admin')

@section('title', 'Tambah Spare Part')

@section('breadcrumb')
    <a href="{{ route('admin.spare-parts.index') }}" class="text-silver-dim hover:text-giallo transition-premium font-heading uppercase tracking-wider">
        Katalog Spare Part
    </a>
    <span class="text-gunmetal">/</span>
    <span class="text-giallo font-heading uppercase tracking-wider">Tambah Baru</span>
@endsection

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    {{-- Header --}}
    <div>
        <a href="{{ route('admin.spare-parts.index') }}" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1 mb-2">
            ← Kembali ke Katalog Spare Part
        </a>
        <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Master Data</p>
        <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
            TAMBAH SUKU CADANG BARU
        </h1>
        <p class="text-xs text-silver-dim mt-1">
            Daftarkan SKU suku cadang baru ke dalam katalog dan database sistem.
        </p>
    </div>

    {{-- Form --}}
    <form action="{{ route('admin.spare-parts.store') }}" method="POST" enctype="multipart/form-data" class="panel border border-gunmetal/70 bg-carbon p-6 space-y-4">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="sku" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">
                    SKU / Part Number <span class="text-red-500">*</span>
                </label>
                <input type="text" name="sku" id="sku" value="{{ old('sku') }}" required placeholder="Contoh: AUD-BRK-001" class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 focus:border-giallo focus:outline-none">
                @error('sku') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="name" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">
                    Nama Spare Part <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: Kampas Rem Depan Brembo" class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 focus:border-giallo focus:outline-none">
                @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="category_id" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">
                    Kategori <span class="text-red-500">*</span>
                </label>
                <select name="category_id" id="category_id" required class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 focus:border-giallo focus:outline-none">
                    <option value="">Pilih Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="brand" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">
                    Merk / Brand
                </label>
                <input type="text" name="brand" id="brand" value="{{ old('brand') }}" placeholder="Contoh: Brembo / Bosch / OEM" class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 focus:border-giallo focus:outline-none">
                @error('brand') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="price_idr" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">
                    Harga Jual Satuan (IDR) <span class="text-red-500">*</span>
                </label>
                <input type="number" name="price_idr" id="price_idr" value="{{ old('price_idr') }}" required min="0" placeholder="Contoh: 1500000" class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 focus:border-giallo focus:outline-none">
                @error('price_idr') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="stock" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">
                    Stok Awal <span class="text-red-500">*</span>
                </label>
                <input type="number" name="stock" id="stock" value="{{ old('stock', 0) }}" required min="0" class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 focus:border-giallo focus:outline-none">
                @error('stock') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label for="low_stock_threshold" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">
                    Batas Minimum Stok (Peringatan Restock) <span class="text-red-500">*</span>
                </label>
                <input type="number" name="low_stock_threshold" id="low_stock_threshold" value="{{ old('low_stock_threshold', 5) }}" required min="1" class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 focus:border-giallo focus:outline-none">
                @error('low_stock_threshold') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label for="description" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">
                    Deskripsi & Catatan
                </label>
                <textarea name="description" id="description" rows="3" placeholder="Informasi spesifikasi suku cadang, kompatibilitas, atau instruksi penyimpanan..." class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 focus:border-giallo focus:outline-none">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label for="image" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">
                    Foto / Gambar Spare Part
                </label>
                <div class="flex flex-col sm:flex-row gap-4 items-start bg-steel/20 border border-gunmetal/60 rounded p-4">
                    <div id="image-preview-container" class="w-28 h-28 rounded border border-gunmetal bg-carbon flex items-center justify-center overflow-hidden flex-shrink-0 text-gunmetal">
                        <span id="preview-placeholder" class="text-3xl">⚙️</span>
                        <img id="image-preview" src="#" alt="Preview" class="w-full h-full object-cover hidden">
                    </div>
                    <div class="flex-1 space-y-2">
                        <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/jpg,image/webp"
                            onchange="previewSparePartImage(this)"
                            class="block w-full text-xs text-silver-dim file:mr-3 file:py-2 file:px-3 file:rounded file:border-0 file:text-xs file:font-heading file:uppercase file:tracking-wider file:bg-steel file:text-silver-bright hover:file:bg-giallo hover:file:text-carbon file:transition-premium cursor-pointer">
                        <p class="text-[11px] text-silver-dim">
                            Format yang didukung: JPEG, PNG, JPG, WEBP. Maksimal 8 MB. Foto akan tampil di etalase katalog suku cadang AUDrive.
                        </p>
                        @error('image') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <script>
            function previewSparePartImage(input) {
                const preview = document.getElementById('image-preview');
                const placeholder = document.getElementById('preview-placeholder');
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');
                        placeholder.classList.add('hidden');
                    }
                    reader.readAsDataURL(input.files[0]);
                } else {
                    preview.src = '#';
                    preview.classList.add('hidden');
                    placeholder.classList.remove('hidden');
                }
            }
        </script>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gunmetal">
            <a href="{{ route('admin.spare-parts.index') }}" class="px-4 py-2 border border-gunmetal text-silver-dim hover:text-silver-bright text-xs font-heading uppercase rounded">
                Batal
            </a>
            <button type="submit" class="px-5 py-2 bg-giallo hover:bg-giallo-bright text-carbon font-heading text-xs uppercase tracking-wider font-bold rounded clip-angular-button transition-premium">
                Simpan Spare Part
            </button>
        </div>
    </form>
</div>
@endsection
