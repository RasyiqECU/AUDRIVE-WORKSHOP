@extends('layouts.admin')

@section('title', 'Edit Unit — ' . $car->brand . ' ' . $car->model)

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.cars.show', $car) }}" class="inline-flex items-center gap-2 font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider transition-premium">
            <span>&larr;</span> Kembali ke Detail Unit
        </a>
    </div>

    <div>
        <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">
            EDIT UNIT MOBIL
        </h1>
        <p class="text-xs text-silver-dim mt-1">
            Perbarui data unit {{ $car->code }} — {{ $car->brand }} {{ $car->model }} ({{ $car->year }})
        </p>
    </div>

    <div class="panel border border-gunmetal bg-carbon overflow-hidden">
        <form action="{{ route('admin.cars.update', $car) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Merk / Brand *</label>
                    <input type="text" name="brand" required value="{{ old('brand', $car->brand) }}" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                </div>
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Model / Seri *</label>
                    <input type="text" name="model" required value="{{ old('model', $car->model) }}" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                </div>
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Tahun Pembuatan *</label>
                    <input type="number" name="year" required min="1950" max="{{ date('Y') + 1 }}" value="{{ old('year', $car->year) }}" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                </div>
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Transmisi *</label>
                    <select name="transmission" required class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                        <option value="automatic" {{ old('transmission', $car->transmission) === 'automatic' ? 'selected' : '' }}>Automatic</option>
                        <option value="manual" {{ old('transmission', $car->transmission) === 'manual' ? 'selected' : '' }}>Manual</option>
                        <option value="cvt" {{ old('transmission', $car->transmission) === 'cvt' ? 'selected' : '' }}>CVT</option>
                    </select>
                </div>
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Jarak Tempuh (KM) *</label>
                    <input type="number" name="mileage_km" required min="0" value="{{ old('mileage_km', $car->mileage_km) }}" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                </div>
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Warna Utama</label>
                    <input type="text" name="color" value="{{ old('color', $car->color) }}" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                </div>
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Harga Beli (Modal IDR)</label>
                    <input type="number" name="price_buy_idr" min="0" value="{{ old('price_buy_idr', $car->price_buy_idr) }}" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                </div>
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Harga Jual (Listing IDR) *</label>
                    <input type="number" name="price_sell_idr" required min="0" value="{{ old('price_sell_idr', $car->price_sell_idr) }}" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                </div>
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Status Unit *</label>
                    <select name="stock_status" required class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                        @php $currentStatus = old('stock_status', $car->stock_status instanceof \BackedEnum ? $car->stock_status->value : $car->stock_status); @endphp
                        <option value="available" {{ $currentStatus === 'available' ? 'selected' : '' }}>Tersedia (Available)</option>
                        <option value="reserved" {{ $currentStatus === 'reserved' ? 'selected' : '' }}>Dipesan (Reserved)</option>
                        <option value="sold" {{ $currentStatus === 'sold' ? 'selected' : '' }}>Terjual (Sold)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Deskripsi</label>
                <textarea name="description" rows="4" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">{{ old('description', $car->description) }}</textarea>
            </div>

            <div>
                <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Deskripsi</label>
                <textarea name="description" rows="4" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">{{ old('description', $car->description) }}</textarea>
            </div>

            <div>
                <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Unggah Foto Tambahan</label>
                <input type="file" name="photos[]" multiple accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-dim file:mr-3 file:py-1 file:px-3 file:border-0 file:text-xs file:font-heading file:uppercase file:bg-steel file:text-silver-bright hover:file:bg-giallo hover:file:text-nero cursor-pointer">
                <p class="text-[11px] text-silver-dim mt-1 font-mono">Format didukung: JPG, PNG, WEBP (Bisa pilih beberapa foto sekaligus)</p>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gunmetal">
                <a href="{{ route('admin.cars.show', $car) }}" class="px-5 py-2.5 border border-gunmetal text-silver hover:border-silver-dim text-xs uppercase tracking-wider">
                    Batal
                </a>
                <button type="submit" class="px-7 py-2.5 bg-giallo hover:bg-arancio text-nero font-heading font-bold text-xs uppercase tracking-wider transition-premium shadow-lg shadow-giallo/20 clip-angular-button">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    {{-- Kelola Galeri Foto Unit Saat Ini (Edit / Hapus / Set Utama) --}}
    <div class="panel border border-gunmetal bg-carbon p-6 space-y-6">
        <div class="border-b border-gunmetal pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="font-heading text-sm uppercase tracking-wider text-giallo font-bold flex items-center gap-2">
                    <span>🖼️</span> Galeri Foto Unit Saat Ini ({{ $car->photos->count() }} Foto)
                </h2>
                <p class="text-xs text-silver-dim mt-0.5">
                    Hapus foto, ganti file foto tertentu, atau tentukan foto mana yang menjadi cover utama di marketplace & sales.
                </p>
            </div>
        </div>

        @if($car->photos->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($car->photos as $photo)
                    <div class="panel border {{ $photo->is_primary ? 'border-giallo shadow-lg shadow-giallo/10' : 'border-gunmetal' }} bg-nero p-3 space-y-3 relative group">
                        {{-- Photo Box --}}
                        <div class="aspect-video bg-steel/20 border border-gunmetal overflow-hidden rounded relative">
                            <img src="{{ asset('storage/' . $photo->path) }}" 
                                 alt="{{ $car->model }}" 
                                 class="w-full h-full object-cover"
                                 onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center text-silver-dim text-xs\'>Gambar Rusak</div>';">
                            
                            @if($photo->is_primary)
                                <div class="absolute top-2 left-2 px-2 py-0.5 bg-giallo text-nero font-heading text-[9px] font-bold uppercase tracking-wider rounded shadow">
                                    ⭐ Foto Utama
                                </div>
                            @endif
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center justify-between gap-2 pt-1 border-t border-gunmetal/60">
                            @if(!$photo->is_primary)
                                <form action="{{ route('admin.cars.photos.primary', [$car, $photo]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-2 py-1 bg-steel/60 hover:bg-giallo hover:text-nero text-[10px] font-heading uppercase text-silver-bright rounded transition-premium">
                                        Set Utama
                                    </button>
                                </form>
                            @else
                                <span class="text-[10px] text-giallo font-mono">Tampil di Katalog</span>
                            @endif

                            <div class="flex items-center gap-2">
                                {{-- Tombol Ganti Foto (Toggle form pengganti) --}}
                                <button type="button" 
                                        onclick="toggleReplaceModal({{ $photo->id }})" 
                                        class="px-2 py-1 bg-steel/60 hover:bg-steel text-silver-dim hover:text-silver-bright text-[10px] font-heading uppercase rounded transition-premium"
                                        title="Ganti file foto ini">
                                    Ganti
                                </button>

                                {{-- Tombol Hapus --}}
                                <form action="{{ route('admin.cars.photos.destroy', [$car, $photo]) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus foto ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2 py-1 bg-red-950/60 hover:bg-red-900 border border-red-800 text-red-300 text-[10px] font-heading uppercase rounded transition-premium" title="Hapus foto ini">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Form Ganti Foto (Tersembunyi sampai tombol 'Ganti' diklik) --}}
                        <div id="replace-box-{{ $photo->id }}" class="hidden pt-3 border-t border-gunmetal space-y-2">
                            <form action="{{ route('admin.cars.photos.replace', [$car, $photo]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <label class="block text-[10px] font-heading uppercase tracking-wider text-giallo">Pilih File Pengganti:</label>
                                <input type="file" name="photo" required accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp" class="w-full text-[10px] text-silver-dim bg-nero border border-gunmetal p-1.5 focus:border-giallo focus:outline-none rounded">
                                <div class="flex items-center justify-end gap-2 mt-2">
                                    <button type="button" onclick="toggleReplaceModal({{ $photo->id }})" class="text-[10px] text-silver-dim hover:text-silver-bright px-2 py-1">Batal</button>
                                    <button type="submit" class="px-3 py-1 bg-giallo hover:bg-arancio text-nero text-[10px] font-heading font-bold uppercase rounded">Upload Ganti</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-8 bg-nero border border-gunmetal text-center text-silver-dim text-xs">
                Belum ada foto untuk unit mobil ini. Gunakan kolom upload di form atas untuk menambahkan foto.
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function toggleReplaceModal(photoId) {
        const box = document.getElementById('replace-box-' + photoId);
        if (box) {
            box.classList.toggle('hidden');
        }
    }
</script>
@endpush
@endsection
