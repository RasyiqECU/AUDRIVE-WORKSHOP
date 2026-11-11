<?php $__env->startSection('title', 'Detail Purchase Order #' . $purchaseOrder->id); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-5xl mx-auto">
    
    <div>
        <a href="<?php echo e(route('admin.purchase-orders.index')); ?>" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1 mb-2">
            ← Kembali ke Daftar PO
        </a>
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <span class="text-xs text-giallo font-mono">#PO-<?php echo e(str_pad($purchaseOrder->id, 5, '0', STR_PAD_LEFT)); ?></span>
                <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                    PURCHASE ORDER: <?php echo e($purchaseOrder->supplier_name); ?>

                </h1>
                <p class="text-xs text-silver-dim mt-1">
                    Dibuat oleh: <?php echo e($purchaseOrder->creator?->name ?? 'System'); ?> pada <?php echo e($purchaseOrder->created_at->format('d M Y, H:i')); ?>

                </p>
            </div>
            <?php
                $statusVal = $purchaseOrder->status instanceof \App\Enums\PurchaseOrderStatus 
                    ? $purchaseOrder->status->value 
                    : (string) $purchaseOrder->status;
                $statusLabel = $purchaseOrder->status instanceof \App\Enums\PurchaseOrderStatus 
                    ? $purchaseOrder->status->label() 
                    : ucwords(str_replace('_', ' ', $statusVal));
            ?>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 text-xs uppercase rounded font-bold
                    <?php if($statusVal === 'received'): ?> bg-emerald-950/60 text-emerald-400 border border-emerald-800
                    <?php elseif($statusVal === 'sent'): ?> bg-blue-950/60 text-blue-400 border border-blue-800
                    <?php elseif($statusVal === 'partially_received'): ?> bg-amber-950/60 text-amber-400 border border-amber-800
                    <?php elseif($statusVal === 'cancelled'): ?> bg-red-950/60 text-red-400 border border-red-800
                    <?php else: ?> bg-steel text-silver-dim border border-gunmetal <?php endif; ?>">
                    <?php echo e($statusLabel); ?>

                </span>
            </div>
        </div>
    </div>

    
    <div class="panel border border-gunmetal/70 bg-carbon overflow-hidden">
        <div class="p-4 border-b border-gunmetal/50 bg-steel/30">
            <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright">Daftar Item Spare Part</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-silver-muted">
                <thead class="bg-steel/50 text-silver-bright font-heading uppercase text-[10px] tracking-wider border-b border-gunmetal">
                    <tr>
                        <th class="p-3">SKU</th>
                        <th class="p-3">Nama Part</th>
                        <th class="p-3 text-center">Qty Dipesan</th>
                        <th class="p-3 text-center">Qty Diterima</th>
                        <th class="p-3 text-right">Harga Beli</th>
                        <th class="p-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/40 font-mono">
                    <?php $grandTotal = 0; ?>
                    <?php $__empty_1 = true; $__currentLoopData = $purchaseOrder->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php $subtotal = $item->qty * $item->cost_idr; $grandTotal += $subtotal; ?>
                        <tr class="hover:bg-steel/20 transition-colors">
                            <td class="p-3 text-giallo"><?php echo e($item->sparePart?->sku ?? '-'); ?></td>
                            <td class="p-3 font-sans text-silver-bright font-medium"><?php echo e($item->sparePart?->name ?? 'Custom Part'); ?></td>
                            <td class="p-3 text-center text-silver-bright"><?php echo e($item->qty); ?></td>
                            <td class="p-3 text-center text-emerald-400 font-bold"><?php echo e($item->received_qty ?? 0); ?></td>
                            <td class="p-3 text-right text-silver-bright">Rp <?php echo e(number_format($item->cost_idr, 0, ',', '.')); ?></td>
                            <td class="p-3 text-right text-giallo font-bold">Rp <?php echo e(number_format($subtotal, 0, ',', '.')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="p-6 text-center text-silver-dim">Tidak ada item dalam PO ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="border-t border-gunmetal bg-steel/40 font-mono">
                    <tr>
                        <td colspan="5" class="p-3 text-right font-heading uppercase text-silver-bright font-bold">Total Nilai PO:</td>
                        <td class="p-3 text-right text-giallo text-sm font-bold">Rp <?php echo e(number_format($grandTotal, 0, ',', '.')); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/admin/purchase-orders/show.blade.php ENDPATH**/ ?>