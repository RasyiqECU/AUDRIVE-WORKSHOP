


<?php $__env->startSection('title', 'Approval Requests'); ?>


<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-display text-3xl font-bold text-silver-bright tracking-wide">
                Approval Requests
            </h1>
            <p class="text-sm text-silver-dim mt-2">
                Kelola permintaan approval untuk diskon, refund, dan adjustment
            </p>
        </div>
    </div>


    
    <div class="panel p-4 border border-gunmetal">
        <form method="GET" action="<?php echo e(route('admin.approvals.index')); ?>" class="flex flex-wrap gap-3">
            <select name="status" class="bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                <option value="">Semua Status</option>
                <option value="pending" <?php echo e(request('status') === 'pending' ? 'selected' : ''); ?>>Pending</option>
                <option value="approved" <?php echo e(request('status') === 'approved' ? 'selected' : ''); ?>>Approved</option>
                <option value="rejected" <?php echo e(request('status') === 'rejected' ? 'selected' : ''); ?>>Rejected</option>
            </select>


            <select name="type" class="bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                <option value="">Semua Tipe</option>
                <option value="discount" <?php echo e(request('type') === 'discount' ? 'selected' : ''); ?>>Discount</option>
                <option value="refund" <?php echo e(request('type') === 'refund' ? 'selected' : ''); ?>>Refund</option>
                <option value="price_adjustment" <?php echo e(request('type') === 'price_adjustment' ? 'selected' : ''); ?>>Price Adjustment</option>
                <option value="stock_adjustment" <?php echo e(request('type') === 'stock_adjustment' ? 'selected' : ''); ?>>Stock Adjustment</option>
            </select>


            <button type="submit" class="px-4 py-2 bg-giallo text-nero font-heading text-xs uppercase tracking-wider hover:bg-arancio transition-premium">
                Filter
            </button>


            <a href="<?php echo e(route('admin.approvals.index')); ?>" class="px-4 py-2 border border-gunmetal text-silver-dim font-heading text-xs uppercase tracking-wider hover:border-silver-dim transition-premium">
                Reset
            </a>
        </form>
    </div>


    
    <div class="panel border border-gunmetal overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gunmetal bg-steel/20">
                        <th class="text-left px-4 py-3 font-heading text-[10px] uppercase tracking-wider text-silver-dim">ID</th>
                        <th class="text-left px-4 py-3 font-heading text-[10px] uppercase tracking-wider text-silver-dim">Tipe</th>
                        <th class="text-left px-4 py-3 font-heading text-[10px] uppercase tracking-wider text-silver-dim">Pemohon</th>
                        <th class="text-left px-4 py-3 font-heading text-[10px] uppercase tracking-wider text-silver-dim">Tanggal</th>
                        <th class="text-center px-4 py-3 font-heading text-[10px] uppercase tracking-wider text-silver-dim">Status</th>
                        <th class="text-left px-4 py-3 font-heading text-[10px] uppercase tracking-wider text-silver-dim">Disetujui Oleh</th>
                        <th class="text-center px-4 py-3 font-heading text-[10px] uppercase tracking-wider text-silver-dim">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/30">
                    <?php $__empty_1 = true; $__currentLoopData = $approvals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $approval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-steel/20 transition-premium">
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs text-giallo">#<?php echo e($approval->id); ?></span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-heading text-xs text-silver-bright uppercase">
                                <?php echo e(str_replace('_', ' ', $approval->type)); ?>

                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-body text-xs text-silver-bright"><?php echo e($approval->requester->name); ?></span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs text-silver-dim">
                                <?php echo e($approval->created_at->format('d M Y H:i')); ?>

                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <?php if($approval->status === 'pending'): ?>
                                <span class="px-2 py-1 bg-giallo/10 text-giallo font-heading text-[10px] uppercase tracking-wider">
                                    Pending
                                </span>
                            <?php elseif($approval->status === 'approved'): ?>
                                <span class="px-2 py-1 bg-verde/10 text-verde font-heading text-[10px] uppercase tracking-wider">
                                    Approved
                                </span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-danger/10 text-danger font-heading text-[10px] uppercase tracking-wider">
                                    Rejected
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3">
                            <?php if($approval->approver): ?>
                                <span class="font-body text-xs text-silver-bright"><?php echo e($approval->approver->name); ?></span>
                            <?php else: ?>
                                <span class="font-body text-xs text-silver-dim">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="<?php echo e(route('admin.approvals.show', $approval)); ?>"
                               class="font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider">
                                Detail
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center">
                            <p class="font-body text-sm text-silver-dim">Tidak ada approval request</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>


    
    <?php if($approvals->hasPages()): ?>
    <div class="flex justify-center">
        <?php echo e($approvals->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/admin/approvals/index.blade.php ENDPATH**/ ?>