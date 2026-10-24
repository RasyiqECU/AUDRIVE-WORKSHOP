<?php $__env->startSection('title', 'Manajemen Banner & Event Promosi'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <a href="<?php echo e(route('admin.vouchers.index')); ?>" class="text-silver-dim hover:text-giallo font-heading uppercase tracking-wider">Voucher</a>
    <span class="text-silver-dim">/</span>
    <span class="text-giallo font-heading uppercase tracking-wider">Banner Promo</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Marketing Campaigns</p>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                KAMPANYE & BANNER PROMO AUDrive
            </h1>
            <p class="text-xs text-silver-dim mt-1">
                Kelola penawaran khusus dan event promosi yang tampil di katalog beranda dan portal customer.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?php echo e(route('admin.vouchers.index')); ?>" class="px-4 py-2.5 bg-steel hover:bg-gunmetal border border-gunmetal text-silver-bright font-heading text-xs uppercase font-medium rounded transition-colors flex items-center gap-2">
                <span>🎟️</span>
                <span>Daftar Kode Voucher</span>
            </a>
            <a href="<?php echo e(route('admin.promotions.create')); ?>" class="px-4 py-2.5 bg-giallo hover:bg-giallo-bright text-carbon font-heading text-xs uppercase font-bold rounded clip-angular-button transition-colors flex items-center gap-1.5 shadow-[0_0_15px_rgba(245,196,0,0.2)]">
                <span class="text-sm">+</span>
                <span>Buat Kampanye Promo</span>
            </a>
        </div>
    </div>

    
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="panel border border-gunmetal/60 bg-carbon p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Total Promo</p>
                    <p class="font-display text-2xl font-bold text-silver-bright mt-1"><?php echo e($stats['total']); ?></p>
                </div>
                <div class="w-10 h-10 rounded bg-steel/40 border border-gunmetal flex items-center justify-center text-lg">
                    📢
                </div>
            </div>
        </div>

        <div class="panel border border-emerald-800/40 bg-carbon p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Promo Sedang Berjalan</p>
                    <p class="font-display text-2xl font-bold text-emerald-400 mt-1"><?php echo e($stats['active']); ?></p>
                </div>
                <div class="w-10 h-10 rounded bg-emerald-950/40 border border-emerald-800/40 flex items-center justify-center text-lg">
                    🔥
                </div>
            </div>
        </div>

        <div class="panel border border-blue-500/40 bg-carbon p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Akan Datang (Upcoming)</p>
                    <p class="font-display text-2xl font-bold text-blue-400 mt-1"><?php echo e($stats['upcoming']); ?></p>
                </div>
                <div class="w-10 h-10 rounded bg-blue-500/10 border border-blue-500/30 flex items-center justify-center text-lg">
                    ⏳
                </div>
            </div>
        </div>
    </div>

    
    <div class="panel border border-gunmetal/60 bg-carbon overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-steel/40 text-silver-dim font-heading uppercase text-[10px] tracking-wider border-b border-gunmetal">
                    <tr>
                        <th class="py-3 px-4">Nama Kampanye Promo</th>
                        <th class="py-3 px-4">Nilai Promo</th>
                        <th class="py-3 px-4">Berlaku Untuk</th>
                        <th class="py-3 px-4">Periode Tayang</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/40">
                    <?php $__empty_1 = true; $__currentLoopData = $promotions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $promo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-steel/20 transition-colors">
                            <td class="py-3.5 px-4">
                                <p class="font-bold text-silver-bright text-sm"><?php echo e($promo->name); ?></p>
                                <?php if($promo->description): ?>
                                    <p class="text-[10px] text-silver-dim mt-0.5 line-clamp-1 max-w-sm"><?php echo e($promo->description); ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold">
                                <?php if(($promo->type->value ?? $promo->type) === 'percent'): ?>
                                    <span class="text-amber-400"><?php echo e($promo->value); ?>%</span>
                                <?php else: ?>
                                    <span class="text-emerald-400">Rp <?php echo e(number_format($promo->value, 0, ',', '.')); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4 text-silver-dim">
                                <span class="px-2 py-0.5 rounded bg-steel border border-gunmetal text-[10px] text-silver-bright font-mono">
                                    <?php echo e($promo->applicable_to ?? 'Semua Layanan'); ?>

                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-[11px] text-silver-dim">
                                <div><?php echo e($promo->starts_at->format('d M Y')); ?> s/d</div>
                                <div class="text-silver-bright"><?php echo e($promo->ends_at->format('d M Y')); ?></div>
                            </td>
                            <td class="py-3.5 px-4">
                                <?php if(!$promo->is_active): ?>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-steel text-silver-dim border border-gunmetal">Nonaktif</span>
                                <?php elseif($promo->ends_at->isPast()): ?>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-danger/20 text-danger border border-danger/40">Berakhir</span>
                                <?php elseif($promo->starts_at->isFuture()): ?>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-500/20 text-blue-300 border border-blue-500/40">Mendatang</span>
                                <?php else: ?>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-950/60 text-emerald-400 border border-emerald-800">Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <form action="<?php echo e(route('admin.promotions.toggle', $promo)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <button type="submit" title="<?php echo e($promo->is_active ? 'Nonaktifkan' : 'Aktifkan'); ?>" class="p-1.5 bg-steel hover:bg-gunmetal text-silver-bright rounded text-xs transition-colors">
                                            <?php echo e($promo->is_active ? '⏸️' : '▶️'); ?>

                                        </button>
                                    </form>
                                    <a href="<?php echo e(route('admin.promotions.edit', $promo)); ?>" class="p-1.5 bg-steel hover:bg-giallo hover:text-carbon text-silver-bright rounded text-xs transition-colors" title="Edit">
                                        ✏️
                                    </a>
                                    <form action="<?php echo e(route('admin.promotions.destroy', $promo)); ?>" method="POST" onsubmit="return confirm('Hapus promo <?php echo e($promo->name); ?>?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="p-1.5 bg-steel hover:bg-danger text-silver-bright rounded text-xs transition-colors" title="Hapus">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center py-10 text-silver-dim">
                                Belum ada kampanye promo. Klik tombol "+ Buat Kampanye Promo" di atas.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($promotions->hasPages()): ?>
            <div class="p-4 border-t border-gunmetal">
                <?php echo e($promotions->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/admin/promotions/index.blade.php ENDPATH**/ ?>