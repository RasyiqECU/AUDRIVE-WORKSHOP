<?php $__env->startSection('title', 'Edit Spare Part - ' . $sparePart->name); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <a href="<?php echo e(route('admin.spare-parts.index')); ?>" class="text-silver-dim hover:text-giallo transition-premium font-heading uppercase tracking-wider">
        Katalog Spare Part
    </a>
    <span class="text-gunmetal">/</span>
    <span class="text-giallo font-heading uppercase tracking-wider">Edit</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-4xl mx-auto">
    
    <div>
        <a href="<?php echo e(route('admin.spare-parts.index')); ?>" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1 mb-2">
            ← Kembali ke Katalog Spare Part
        </a>
        <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Perbarui Data</p>
        <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
            EDIT SPART PART: <?php echo e($sparePart->name); ?>

        </h1>
        <p class="text-xs text-silver-dim mt-1">
            Perbarui spesifikasi, harga satuan, dan batas stok spare part.
        </p>
    </div>

    
    <form action="<?php echo e(route('admin.spare-parts.update', $sparePart)); ?>" method="POST" enctype="multipart/form-data" class="panel border border-gunmetal/70 bg-carbon p-6 space-y-4">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="sku" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">
                    SKU / Part Number <span class="text-red-500">*</span>
                </label>
                <input type="text" name="sku" id="sku" value="<?php echo e(old('sku', $sparePart->sku)); ?>" required class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 focus:border-giallo focus:outline-none">
                <?php $__errorArgs = ['sku'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-400 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label for="name" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">
                    Nama Spare Part <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="<?php echo e(old('name', $sparePart->name)); ?>" required class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 focus:border-giallo focus:outline-none">
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-400 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label for="category_id" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">
                    Kategori <span class="text-red-500">*</span>
                </label>
                <select name="category_id" id="category_id" required class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 focus:border-giallo focus:outline-none">
                    <option value="">Pilih Kategori</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cat->id); ?>" <?php echo e(old('category_id', $sparePart->category_id) == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-400 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label for="brand" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">
                    Merk / Brand
                </label>
                <input type="text" name="brand" id="brand" value="<?php echo e(old('brand', $sparePart->brand)); ?>" class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 focus:border-giallo focus:outline-none">
                <?php $__errorArgs = ['brand'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-400 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label for="price_idr" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">
                    Harga Jual Satuan (IDR) <span class="text-red-500">*</span>
                </label>
                <input type="number" name="price_idr" id="price_idr" value="<?php echo e(old('price_idr', $sparePart->price_idr)); ?>" required min="0" class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 focus:border-giallo focus:outline-none">
                <?php $__errorArgs = ['price_idr'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-400 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label for="stock" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">
                    Stok Saat Ini <span class="text-red-500">*</span>
                </label>
                <input type="number" name="stock" id="stock" value="<?php echo e(old('stock', $sparePart->stock)); ?>" required min="0" class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 focus:border-giallo focus:outline-none">
                <?php $__errorArgs = ['stock'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-400 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="md:col-span-2">
                <label for="low_stock_threshold" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">
                    Batas Minimum Stok (Peringatan Restock) <span class="text-red-500">*</span>
                </label>
                <input type="number" name="low_stock_threshold" id="low_stock_threshold" value="<?php echo e(old('low_stock_threshold', $sparePart->low_stock_threshold)); ?>" required min="1" class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 focus:border-giallo focus:outline-none">
                <?php $__errorArgs = ['low_stock_threshold'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-400 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="md:col-span-2">
                <label for="description" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">
                    Deskripsi & Catatan
                </label>
                <textarea name="description" id="description" rows="3" class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 focus:border-giallo focus:outline-none"><?php echo e(old('description', $sparePart->description)); ?></textarea>
                <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-400 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div class="md:col-span-2">
                <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">
                    Foto / Gambar Suku Cadang
                </label>
                <div class="bg-steel/20 border border-gunmetal/60 rounded p-4 space-y-4">
                    <div class="flex flex-col sm:flex-row gap-4 items-start">
                        
                        <div class="relative w-32 h-32 rounded border border-gunmetal bg-carbon flex items-center justify-center overflow-hidden flex-shrink-0">
                            <?php if($sparePart->image): ?>
                                <img id="image-current" src="<?php echo e(asset('storage/' . $sparePart->image)); ?>" alt="<?php echo e($sparePart->name); ?>" class="w-full h-full object-cover">
                                <img id="image-new-preview" src="#" alt="New Preview" class="w-full h-full object-cover hidden">
                            <?php else: ?>
                                <span id="placeholder-icon" class="text-4xl text-gunmetal">⚙️</span>
                                <img id="image-new-preview" src="#" alt="New Preview" class="w-full h-full object-cover hidden">
                            <?php endif; ?>
                        </div>

                        
                        <div class="flex-1 space-y-3">
                            <div>
                                <label for="image" class="block text-xs font-semibold text-silver-bright mb-1">
                                    <?php echo e($sparePart->image ? 'Ganti dengan Gambar Baru' : 'Unggah Gambar Suku Cadang'); ?>

                                </label>
                                <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/jpg,image/webp"
                                    onchange="previewEditImage(this)"
                                    class="block w-full text-xs text-silver-dim file:mr-3 file:py-2 file:px-3 file:rounded file:border-0 file:text-xs file:font-heading file:uppercase file:tracking-wider file:bg-steel file:text-silver-bright hover:file:bg-giallo hover:file:text-carbon file:transition-premium cursor-pointer">
                                <p class="text-[11px] text-silver-dim mt-1">
                                    Format: JPEG, PNG, JPG, WEBP. Maks: 8 MB.
                                </p>
                                <?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-400 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <?php if($sparePart->image): ?>
                                <div class="pt-2 border-t border-gunmetal/40 flex flex-wrap items-center gap-3">
                                    <button type="button" onclick="if(confirm('Hapus gambar ini sekarang?')) document.getElementById('delete-image-form').submit();"
                                        class="px-3 py-1.5 bg-red-950/60 hover:bg-red-900 border border-red-700/60 text-red-300 font-heading text-[11px] uppercase tracking-wider rounded transition-premium flex items-center gap-1.5">
                                        🗑️ Hapus Gambar Sekarang
                                    </button>
                                    <label class="inline-flex items-center gap-2 text-xs text-silver-dim cursor-pointer hover:text-silver-bright">
                                        <input type="checkbox" name="remove_image" value="1" class="rounded border-gunmetal bg-steel text-giallo focus:ring-0">
                                        <span>Centang untuk menghapus gambar saat formulir ini disimpan</span>
                                    </label>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function previewEditImage(input) {
                const newPreview = document.getElementById('image-new-preview');
                const currentImg = document.getElementById('image-current');
                const placeholder = document.getElementById('placeholder-icon');

                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        newPreview.src = e.target.result;
                        newPreview.classList.remove('hidden');
                        if (currentImg) currentImg.classList.add('hidden');
                        if (placeholder) placeholder.classList.add('hidden');
                    }
                    reader.readAsDataURL(input.files[0]);
                } else {
                    newPreview.src = '#';
                    newPreview.classList.add('hidden');
                    if (currentImg) currentImg.classList.remove('hidden');
                    if (placeholder) placeholder.classList.remove('hidden');
                }
            }
        </script>

        <div class="flex items-center justify-between pt-4 border-t border-gunmetal">
            <button type="button" onclick="if(confirm('Yakin ingin menghapus spare part ini?')) document.getElementById('delete-part-form').submit();" class="text-red-400 hover:text-red-300 text-xs font-heading uppercase">
                Hapus Spare Part
            </button>
            <div class="flex items-center gap-3">
                <a href="<?php echo e(route('admin.spare-parts.index')); ?>" class="px-4 py-2 border border-gunmetal text-silver-dim hover:text-silver-bright text-xs font-heading uppercase rounded">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2 bg-giallo hover:bg-giallo-bright text-carbon font-heading text-xs uppercase tracking-wider font-bold rounded clip-angular-button transition-premium">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>

    <form id="delete-part-form" action="<?php echo e(route('admin.spare-parts.destroy', $sparePart)); ?>" method="POST" class="hidden">
        <?php echo csrf_field(); ?>
        <?php echo method_field('DELETE'); ?>
    </form>

    <?php if($sparePart->image): ?>
        <form id="delete-image-form" action="<?php echo e(route('admin.spare-parts.image.destroy', $sparePart)); ?>" method="POST" class="hidden">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
        </form>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/admin/spare-parts/edit.blade.php ENDPATH**/ ?>