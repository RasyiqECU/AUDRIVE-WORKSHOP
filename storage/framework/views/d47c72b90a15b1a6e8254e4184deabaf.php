<?php $__env->startSection('title', 'Manajemen Voucher & Diskon'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <span class="text-giallo font-heading uppercase tracking-wider">Voucher & Diskon</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Marketing & Sales Incentive</p>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                MANAJEMEN KODE VOUCHER & DISKON
            </h1>
            <p class="text-xs text-silver-dim mt-1">
                Buat dan kelola kupon potongan harga untuk kasir POS, reward penukaran poin loyalty, dan promo pelanggan.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?php echo e(route('admin.promotions.index')); ?>" class="px-4 py-2.5 bg-steel hover:bg-gunmetal border border-gunmetal text-silver-bright font-heading text-xs uppercase font-medium rounded transition-colors flex items-center gap-2">
                <span>📢</span>
                <span>Kelola Banner Promo</span>
            </a>
            <a href="<?php echo e(route('admin.vouchers.create')); ?>" class="px-4 py-2.5 bg-giallo hover:bg-giallo-bright text-carbon font-heading text-xs uppercase font-bold rounded clip-angular-button transition-colors flex items-center gap-1.5 shadow-[0_0_15px_rgba(245,196,0,0.2)]">
                <span class="text-sm">+</span>
                <span>Tambah Voucher Baru</span>
            </a>
        </div>
    </div>

    
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="panel border border-gunmetal/60 bg-carbon p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Total Voucher</p>
                    <p class="font-display text-2xl font-bold text-silver-bright mt-1"><?php echo e($stats['total']); ?></p>
                </div>
                <div class="w-10 h-10 rounded bg-steel/40 border border-gunmetal flex items-center justify-center text-lg">
                    🎟️
                </div>
            </div>
        </div>

        <div class="panel border border-emerald-800/40 bg-carbon p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Voucher Aktif</p>
                    <p class="font-display text-2xl font-bold text-emerald-400 mt-1"><?php echo e($stats['active']); ?></p>
                </div>
                <div class="w-10 h-10 rounded bg-emerald-950/40 border border-emerald-800/40 flex items-center justify-center text-lg">
                    ⚡
                </div>
            </div>
        </div>

        <div class="panel border border-giallo/40 bg-carbon p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Reward Loyalty</p>
                    <p class="font-display text-2xl font-bold text-giallo mt-1"><?php echo e($stats['loyalty_reward']); ?></p>
                </div>
                <div class="w-10 h-10 rounded bg-giallo/10 border border-giallo/30 flex items-center justify-center text-lg">
                    👑
                </div>
            </div>
        </div>

        <div class="panel border border-blue-500/40 bg-carbon p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Total Terpakai</p>
                    <p class="font-display text-2xl font-bold text-blue-400 mt-1"><?php echo e(number_format($stats['total_used'])); ?>x</p>
                </div>
                <div class="w-10 h-10 rounded bg-blue-500/10 border border-blue-500/30 flex items-center justify-center text-lg">
                    🛒
                </div>
            </div>
        </div>
    </div>

    
    <div class="panel border border-gunmetal/60 bg-carbon p-4">
        <form method="GET" action="<?php echo e(route('admin.vouchers.index')); ?>" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari Kode Voucher / Deskripsi..." 
                       class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 focus:border-giallo focus:outline-none placeholder-silver-dim">
            </div>
            <div>
                <select name="type" class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 focus:border-giallo focus:outline-none">
                    <option value="">Semua Tipe Diskon</option>
                    <option value="fixed" <?php echo e(request('type') === 'fixed' ? 'selected' : ''); ?>>Nominal Tetap (Rp)</option>
                    <option value="percent" <?php echo e(request('type') === 'percent' ? 'selected' : ''); ?>>Persentase (%)</option>
                </select>
            </div>
            <div>
                <select name="status" class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 focus:border-giallo focus:outline-none">
                    <option value="">Semua Status</option>
                    <option value="active" <?php echo e(request('status') === 'active' ? 'selected' : ''); ?>>Aktif & Berlaku</option>
                    <option value="expired" <?php echo e(request('status') === 'expired' ? 'selected' : ''); ?>>Kadaluarsa</option>
                    <option value="inactive" <?php echo e(request('status') === 'inactive' ? 'selected' : ''); ?>>Dinonaktifkan</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-giallo hover:bg-giallo-bright text-carbon font-heading text-xs uppercase font-bold py-2.5 px-4 rounded transition-premium">
                    Filter
                </button>
                <?php if(request()->hasAny(['search', 'type', 'status'])): ?>
                    <a href="<?php echo e(route('admin.vouchers.index')); ?>" class="bg-steel hover:bg-gunmetal text-silver-bright font-heading text-xs uppercase py-2.5 px-3 rounded flex items-center justify-center">
                        Reset
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    
    <div class="panel border border-gunmetal/60 bg-carbon overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-steel/40 text-silver-dim font-heading uppercase text-[10px] tracking-wider border-b border-gunmetal">
                    <tr>
                        <th class="py-3 px-4">Kode Kupon</th>
                        <th class="py-3 px-4">Besaran Diskon</th>
                        <th class="py-3 px-4">Loyalty Poin</th>
                        <th class="py-3 px-4">Min. Belanja</th>
                        <th class="py-3 px-4">Kuota / Terpakai</th>
                        <th class="py-3 px-4">Berlaku Sampai</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/40">
                    <?php $__empty_1 = true; $__currentLoopData = $vouchers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-steel/20 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-giallo text-sm px-2 py-0.5 bg-giallo/10 border border-giallo/30 rounded">
                                        <?php echo e($v->code); ?>

                                    </span>
                                </div>
                                <?php if($v->description): ?>
                                    <p class="text-[10px] text-silver-dim mt-1 max-w-xs truncate"><?php echo e($v->description); ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-silver-bright">
                                <?php if(($v->type->value ?? $v->type) === 'percent'): ?>
                                    <span class="text-amber-400"><?php echo e($v->value); ?>%</span>
                                <?php else: ?>
                                    <span class="text-emerald-400">Rp <?php echo e(number_format($v->value, 0, ',', '.')); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4">
                                <?php if($v->points_cost > 0): ?>
                                    <span class="inline-flex items-center gap-1 font-mono text-giallo font-bold bg-giallo/10 px-2 py-0.5 rounded border border-giallo/20">
                                        👑 <?php echo e(number_format($v->points_cost)); ?> Poin
                                    </span>
                                <?php else: ?>
                                    <span class="text-silver-dim text-[11px]">Kupon Publik (0 Poin)</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-silver-dim">
                                <?php echo e($v->min_purchase_idr > 0 ? 'Rp ' . number_format($v->min_purchase_idr, 0, ',', '.') : 'Tanpa Minimum'); ?>

                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-mono font-bold text-silver-bright"><?php echo e($v->used_count); ?></span>
                                <span class="text-silver-dim font-mono">/ <?php echo e($v->max_usage); ?></span>
                                <div class="w-20 bg-gunmetal h-1.5 rounded-full overflow-hidden mt-1">
                                    <?php $pct = $v->max_usage > 0 ? min(100, ($v->used_count / $v->max_usage) * 100) : 0; ?>
                                    <div class="bg-giallo h-full" style="width: <?php echo e($pct); ?>%"></div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-[11px] <?php echo e($v->expires_at && $v->expires_at->isPast() ? 'text-danger' : 'text-silver-dim'); ?>">
                                <?php echo e($v->expires_at ? $v->expires_at->format('d M Y, H:i') : 'Selamanya'); ?>

                            </td>
                            <td class="py-3.5 px-4">
                                <?php if(!$v->is_active): ?>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-steel text-silver-dim border border-gunmetal">Nonaktif</span>
                                <?php elseif($v->expires_at && $v->expires_at->isPast()): ?>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-danger/20 text-danger border border-danger/40">Kadaluarsa</span>
                                <?php elseif($v->used_count >= $v->max_usage): ?>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-500/20 text-amber-400 border border-amber-500/40">Habis</span>
                                <?php else: ?>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-950/60 text-emerald-400 border border-emerald-800">Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <form action="<?php echo e(route('admin.vouchers.toggle', $v)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <button type="submit" title="<?php echo e($v->is_active ? 'Nonaktifkan' : 'Aktifkan'); ?>" class="p-1.5 bg-steel hover:bg-gunmetal text-silver-bright rounded text-xs transition-colors">
                                            <?php echo e($v->is_active ? '⏸️' : '▶️'); ?>

                                        </button>
                                    </form>
                                    <a href="<?php echo e(route('admin.vouchers.edit', $v)); ?>" class="p-1.5 bg-steel hover:bg-giallo hover:text-carbon text-silver-bright rounded text-xs transition-colors" title="Edit">
                                        ✏️
                                    </a>
                                    <form action="<?php echo e(route('admin.vouchers.destroy', $v)); ?>" method="POST" onsubmit="return confirm('Hapus voucher <?php echo e($v->code); ?>?');">
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
                            <td colspan="8" class="text-center py-10 text-silver-dim">
                                Belum ada voucher yang dibuat. Klik tombol "+ Tambah Voucher Baru" di atas.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($vouchers->hasPages()): ?>
            <div class="p-4 border-t border-gunmetal">
                <?php echo e($vouchers->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/admin/vouchers/index.blade.php ENDPATH**/ ?>