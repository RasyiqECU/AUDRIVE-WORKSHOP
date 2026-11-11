<?php $__env->startSection('title', 'Struk Transaksi POS #' . $salesOrder->code); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-2xl mx-auto">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <a href="<?php echo e(route('admin.pos.index')); ?>" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1">
            ← Transaksi Kasir Baru
        </a>
        <div class="flex items-center gap-2">
            <a href="<?php echo e(route('admin.pos.print', $salesOrder)); ?>" target="_blank" class="px-4 py-2 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright font-heading text-xs uppercase tracking-wider rounded flex items-center gap-1.5 transition-premium">
                <span>🖨️</span> Cetak Thermal (80mm)
            </a>
            <?php if($salesOrder->invoice): ?>
                <a href="<?php echo e(route('admin.invoices.pdf', $salesOrder->invoice)); ?>" target="_blank" class="px-4 py-2 bg-giallo hover:bg-giallo/90 text-carbon font-heading text-xs font-bold uppercase tracking-wider rounded flex items-center gap-1.5 transition-premium shadow-md shadow-giallo/20">
                    <span>📄</span> Download Invoice Resmi
                </a>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="panel border border-gunmetal/70 bg-carbon p-6 md:p-8 space-y-5 font-mono text-xs text-silver-muted shadow-2xl relative overflow-hidden">
        
        <div class="absolute top-4 right-4 border-2 border-emerald-500/60 bg-emerald-950/40 text-emerald-400 font-heading text-[10px] font-bold px-3 py-1 rounded uppercase tracking-widest rotate-6">
            ✓ LUNAS (PAID)
        </div>

        <div class="text-center space-y-1.5 border-b border-gunmetal/60 pb-5">
            <h2 class="font-display text-xl text-silver-bright font-bold tracking-widest">AUDRIVE MOTORS</h2>
            <p class="text-[11px] text-silver-dim">Luxury Dealership & Performance Workshop</p>
            <p class="text-[10px] text-silver-dim">SCBD Sudirman Kav. 88, Jakarta Selatan</p>
            <div class="pt-2 text-[11px]">
                <span class="text-giallo font-bold">#<?php echo e($salesOrder->code); ?></span>
                <span class="text-silver-dim mx-1.5">&bull;</span>
                <span class="text-silver-dim"><?php echo e($salesOrder->created_at->format('d/m/Y H:i:s')); ?> WIB</span>
            </div>
            <?php if($salesOrder->customer): ?>
                <p class="text-[11px] text-silver-bright font-semibold pt-1">
                    Customer: <span class="text-giallo"><?php echo e($salesOrder->customer->user?->name); ?></span>
                    <?php if($salesOrder->customer->user?->phone): ?>
                        <span class="text-silver-dim font-normal">(<?php echo e($salesOrder->customer->user->phone); ?>)</span>
                    <?php endif; ?>
                </p>
            <?php else: ?>
                <p class="text-[10px] text-silver-dim italic pt-1">Walk-in Customer</p>
            <?php endif; ?>
        </div>

        
        <div class="space-y-3 py-3 border-b border-gunmetal/60">
            <?php $__currentLoopData = $salesOrder->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex justify-between items-start">
                    <div class="pr-4">
                        <p class="text-silver-bright font-medium text-xs"><?php echo e($item->description ?: ($item->sparePart?->name ?? 'Item Transaksi')); ?></p>
                        <p class="text-[10px] text-silver-dim"><?php echo e($item->qty); ?> x Rp <?php echo e(number_format($item->unit_price_idr, 0, ',', '.')); ?></p>
                    </div>
                    <p class="text-silver-bright font-bold font-mono text-xs whitespace-nowrap">
                        Rp <?php echo e(number_format($item->subtotal_idr, 0, ',', '.')); ?>

                    </p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <div class="space-y-2 pt-2 text-xs">
            <div class="flex justify-between text-silver-dim">
                <span>Subtotal:</span>
                <span class="font-mono text-silver-bright">Rp <?php echo e(number_format($salesOrder->subtotal_idr, 0, ',', '.')); ?></span>
            </div>
            <?php if($salesOrder->discount_idr > 0): ?>
                <div class="flex justify-between text-emerald-400">
                    <span>Diskon Promo:</span>
                    <span class="font-mono">- Rp <?php echo e(number_format($salesOrder->discount_idr, 0, ',', '.')); ?></span>
                </div>
            <?php endif; ?>
            <?php if($salesOrder->tax_idr > 0): ?>
                <div class="flex justify-between text-silver-dim">
                    <span>PPN (11%):</span>
                    <span class="font-mono text-silver-bright">Rp <?php echo e(number_format($salesOrder->tax_idr, 0, ',', '.')); ?></span>
                </div>
            <?php endif; ?>
            <div class="flex justify-between text-silver-bright text-base font-bold border-t border-gunmetal/60 pt-3">
                <span class="font-heading tracking-wide">TOTAL:</span>
                <span class="text-giallo font-mono">Rp <?php echo e(number_format($salesOrder->total_idr, 0, ',', '.')); ?></span>
            </div>
            <div class="flex justify-between text-[11px] text-silver-dim pt-1 border-t border-gunmetal/40">
                <span>Metode Pembayaran:</span>
                <span class="uppercase font-bold text-silver-bright"><?php echo e($salesOrder->payments?->first()?->method ?? 'CASH'); ?></span>
            </div>
            <?php if($salesOrder->payments?->first()?->reference): ?>
                <div class="flex justify-between text-[10px] text-silver-dim">
                    <span>No. Referensi Transaksi:</span>
                    <span class="font-mono"><?php echo e($salesOrder->payments->first()->reference); ?></span>
                </div>
            <?php endif; ?>
            <div class="flex justify-between text-[10px] text-silver-dim">
                <span>Kasir Petugas:</span>
                <span><?php echo e($salesOrder->creator?->name ?? 'Kasir POS'); ?></span>
            </div>
        </div>

        <div class="text-center pt-6 text-[10px] text-silver-dim space-y-1">
            <p class="text-silver-bright font-semibold">Terima Kasih Atas Kunjungan Anda!</p>
            <p>Barang yang sudah dibeli tidak dapat ditukar kecuali terdapat perjanjian tertulis resmi.</p>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/admin/pos/receipt.blade.php ENDPATH**/ ?>