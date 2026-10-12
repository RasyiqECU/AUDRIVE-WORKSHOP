<?php $__env->startSection('title', 'Edit Unit — ' . $car->brand . ' ' . $car->model); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-5xl mx-auto">
    <div class="flex items-center justify-between">
        <a href="<?php echo e(route('admin.cars.show', $car)); ?>" class="inline-flex items-center gap-2 font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider transition-premium">
            <span>&larr;</span> Kembali ke Detail Unit
        </a>
    </div>

    <div>
        <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">
            EDIT UNIT MOBIL
        </h1>
        <p class="text-xs text-silver-dim mt-1">
            Perbarui data unit <?php echo e($car->code); ?> — <?php echo e($car->brand); ?> <?php echo e($car->model); ?> (<?php echo e($car->year); ?>)
        </p>
    </div>

    <div class="panel border border-gunmetal bg-carbon overflow-hidden">
        <form action="<?php echo e(route('admin.cars.update', $car)); ?>" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Merk / Brand *</label>
                    <input type="text" name="brand" required value="<?php echo e(old('brand', $car->brand)); ?>" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                </div>
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Model / Seri *</label>
                    <input type="text" name="model" required value="<?php echo e(old('model', $car->model)); ?>" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                </div>
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Tahun Pembuatan *</label>
                    <input type="number" name="year" required min="1950" max="<?php echo e(date('Y') + 1); ?>" value="<?php echo e(old('year', $car->year)); ?>" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                </div>
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Transmisi *</label>
                    <select name="transmission" required class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                        <option value="automatic" <?php echo e(old('transmission', $car->transmission) === 'automatic' ? 'selected' : ''); ?>>Automatic</option>
                        <option value="manual" <?php echo e(old('transmission', $car->transmission) === 'manual' ? 'selected' : ''); ?>>Manual</option>
                        <option value="cvt" <?php echo e(old('transmission', $car->transmission) === 'cvt' ? 'selected' : ''); ?>>CVT</option>
                    </select>
                </div>
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Jarak Tempuh (KM) *</label>
                    <input type="number" name="mileage_km" required min="0" value="<?php echo e(old('mileage_km', $car->mileage_km)); ?>" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                </div>
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Warna Utama</label>
                    <input type="text" name="color" value="<?php echo e(old('color', $car->color)); ?>" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                </div>
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Harga Beli (Modal IDR)</label>
                    <input type="number" name="price_buy_idr" min="0" value="<?php echo e(old('price_buy_idr', $car->price_buy_idr)); ?>" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                </div>
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Harga Jual (Listing IDR) *</label>
                    <input type="number" name="price_sell_idr" required min="0" value="<?php echo e(old('price_sell_idr', $car->price_sell_idr)); ?>" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                </div>
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Status Unit *</label>
                    <select name="stock_status" required class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                        <?php $currentStatus = old('stock_status', $car->stock_status instanceof \BackedEnum ? $car->stock_status->value : $car->stock_status); ?>
                        <option value="available" <?php echo e($currentStatus === 'available' ? 'selected' : ''); ?>>Tersedia (Available)</option>
                        <option value="reserved" <?php echo e($currentStatus === 'reserved' ? 'selected' : ''); ?>>Dipesan (Reserved)</option>
                        <option value="sold" <?php echo e($currentStatus === 'sold' ? 'selected' : ''); ?>>Terjual (Sold)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Deskripsi</label>
                <textarea name="description" rows="4" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none"><?php echo e(old('description', $car->description)); ?></textarea>
            </div>

            <div>
                <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-2">Upload Foto Tambahan</label>
                <input type="file" name="photos[]" multiple accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-dim">
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gunmetal">
                <a href="<?php echo e(route('admin.cars.show', $car)); ?>" class="px-5 py-2.5 border border-gunmetal text-silver hover:border-silver-dim text-xs uppercase tracking-wider">
                    Batal
                </a>
                <button type="submit" class="px-7 py-2.5 bg-giallo hover:bg-arancio text-nero font-heading font-bold text-xs uppercase tracking-wider transition-premium shadow-lg shadow-giallo/20 clip-angular-button">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/admin/cars/edit.blade.php ENDPATH**/ ?>