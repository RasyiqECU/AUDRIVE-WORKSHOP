<?php $__env->startSection('title', 'Buat Purchase Order'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-4xl mx-auto">
    
    <div>
        <a href="<?php echo e(route('admin.purchase-orders.index')); ?>" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1 mb-2">
            ← Kembali ke Purchase Orders
        </a>
        <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Pengadaan Barang</p>
        <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
            BUAT PURCHASE ORDER BARU
        </h1>
    </div>

    
    <form action="<?php echo e(route('admin.purchase-orders.store')); ?>" method="POST" class="panel border border-gunmetal/70 bg-carbon p-6 space-y-5">
        <?php echo csrf_field(); ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="supplier_name" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">
                    Nama Supplier <span class="text-red-500">*</span>
                </label>
                <input type="text" name="supplier_name" id="supplier_name" value="<?php echo e(old('supplier_name')); ?>" required placeholder="Contoh: PT Brembo Indonesia Parts" class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 focus:border-giallo focus:outline-none">
                <?php $__errorArgs = ['supplier_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-400 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label for="notes" class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">
                    Catatan PO
                </label>
                <input type="text" name="notes" id="notes" value="<?php echo e(old('notes')); ?>" placeholder="Catatan pengiriman / instruksi khusus..." class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 focus:border-giallo focus:outline-none">
            </div>
        </div>

        
        <div class="border-t border-gunmetal/40 pt-4 space-y-3">
            <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright">Item Suku Cadang yang Dipesan</h3>
            
            <div class="p-4 bg-steel/30 border border-gunmetal/50 rounded space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[11px] text-silver-dim uppercase mb-1">Pilih Spare Part</label>
                        <select name="items[0][spare_part_id]" required class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2 focus:border-giallo focus:outline-none">
                            <option value="">Pilih Part</option>
                            <?php $__currentLoopData = $parts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $part): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($part->id); ?>"><?php echo e($part->sku); ?> - <?php echo e($part->name); ?> (Stok: <?php echo e($part->stock); ?>)</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] text-silver-dim uppercase mb-1">Jumlah Order (Qty)</label>
                        <input type="number" name="items[0][qty]" min="1" value="1" required class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2 focus:border-giallo focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] text-silver-dim uppercase mb-1">Harga Beli Satuan (IDR)</label>
                        <input type="number" name="items[0][cost_idr]" min="0" value="0" required class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2 focus:border-giallo focus:outline-none">
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-4 flex justify-end gap-3 border-t border-gunmetal/40">
            <a href="<?php echo e(route('admin.purchase-orders.index')); ?>" class="px-4 py-2 border border-gunmetal text-silver-dim hover:text-silver-bright text-xs font-heading uppercase rounded">
                Batal
            </a>
            <button type="submit" class="px-5 py-2 bg-giallo hover:bg-giallo-bright text-carbon font-heading text-xs uppercase tracking-wider font-bold rounded clip-angular-button">
                Simpan Purchase Order
            </button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/admin/purchase-orders/create.blade.php ENDPATH**/ ?>