<?php $__env->startSection('title', 'Buat Kampanye Promo Baru'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <a href="<?php echo e(route('admin.promotions.index')); ?>" class="text-silver-dim hover:text-giallo font-heading uppercase tracking-wider">Promo</a>
    <span class="text-silver-dim">/</span>
    <span class="text-giallo font-heading uppercase tracking-wider">Tambah Baru</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="<?php echo e(route('admin.promotions.index')); ?>" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1 mb-2 transition-colors">
            ← Kembali ke Daftar Promo
        </a>
        <h1 class="font-display text-2xl text-silver-bright font-bold tracking-wide">
            BUAT EVENT / KAMPANYE PROMO BARU
        </h1>
        <p class="text-xs text-silver-dim mt-1">
            Publikasikan penawaran spesial untuk menarik transaksi servis atau pembelian unit supercar di AUDrive.
        </p>
    </div>

    <div class="panel border border-gunmetal/70 bg-carbon p-6">
        <form action="<?php echo e(route('admin.promotions.store')); ?>" method="POST" class="space-y-5">
            <?php echo csrf_field(); ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <div class="sm:col-span-2">
                    <label class="block text-xs font-heading uppercase text-silver-bright mb-1">
                        Judul / Nama Kampanye Promo <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="name" value="<?php echo e(old('name')); ?>" required placeholder="Contoh: Promo Servis Supercar Spesial Mudik, Cashback Part Ferrari..." 
                           class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-sm rounded p-3 focus:border-giallo focus:outline-none placeholder-silver-dim">
                </div>

                
                <div>
                    <label class="block text-xs font-heading uppercase text-silver-bright mb-1">
                        Tipe Potongan <span class="text-danger">*</span>
                    </label>
                    <select name="type" required class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-sm rounded p-3 focus:border-giallo focus:outline-none">
                        <option value="fixed" <?php echo e(old('type') === 'fixed' ? 'selected' : ''); ?>>Nominal Tetap (Rp)</option>
                        <option value="percent" <?php echo e(old('type') === 'percent' ? 'selected' : ''); ?>>Persentase (%)</option>
                    </select>
                </div>

                
                <div>
                    <label class="block text-xs font-heading uppercase text-silver-bright mb-1">
                        Besar Potongan <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="value" value="<?php echo e(old('value')); ?>" required min="1" placeholder="Contoh: 15 atau 250000" 
                           class="w-full bg-steel/60 border border-gunmetal text-silver-bright font-mono text-sm rounded p-3 focus:border-giallo focus:outline-none">
                </div>

                
                <div class="sm:col-span-2">
                    <label class="block text-xs font-heading uppercase text-silver-bright mb-1">
                        Kategori / Layanan yang Berlaku
                    </label>
                    <input type="text" name="applicable_to" value="<?php echo e(old('applicable_to', 'Semua Layanan & Unit')); ?>" placeholder="Contoh: Servis Berkala, Penggantian Oli & Filter, Pembelian Unit..." 
                           class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-3 focus:border-giallo focus:outline-none">
                </div>

                
                <div>
                    <label class="block text-xs font-heading uppercase text-silver-bright mb-1">
                        Tanggal Mulai Tayang <span class="text-danger">*</span>
                    </label>
                    <input type="datetime-local" name="starts_at" value="<?php echo e(old('starts_at', now()->format('Y-m-d\TH:i'))); ?>" required 
                           class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-3 focus:border-giallo focus:outline-none">
                </div>

                
                <div>
                    <label class="block text-xs font-heading uppercase text-silver-bright mb-1">
                        Tanggal Selesai <span class="text-danger">*</span>
                    </label>
                    <input type="datetime-local" name="ends_at" value="<?php echo e(old('ends_at', now()->addMonth()->format('Y-m-d\TH:i'))); ?>" required 
                           class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-3 focus:border-giallo focus:outline-none">
                </div>

                
                <div class="sm:col-span-2 pt-2">
                    <label class="relative flex items-center gap-3 cursor-pointer select-none">
                        <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', true) ? 'checked' : ''); ?> class="w-5 h-5 rounded border-gunmetal bg-steel text-giallo focus:ring-0">
                        <div>
                            <span class="font-heading text-xs uppercase text-silver-bright font-bold">Publikasikan Promo Sekarang</span>
                            <p class="text-[10px] text-silver-dim">Jika dicentang, banner promo langsung tayang di portal customer dan halaman utama.</p>
                        </div>
                    </label>
                </div>
            </div>

            
            <div>
                <label class="block text-xs font-heading uppercase text-silver-bright mb-1">
                    Detail & Syarat Ketentuan Promo
                </label>
                <textarea name="description" rows="4" placeholder="Jelaskan keuntungan promo, kuota kendaraan, dan syarat klaim..." 
                          class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-3 focus:border-giallo focus:outline-none placeholder-silver-dim leading-relaxed"><?php echo e(old('description')); ?></textarea>
            </div>

            
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gunmetal/60">
                <a href="<?php echo e(route('admin.promotions.index')); ?>" class="px-5 py-2.5 bg-steel hover:bg-gunmetal text-silver-dim hover:text-silver-bright text-xs font-heading uppercase rounded transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-giallo hover:bg-giallo-bright text-carbon font-heading text-xs uppercase font-bold rounded clip-angular-button transition-colors">
                    Publikasikan Promo
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/admin/promotions/create.blade.php ENDPATH**/ ?>