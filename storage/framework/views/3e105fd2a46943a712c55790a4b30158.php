<?php $__env->startSection('title', 'Katalog Spare Part'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <span class="text-giallo font-heading uppercase tracking-wider">Katalog Spare Part</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Master Data</p>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                KATALOG SUKU CADANG
            </h1>
            <p class="text-xs text-silver-dim mt-1">
                Katalog master spare parts dan suku cadang mobil performa tinggi AUDrive.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?php echo e(route('admin.inventory.index')); ?>" class="px-3 py-2 bg-steel/60 border border-gunmetal text-silver-bright hover:border-giallo font-heading text-xs uppercase tracking-wider rounded flex items-center gap-2 transition-premium">
                📦 Cek Inventori
            </a>
            <a href="<?php echo e(route('admin.spare-parts.create')); ?>" class="px-4 py-2 bg-giallo hover:bg-giallo-bright text-carbon font-heading text-xs uppercase tracking-wider font-bold rounded clip-angular-button transition-premium">
                + Tambah Spare Part
            </a>
        </div>
    </div>

    
    <div class="panel border border-gunmetal/70 bg-carbon p-4">
        <form action="<?php echo e(route('admin.spare-parts.index')); ?>" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="md:col-span-2">
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari nama atau SKU / Part Number..." class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2 focus:border-giallo focus:outline-none">
            </div>
            <div>
                <select name="category_id" class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2 focus:border-giallo focus:outline-none">
                    <option value="">Semua Kategori</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cat->id); ?>" <?php echo e(request('category_id') == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright text-xs font-heading uppercase tracking-wider rounded">
                    Filter
                </button>
                <a href="<?php echo e(route('admin.spare-parts.index')); ?>" class="px-3 py-2 bg-carbon border border-gunmetal text-silver-dim text-xs font-heading uppercase rounded">
                    Reset
                </a>
            </div>
        </form>
    </div>

    
    <div class="panel border border-gunmetal/70 bg-carbon overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-silver-muted">
                <thead class="bg-steel/50 text-silver-bright font-heading uppercase text-[10px] tracking-wider border-b border-gunmetal">
                    <tr>
                        <th class="p-3">SKU / Part Number</th>
                        <th class="p-3">Nama Spare Part</th>
                        <th class="p-3">Kategori</th>
                        <th class="p-3">Brand</th>
                        <th class="p-3 text-right">Harga Jual</th>
                        <th class="p-3 text-center">Stok</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/40 font-mono">
                    <?php $__empty_1 = true; $__currentLoopData = $spareParts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $part): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-steel/20 transition-colors">
                            <td class="p-3 text-giallo font-semibold"><?php echo e($part->sku); ?></td>
                            <td class="p-3 font-sans text-silver-bright font-medium"><?php echo e($part->name); ?></td>
                            <td class="p-3 font-sans text-silver-dim"><?php echo e($part->category?->name ?? '-'); ?></td>
                            <td class="p-3 font-sans text-silver-dim"><?php echo e($part->brand ?? '-'); ?></td>
                            <td class="p-3 text-right text-silver-bright">Rp <?php echo e(number_format($part->price_idr, 0, ',', '.')); ?></td>
                            <td class="p-3 text-center">
                                <span class="px-2 py-0.5 text-[11px] rounded font-bold
                                    <?php if($part->stock <= $part->low_stock_threshold): ?> bg-red-950/60 text-red-400 border border-red-800
                                    <?php else: ?> bg-emerald-950/60 text-emerald-400 border border-emerald-800 <?php endif; ?>">
                                    <?php echo e($part->stock); ?>

                                </span>
                            </td>
                            <td class="p-3 text-center font-sans">
                                <span class="px-2 py-0.5 text-[10px] uppercase rounded font-bold
                                    <?php if($part->status?->value === 'active' || $part->status === 'active'): ?> bg-emerald-950/60 text-emerald-400 border border-emerald-800
                                    <?php else: ?> bg-gray-800 text-silver-dim border border-gunmetal <?php endif; ?>">
                                    <?php echo e(is_string($part->status) ? $part->status : ($part->status?->value ?? 'active')); ?>

                                </span>
                            </td>
                            <td class="p-3 text-right font-sans space-x-2">
                                <a href="<?php echo e(route('admin.spare-parts.show', $part)); ?>" class="text-xs text-silver-dim hover:text-silver-bright">
                                    Detail
                                </a>
                                <a href="<?php echo e(route('admin.spare-parts.edit', $part)); ?>" class="text-xs text-giallo hover:underline font-heading uppercase font-semibold">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="p-8 text-center text-silver-dim font-sans">
                                Belum ada data suku cadang dalam katalog.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if($spareParts->hasPages()): ?>
        <div><?php echo e($spareParts->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/admin/spare-parts/index.blade.php ENDPATH**/ ?>