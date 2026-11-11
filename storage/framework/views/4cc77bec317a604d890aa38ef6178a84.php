<?php $__env->startSection('title', 'Edit Spare Part ' . $sparePart->sku); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-4xl mx-auto">
    
    <div>
        <a href="<?php echo e(route('admin.inventory.index')); ?>" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1 mb-2">
            ← Kembali ke Daftar Inventaris
        </a>
        <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Perbarui Data</p>
        <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
            EDIT SPART PART: <?php echo e($sparePart->name); ?>

        </h1>
    </div>

    
    <form action="<?php echo e(route('admin.inventory.update', $sparePart->id)); ?>" method="POST" class="panel border border-gunmetal/70 bg-carbon p-6 space-y-4">
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
                    Harga Jual (IDR) <span class="text-red-500">*</span>
                </label>
                <input type="number" name="price_idr" id="price_idr" value="<?php echo e(old('price_idr', $sparePart->price_idr)); ?>" min="0" required class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 focus:border-giallo focus:outline-none">
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
                    Jumlah Stok Saat Ini <span class="text-red-500">*</span>
                </label>
                <input type="number" name="stock" id="stock" value="<?php echo e(old('stock', $sparePart->stock)); ?>" min="0" required class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 focus:border-giallo focus:outline-none">
                <?php $__errorArgs = ['stock'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-400 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label for="low_stock_threshold" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">
                    Batas Minimum Stok (Alert) <span class="text-red-500">*</span>
                </label>
                <input type="number" name="low_stock_threshold" id="low_stock_threshold" value="<?php echo e(old('low_stock_threshold', $sparePart->low_stock_threshold)); ?>" min="0" required class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 focus:border-giallo focus:outline-none">
                <?php $__errorArgs = ['low_stock_threshold'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-400 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        <div>
            <label for="description" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">
                Deskripsi / Catatan Part
            </label>
            <textarea name="description" id="description" rows="3" class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-3 focus:border-giallo focus:outline-none"><?php echo e(old('description', $sparePart->description)); ?></textarea>
        </div>

        <div class="pt-4 flex justify-end gap-3 border-t border-gunmetal/40">
            <a href="<?php echo e(route('admin.inventory.index')); ?>" class="px-4 py-2 border border-gunmetal text-silver-dim hover:text-silver-bright text-xs font-heading uppercase rounded">
                Batal
            </a>
            <button type="submit" class="px-5 py-2 bg-giallo hover:bg-giallo-bright text-carbon font-heading text-xs uppercase tracking-wider font-bold rounded clip-angular-button">
                Perbarui Spare Part
            </button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/admin/inventory/edit.blade.php ENDPATH**/ ?>