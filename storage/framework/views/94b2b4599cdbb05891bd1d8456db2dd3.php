<?php $__env->startSection('title', 'Struk Transaksi POS #' . $salesOrder->id); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-xl mx-auto">
    
    <div class="flex items-center justify-between">
        <a href="<?php echo e(route('admin.pos.index')); ?>" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1">
            ← Kasir Baru
        </a>
        <a href="<?php echo e(route('admin.pos.print', $salesOrder)); ?>" target="_blank" class="px-4 py-2 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright font-heading text-xs uppercase tracking-wider rounded">
            🖨️ Cetak Struk
        </a>
    </div>

    
    <div class="panel border border-gunmetal/70 bg-carbon p-6 space-y-4 font-mono text-xs text-silver-muted shadow-2xl">
        <div class="text-center space-y-1 border-b border-gunmetal/60 pb-4">
            <h2 class="font-display text-lg text-silver-bright font-bold tracking-widest">AUDRIVE MOTORS</h2>
            <p class="text-[10px] text-silver-dim">Workshop & Luxury Auto Solutions</p>
            <p class="text-[10px] text-silver-dim">No. Trx: #<?php echo e($salesOrder->code); ?></p>
            <p class="text-[10px] text-silver-dim"><?php echo e($salesOrder->created_at->format('d/m/Y H:i:s')); ?></p>
            <?php if($salesOrder->customer): ?>
                <p class="text-[10px] text-silver-bright font-bold">Customer: <?php echo e($salesOrder->customer->user?->name); ?></p>
            <?php endif; ?>
        </div>

        <div class="space-y-2 py-2 border-b border-gunmetal/60">
            <?php $__currentLoopData = $salesOrder->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex justify-between">
                    <div>
                        <p class="text-silver-bright font-medium"><?php echo e($item->description ?: ($item->sparePart?->name ?? 'Item')); ?></p>
                        <p class="text-[10px] text-silver-dim"><?php echo e($item->qty); ?> x Rp <?php echo e(number_format($item->unit_price_idr, 0, ',', '.')); ?></p>
                    </div>
                    <p class="text-silver-bright font-bold">Rp <?php echo e(number_format($item->subtotal_idr, 0, ',', '.')); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="space-y-1.5 pt-2">
            <?php if($salesOrder->discount_idr > 0): ?>
                <div class="flex justify-between text-xs text-verde">
                    <span>Diskon:</span>
                    <span>- Rp <?php echo e(number_format($salesOrder->discount_idr, 0, ',', '.')); ?></span>
                </div>
            <?php endif; ?>
            <?php if($salesOrder->tax_idr > 0): ?>
                <div class="flex justify-between text-xs text-silver-dim">
                    <span>PPN (11%):</span>
                    <span>Rp <?php echo e(number_format($salesOrder->tax_idr, 0, ',', '.')); ?></span>
                </div>
            <?php endif; ?>
            <div class="flex justify-between text-silver-bright text-sm font-bold border-t border-gunmetal/60 pt-2">
                <span>TOTAL:</span>
                <span class="text-giallo">Rp <?php echo e(number_format($salesOrder->total_idr, 0, ',', '.')); ?></span>
            </div>
            <div class="flex justify-between text-[11px] text-silver-dim">
                <span>Pembayaran:</span>
                <span class="uppercase font-bold text-silver-bright"><?php echo e($salesOrder->payments?->first()?->method ?? 'CASH'); ?></span>
            </div>
        </div>

        <div class="text-center pt-6 text-[10px] text-silver-dim">
            <p>Terima kasih atas kunjungan Anda!</p>
            <p>Barang yang sudah dibeli tidak dapat ditukar kecuali ada perjanjian.</p>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/admin/pos/receipt.blade.php ENDPATH**/ ?>