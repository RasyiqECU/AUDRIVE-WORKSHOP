<?php $__env->startSection('title', 'Edit Voucher ' . $voucher->code); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <a href="<?php echo e(route('admin.vouchers.index')); ?>" class="text-silver-dim hover:text-giallo font-heading uppercase tracking-wider">Voucher</a>
    <span class="text-silver-dim">/</span>
    <span class="text-giallo font-heading uppercase tracking-wider">Edit <?php echo e($voucher->code); ?></span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="<?php echo e(route('admin.vouchers.index')); ?>" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1 mb-2 transition-colors">
            ← Kembali ke Daftar Voucher
        </a>
        <h1 class="font-display text-2xl text-silver-bright font-bold tracking-wide">
            EDIT VOUCHER: <?php echo e($voucher->code); ?>

        </h1>
        <p class="text-xs text-silver-dim mt-1">
            Ubah pengaturan kupon, nilai diskon, kuota pemakaian, dan status aktif.
        </p>
    </div>

    <div class="panel border border-gunmetal/70 bg-carbon p-6">
        <form action="<?php echo e(route('admin.vouchers.update', $voucher)); ?>" method="POST" class="space-y-5">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <div>
                    <label class="block text-xs font-heading uppercase text-silver-bright mb-1">
                        Kode Voucher <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="code" value="<?php echo e(old('code', $voucher->code)); ?>" required 
                           class="w-full bg-steel/60 border border-gunmetal text-silver-bright font-mono uppercase text-sm rounded p-3 focus:border-giallo focus:outline-none">
                </div>

                
                <?php $currentType = old('type', $voucher->type->value ?? $voucher->type); ?>
                <div>
                    <label class="block text-xs font-heading uppercase text-silver-bright mb-1">
                        Tipe Potongan <span class="text-danger">*</span>
                    </label>
                    <select name="type" required class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-sm rounded p-3 focus:border-giallo focus:outline-none">
                        <option value="fixed" <?php echo e($currentType === 'fixed' ? 'selected' : ''); ?>>Nominal Tetap (Rp)</option>
                        <option value="percent" <?php echo e($currentType === 'percent' ? 'selected' : ''); ?>>Persentase (%)</option>
                    </select>
                </div>

                
                <div>
                    <label class="block text-xs font-heading uppercase text-silver-bright mb-1">
                        Besar Potongan <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="value" value="<?php echo e(old('value', $voucher->value)); ?>" required min="1" 
                           class="w-full bg-steel/60 border border-gunmetal text-silver-bright font-mono text-sm rounded p-3 focus:border-giallo focus:outline-none">
                </div>

                
                <div>
                    <label class="block text-xs font-heading uppercase text-silver-bright mb-1">
                        Biaya Tukar Poin Loyalty
                    </label>
                    <input type="number" name="points_cost" value="<?php echo e(old('points_cost', $voucher->points_cost)); ?>" min="0" 
                           class="w-full bg-steel/60 border border-gunmetal text-silver-bright font-mono text-sm rounded p-3 focus:border-giallo focus:outline-none">
                </div>

                
                <div>
                    <label class="block text-xs font-heading uppercase text-silver-bright mb-1">
                        Minimal Belanja / Servis (IDR)
                    </label>
                    <input type="number" name="min_purchase_idr" value="<?php echo e(old('min_purchase_idr', $voucher->min_purchase_idr)); ?>" min="0" 
                           class="w-full bg-steel/60 border border-gunmetal text-silver-bright font-mono text-sm rounded p-3 focus:border-giallo focus:outline-none">
                </div>

                
                <div>
                    <label class="block text-xs font-heading uppercase text-silver-bright mb-1">
                        Batas Kuota Penggunaan <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="max_usage" value="<?php echo e(old('max_usage', $voucher->max_usage)); ?>" required min="1" 
                           class="w-full bg-steel/60 border border-gunmetal text-silver-bright font-mono text-sm rounded p-3 focus:border-giallo focus:outline-none">
                    <p class="text-[10px] text-silver-dim mt-1">Saat ini telah terpakai: <?php echo e($voucher->used_count); ?> kali.</p>
                </div>

                
                <div>
                    <label class="block text-xs font-heading uppercase text-silver-bright mb-1">
                        Tanggal Kadaluarsa
                    </label>
                    <input type="datetime-local" name="expires_at" value="<?php echo e(old('expires_at', $voucher->expires_at ? $voucher->expires_at->format('Y-m-d\TH:i') : '')); ?>" 
                           class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-3 focus:border-giallo focus:outline-none">
                </div>

                
                <div class="flex items-center pt-6">
                    <label class="relative flex items-center gap-3 cursor-pointer select-none">
                        <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', $voucher->is_active) ? 'checked' : ''); ?> class="w-5 h-5 rounded border-gunmetal bg-steel text-giallo focus:ring-0">
                        <div>
                            <span class="font-heading text-xs uppercase text-silver-bright font-bold">Voucher Aktif</span>
                            <p class="text-[10px] text-silver-dim">Dapat digunakan pelanggan & kasir POS.</p>
                        </div>
                    </label>
                </div>
            </div>

            
            <div>
                <label class="block text-xs font-heading uppercase text-silver-bright mb-1">
                    Deskripsi & Syarat Ketentuan
                </label>
                <textarea name="description" rows="3" class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-3 focus:border-giallo focus:outline-none"><?php echo e(old('description', $voucher->description)); ?></textarea>
            </div>

            
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gunmetal/60">
                <a href="<?php echo e(route('admin.vouchers.index')); ?>" class="px-5 py-2.5 bg-steel hover:bg-gunmetal text-silver-dim hover:text-silver-bright text-xs font-heading uppercase rounded transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-giallo hover:bg-giallo-bright text-carbon font-heading text-xs uppercase font-bold rounded clip-angular-button transition-colors">
                    Perbarui Voucher
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/admin/vouchers/edit.blade.php ENDPATH**/ ?>