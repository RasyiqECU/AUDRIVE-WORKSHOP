<?php $__env->startSection('title', 'Purchase Orders'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Procurement & Supplier</p>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                PURCHASE ORDERS (PO)
            </h1>
            <p class="text-xs text-silver-dim mt-1">
                Kelola pemesanan suku cadang ke supplier dan penerimaan barang masuk ke gudang.
            </p>
        </div>
        <a href="<?php echo e(route('admin.purchase-orders.create')); ?>" class="px-4 py-2 bg-giallo hover:bg-giallo-bright text-carbon font-heading text-xs uppercase tracking-wider font-bold rounded clip-angular-button transition-premium">
            + Buat PO Baru
        </a>
    </div>

    
    <div class="panel border border-gunmetal/70 bg-carbon p-4">
        <form action="<?php echo e(route('admin.purchase-orders.index')); ?>" method="GET" class="flex flex-wrap items-center gap-3">
            <select name="status" class="bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2 focus:border-giallo focus:outline-none">
                <option value="">Semua Status</option>
                <option value="draft" <?php echo e(request('status') == 'draft' ? 'selected' : ''); ?>>Draft</option>
                <option value="sent" <?php echo e(request('status') == 'sent' ? 'selected' : ''); ?>>Sent to Supplier</option>
                <option value="partially_received" <?php echo e(request('status') == 'partially_received' ? 'selected' : ''); ?>>Partially Received</option>
                <option value="received" <?php echo e(request('status') == 'received' ? 'selected' : ''); ?>>Received (Selesai)</option>
                <option value="cancelled" <?php echo e(request('status') == 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright text-xs font-heading uppercase rounded">
                Filter
            </button>
            <a href="<?php echo e(route('admin.purchase-orders.index')); ?>" class="px-3 py-2 text-silver-dim hover:text-silver-bright text-xs font-heading uppercase">
                Reset
            </a>
        </form>
    </div>

    
    <div class="panel border border-gunmetal/70 bg-carbon overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-silver-muted">
                <thead class="bg-steel/50 text-silver-bright font-heading uppercase text-[10px] tracking-wider border-b border-gunmetal">
                    <tr>
                        <th class="p-3">No. PO</th>
                        <th class="p-3">Supplier</th>
                        <th class="p-3">Dibuat Oleh</th>
                        <th class="p-3 text-right">Total Biaya</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Tanggal</th>
                        <th class="p-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/40 font-mono">
                    <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $po): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-steel/20 transition-colors">
                            <td class="p-3 text-giallo font-semibold">#PO-<?php echo e(str_pad($po->id, 5, '0', STR_PAD_LEFT)); ?></td>
                            <td class="p-3 font-sans text-silver-bright font-medium"><?php echo e($po->supplier_name); ?></td>
                            <td class="p-3 font-sans text-silver-dim"><?php echo e($po->creator?->name ?? 'System'); ?></td>
                            <td class="p-3 text-right text-silver-bright">Rp <?php echo e(number_format($po->total_cost_idr ?? 0, 0, ',', '.')); ?></td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 text-[10px] uppercase rounded font-bold
                                    <?php if($po->status === 'received'): ?> bg-emerald-950/60 text-emerald-400 border border-emerald-800
                                    <?php elseif($po->status === 'sent'): ?> bg-blue-950/60 text-blue-400 border border-blue-800
                                    <?php elseif($po->status === 'cancelled'): ?> bg-red-950/60 text-red-400 border border-red-800
                                    <?php else: ?> bg-steel text-silver-dim border border-gunmetal <?php endif; ?>">
                                    <?php echo e(str_replace('_', ' ', $po->status)); ?>

                                </span>
                            </td>
                            <td class="p-3 text-silver-dim text-[11px]"><?php echo e($po->created_at->format('d M Y')); ?></td>
                            <td class="p-3 text-right font-sans">
                                <a href="<?php echo e(route('admin.purchase-orders.show', $po->id)); ?>" class="text-xs text-giallo hover:underline font-heading uppercase">
                                    Detail →
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="p-8 text-center text-silver-dim font-sans">
                                Belum ada transaksi Purchase Order.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if($orders->hasPages()): ?>
        <div><?php echo e($orders->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/admin/purchase-orders/index.blade.php ENDPATH**/ ?>