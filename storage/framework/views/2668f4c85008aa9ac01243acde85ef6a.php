<?php $__env->startSection('title', 'Tambah Unit Mobil Baru'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-5xl mx-auto">
    <div class="flex items-center justify-between">
        <a href="<?php echo e(route('admin.cars.index')); ?>" class="inline-flex items-center gap-2 font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider transition-premium">
            <span>&larr;</span> Kembali ke Daftar Mobil
        </a>
    </div>

    <div>
        <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">
            TAMBAH UNIT MOBIL BARU
        </h1>
        <p class="text-xs text-silver-dim mt-1">
            Input spesifikasi lengkap unit supercar / mobil baru ke inventori showroom AUDrive
        </p>
    </div>

    <div class="panel border border-gunmetal bg-carbon overflow-hidden">
        <form action="<?php echo e(route('admin.cars.store')); ?>" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            <?php echo csrf_field(); ?>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Merk / Brand *</label>
                    <input type="text" name="brand" required value="<?php echo e(old('brand')); ?>" placeholder="Contoh: Lamborghini" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                </div>
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Model / Seri *</label>
                    <input type="text" name="model" required value="<?php echo e(old('model')); ?>" placeholder="Contoh: Huracán STO" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                </div>
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Tahun Pembuatan *</label>
                    <input type="number" name="year" required min="1950" max="<?php echo e(date('Y') + 1); ?>" value="<?php echo e(old('year', date('Y'))); ?>" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                </div>
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Transmisi *</label>
                    <select name="transmission" required class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                        <option value="" disabled <?php echo e(old('transmission') ? '' : 'selected'); ?>>-- Pilih Transmisi --</option>
                        <option value="automatic" <?php echo e(old('transmission') === 'automatic' ? 'selected' : ''); ?>>Automatic</option>
                        <option value="manual" <?php echo e(old('transmission') === 'manual' ? 'selected' : ''); ?>>Manual</option>
                        <option value="cvt" <?php echo e(old('transmission') === 'cvt' ? 'selected' : ''); ?>>CVT</option>
                    </select>
                </div>
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Jarak Tempuh (KM) *</label>
                    <input type="number" name="mileage_km" required min="0" value="<?php echo e(old('mileage_km', 0)); ?>" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                </div>
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Warna Utama</label>
                    <input type="text" name="color" value="<?php echo e(old('color')); ?>" placeholder="Contoh: Giallo Belenus" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                </div>
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Harga Beli (Modal IDR)</label>
                    <input type="number" name="price_buy_idr" min="0" value="<?php echo e(old('price_buy_idr')); ?>" placeholder="0" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                </div>
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Harga Jual (Listing IDR) *</label>
                    <input type="number" name="price_sell_idr" required min="0" value="<?php echo e(old('price_sell_idr')); ?>" placeholder="0" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                </div>
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Status Unit *</label>
                    <select name="stock_status" required class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                        <option value="available" <?php echo e(old('stock_status', 'available') === 'available' ? 'selected' : ''); ?>>Tersedia (Available)</option>
                        <option value="reserved" <?php echo e(old('stock_status') === 'reserved' ? 'selected' : ''); ?>>Dipesan (Reserved)</option>
                        <option value="sold" <?php echo e(old('stock_status') === 'sold' ? 'selected' : ''); ?>>Terjual (Sold)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Deskripsi & Kelengkapan</label>
                <textarea name="description" rows="4" placeholder="Deskripsi spesifikasi mesin, opsi velg, jok, knalpot akrapovic, paket carbon..." class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none"><?php echo e(old('description')); ?></textarea>
            </div>

            <div>
                <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Foto-Foto Unit (Multiple)</label>
                <input type="file" name="photos[]" multiple accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-dim">
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gunmetal">
                <a href="<?php echo e(route('admin.cars.index')); ?>" class="px-5 py-2.5 border border-gunmetal text-silver hover:border-silver-dim text-xs uppercase tracking-wider">
                    Batal
                </a>
                <button type="submit" class="px-7 py-2.5 bg-giallo hover:bg-arancio text-nero font-heading font-bold text-xs uppercase tracking-wider transition-premium shadow-lg shadow-giallo/20 clip-angular-button">
                    Simpan Unit Mobil
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/admin/cars/create.blade.php ENDPATH**/ ?>