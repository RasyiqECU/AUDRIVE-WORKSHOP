<?php $__env->startSection('title', 'Detail Spare Part - ' . $sparePart->name); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <a href="<?php echo e(route('admin.spare-parts.index')); ?>" class="text-silver-dim hover:text-giallo transition-premium font-heading uppercase tracking-wider">
        Katalog Spare Part
    </a>
    <span class="text-gunmetal">/</span>
    <span class="text-giallo font-heading uppercase tracking-wider"><?php echo e($sparePart->sku); ?></span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-5xl mx-auto">
    
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <a href="<?php echo e(route('admin.spare-parts.index')); ?>" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1 mb-2">
                ← Kembali ke Katalog Spare Part
            </a>
            <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Informasi Suku Cadang</p>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                <?php echo e($sparePart->name); ?>

            </h1>
            <p class="font-mono text-xs text-giallo mt-1">SKU: <?php echo e($sparePart->sku); ?></p>
        </div>
        <div class="flex items-center gap-3">
            <?php if(in_array(auth()->user()->role ?? '', ['super-admin', 'manager', 'inventory'], true)): ?>
                <a href="<?php echo e(route('admin.inventory.movements', $sparePart->id)); ?>" class="px-3 py-2 bg-steel/60 border border-gunmetal text-silver-bright hover:border-giallo font-heading text-xs uppercase tracking-wider rounded transition-premium">
                    📊 Kartu Stok
                </a>
            <?php endif; ?>
            <?php if(in_array(auth()->user()->role ?? '', ['super-admin', 'inventory'], true)): ?>
                <a href="<?php echo e(route('admin.spare-parts.edit', $sparePart)); ?>" class="px-4 py-2 bg-giallo hover:bg-giallo-bright text-carbon font-heading text-xs uppercase tracking-wider font-bold rounded clip-angular-button transition-premium">
                    Edit Data
                </a>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div class="md:col-span-2 panel border border-gunmetal/70 bg-carbon p-6 space-y-4">
            <h2 class="font-heading text-sm uppercase tracking-wider text-silver-bright border-b border-gunmetal pb-2">
                Spesifikasi & Informasi Part
            </h2>
            <div class="grid grid-cols-2 gap-4 text-xs">
                <div>
                    <p class="text-silver-dim">Nama Suku Cadang</p>
                    <p class="font-medium text-silver-bright mt-0.5"><?php echo e($sparePart->name); ?></p>
                </div>
                <div>
                    <p class="text-silver-dim">SKU / Part Number</p>
                    <p class="font-mono text-giallo mt-0.5"><?php echo e($sparePart->sku); ?></p>
                </div>
                <div>
                    <p class="text-silver-dim">Kategori</p>
                    <p class="text-silver-bright mt-0.5"><?php echo e($sparePart->category?->name ?? '-'); ?></p>
                </div>
                <div>
                    <p class="text-silver-dim">Merk / Brand</p>
                    <p class="text-silver-bright mt-0.5"><?php echo e($sparePart->brand ?? '-'); ?></p>
                </div>
                <div>
                    <p class="text-silver-dim">Status Katalog</p>
                    <p class="text-silver-bright mt-0.5 uppercase"><?php echo e(is_string($sparePart->status) ? $sparePart->status : ($sparePart->status?->value ?? 'active')); ?></p>
                </div>
                <div>
                    <p class="text-silver-dim">Berat (Kg)</p>
                    <p class="text-silver-bright mt-0.5"><?php echo e($sparePart->weight_kg ? $sparePart->weight_kg . ' kg' : '-'); ?></p>
                </div>
            </div>

            <?php if($sparePart->description): ?>
                <div class="pt-4 border-t border-gunmetal">
                    <p class="text-xs text-silver-dim mb-1">Deskripsi & Catatan</p>
                    <p class="text-xs text-silver leading-relaxed bg-steel/30 p-3 rounded border border-gunmetal/40">
                        <?php echo e($sparePart->description); ?>

                    </p>
                </div>
            <?php endif; ?>
        </div>

        
        <div class="space-y-6">
            
            <div class="panel border border-gunmetal/70 bg-carbon p-4">
                <div class="relative aspect-square w-full rounded border border-gunmetal bg-steel/20 overflow-hidden flex items-center justify-center">
                    <?php if($sparePart->image): ?>
                        <img src="<?php echo e(asset('storage/' . $sparePart->image)); ?>" alt="<?php echo e($sparePart->name); ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <div class="flex flex-col items-center justify-center text-gunmetal p-6 text-center">
                            <span class="text-5xl mb-2">⚙️</span>
                            <span class="text-xs text-silver-dim">Belum ada gambar</span>
                            <a href="<?php echo e(route('admin.spare-parts.edit', $sparePart)); ?>" class="mt-2 text-xs text-giallo hover:underline font-heading uppercase">
                                + Tambah Gambar
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if($sparePart->image): ?>
                    <div class="mt-3 flex items-center justify-between text-xs">
                        <span class="text-silver-dim text-[11px]">Foto Katalog</span>
                        <?php if(in_array(auth()->user()->role ?? '', ['super-admin', 'inventory'], true)): ?>
                            <a href="<?php echo e(route('admin.spare-parts.edit', $sparePart)); ?>" class="text-giallo hover:underline font-heading uppercase text-[11px]">
                                Ganti / Hapus Foto
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            
            <div class="panel border border-gunmetal/70 bg-carbon p-6 space-y-6">
            <div>
                <p class="font-heading text-xs uppercase tracking-wider text-silver-dim">Harga Jual Satuan</p>
                <p class="font-display text-2xl font-bold text-silver-bright mt-1">
                    Rp <?php echo e(number_format($sparePart->price_idr, 0, ',', '.')); ?>

                </p>
            </div>

            <div class="pt-4 border-t border-gunmetal">
                <p class="font-heading text-xs uppercase tracking-wider text-silver-dim">Level Stok Saat Ini</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="font-display text-3xl font-bold <?php echo e($sparePart->stock <= $sparePart->low_stock_threshold ? 'text-red-400' : 'text-emerald-400'); ?>">
                        <?php echo e($sparePart->stock); ?>

                    </span>
                    <span class="text-xs text-silver-dim">pcs tersedia</span>
                </div>
                <p class="text-[11px] text-silver-dim mt-2">
                    Batas Minimum: <span class="font-mono text-silver-bright"><?php echo e($sparePart->low_stock_threshold); ?> pcs</span>
                </p>
            </div>

            <?php if($sparePart->compatibilities->count() > 0): ?>
                <div class="pt-4 border-t border-gunmetal">
                    <p class="font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">Kompatibilitas Kendaraan</p>
                    <ul class="space-y-1 text-xs text-silver-bright font-mono">
                        <?php $__currentLoopData = $sparePart->compatibilities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="bg-steel/40 px-2.5 py-1 rounded border border-gunmetal/40">
                                <?php echo e($comp->vehicle_brand); ?> <?php echo e($comp->vehicle_model); ?> (<?php echo e($comp->year_from); ?> - <?php echo e($comp->year_to); ?>)
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/admin/spare-parts/show.blade.php ENDPATH**/ ?>