<?php $__env->startSection('title', 'Kartu Stok - ' . $sparePart->name); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div>
        <a href="<?php echo e(route('admin.inventory.index')); ?>" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1 mb-2">
            ← Kembali ke Inventaris
        </a>
        <div class="flex items-center justify-between">
            <div>
                <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Log Pergerakan Stok</p>
                <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                    <?php echo e($sparePart->name); ?> (<?php echo e($sparePart->sku); ?>)
                </h1>
            </div>
            <div class="text-right">
                <p class="text-xs text-silver-dim">Stok Saat Ini</p>
                <p class="font-mono text-2xl text-giallo font-bold"><?php echo e($sparePart->stock); ?> pcs</p>
            </div>
        </div>
    </div>

    
    <div class="panel border border-gunmetal/70 bg-carbon overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-silver-muted">
                <thead class="bg-steel/50 text-silver-bright font-heading uppercase text-[10px] tracking-wider border-b border-gunmetal">
                    <tr>
                        <th class="p-3">Tanggal</th>
                        <th class="p-3">Tipe Mutasi</th>
                        <th class="p-3 text-center">Qty</th>
                        <th class="p-3 text-center">Stok Sebelum</th>
                        <th class="p-3 text-center">Stok Sesudah</th>
                        <th class="p-3">Keterangan / Referensi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/40 font-mono">
                    <?php $__empty_1 = true; $__currentLoopData = $movements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-steel/20 transition-colors">
                            <td class="p-3 text-silver-dim"><?php echo e($m->created_at->format('d M Y, H:i')); ?></td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 text-[10px] uppercase rounded font-bold
                                    <?php if($m->type === 'in' || $m->quantity > 0): ?> bg-emerald-950/60 text-emerald-400 border border-emerald-800
                                    <?php else: ?> bg-red-950/60 text-red-400 border border-red-800 <?php endif; ?>">
                                    <?php echo e($m->type ?? ($m->quantity > 0 ? 'MASUK' : 'KELUAR')); ?>

                                </span>
                            </td>
                            <td class="p-3 text-center font-bold <?php echo e($m->quantity > 0 ? 'text-emerald-400' : 'text-red-400'); ?>">
                                <?php echo e($m->quantity > 0 ? '+' . $m->quantity : $m->quantity); ?>

                            </td>
                            <td class="p-3 text-center text-silver-dim"><?php echo e($m->stock_before ?? '-'); ?></td>
                            <td class="p-3 text-center text-silver-bright font-bold"><?php echo e($m->stock_after ?? '-'); ?></td>
                            <td class="p-3 font-sans text-silver-dim"><?php echo e($m->notes ?? $m->reference ?? '-'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="p-8 text-center text-silver-dim font-sans">
                                Belum ada riwayat mutasi stok untuk barang ini.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/admin/inventory/movements.blade.php ENDPATH**/ ?>