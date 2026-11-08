<?php $__env->startSection('title', 'Tagihan & Invoice'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-giallo font-mono text-sm tracking-widest uppercase">Billing & Financial Proof</span>
                <span class="text-silver-dim text-xs">/</span>
                <span class="text-silver-dim text-xs font-mono">Invoices</span>
            </div>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                TAGIHAN & INVOICE SAYA
            </h1>
            <p class="text-xs text-silver-dim mt-1">
                Arsip lengkap tagihan resmi, riwayat pelunasan, dan bukti pembayaran transaksi supercar, servis berkala, dan spare parts Anda.
            </p>
        </div>
    </div>

    
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="panel border border-gunmetal bg-carbon p-5 relative overflow-hidden group hover:border-verde/40 transition-premium">
            <span class="text-silver-dim text-xs font-heading uppercase tracking-wider">Total Pembayaran Lunas</span>
            <p class="font-mono text-xl sm:text-2xl font-bold text-verde mt-2">
                Rp <?php echo e(number_format($totalPaid, 0, ',', '.')); ?>

            </p>
            <p class="text-[10px] text-silver-dim font-mono mt-1"><?php echo e($countPaid); ?> transaksi terverifikasi selesai</p>
        </div>
        <div class="panel border border-gunmetal bg-carbon p-5 relative overflow-hidden group hover:border-arancio/40 transition-premium">
            <span class="text-silver-dim text-xs font-heading uppercase tracking-wider">Menunggu Pelunasan</span>
            <p class="font-mono text-xl sm:text-2xl font-bold text-arancio mt-2">
                Rp <?php echo e(number_format($totalPending, 0, ',', '.')); ?>

            </p>
            <p class="text-[10px] text-silver-dim font-mono mt-1">Tagihan yang perlu diselesaikan</p>
        </div>
        <div class="panel border border-gunmetal bg-carbon p-5 relative overflow-hidden group hover:border-giallo/40 transition-premium">
            <span class="text-silver-dim text-xs font-heading uppercase tracking-wider">Total Arsip Invoice</span>
            <p class="font-mono text-xl sm:text-2xl font-bold text-giallo mt-2">
                <?php echo e($invoices->total()); ?> <span class="text-xs font-normal text-silver-dim">Dokumen</span>
            </p>
            <p class="text-[10px] text-silver-dim font-mono mt-1">Dapat diunduh kapan pun</p>
        </div>
    </div>

    
    <div class="panel border border-gunmetal bg-carbon p-4">
        <form method="GET" action="<?php echo e(route('customer.invoices.index')); ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            
            <div>
                <label class="block text-[10px] font-heading uppercase tracking-wider text-silver-dim mb-1">Cari Invoice / Order</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="No. Invoice atau No. Order..."
                    class="w-full bg-steel/20 border border-gunmetal rounded px-3 py-2 text-xs text-silver-bright placeholder-silver-dim/50 focus:border-giallo focus:outline-none font-mono">
            </div>

            
            <div>
                <label class="block text-[10px] font-heading uppercase tracking-wider text-silver-dim mb-1">Status Pembayaran</label>
                <select name="status" class="w-full bg-steel/20 border border-gunmetal rounded px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                    <option value="all" <?php echo e(request('status') === 'all' ? 'selected' : ''); ?>>Semua Status</option>
                    <option value="paid" <?php echo e(request('status') === 'paid' ? 'selected' : ''); ?>>Lunas (Paid)</option>
                    <option value="draft" <?php echo e(request('status') === 'draft' ? 'selected' : ''); ?>>Menunggu Pembayaran</option>
                </select>
            </div>

            
            <div>
                <label class="block text-[10px] font-heading uppercase tracking-wider text-silver-dim mb-1">Kategori Transaksi</label>
                <select name="type" class="w-full bg-steel/20 border border-gunmetal rounded px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                    <option value="all" <?php echo e(request('type') === 'all' ? 'selected' : ''); ?>>Semua Kategori</option>
                    <option value="car_sales" <?php echo e(request('type') === 'car_sales' ? 'selected' : ''); ?>>Pembelian Supercar</option>
                    <option value="service" <?php echo e(request('type') === 'service' ? 'selected' : ''); ?>>Bengkel & Servis</option>
                    <option value="spare_part" <?php echo e(request('type') === 'spare_part' ? 'selected' : ''); ?>>Suku Cadang</option>
                </select>
            </div>

            
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-giallo hover:bg-giallo/90 text-carbon font-heading text-xs uppercase tracking-wider font-bold rounded transition-premium">
                    Filter
                </button>
                <a href="<?php echo e(route('customer.invoices.index')); ?>" class="px-3 py-2 bg-steel/40 hover:bg-steel border border-gunmetal text-silver-dim hover:text-silver-bright text-xs rounded transition-premium">
                    Reset
                </a>
            </div>
        </form>
    </div>

    
    <div class="panel border border-gunmetal bg-carbon overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-steel/30 text-silver-dim font-heading uppercase text-[10px] border-b border-gunmetal">
                        <th class="py-3 px-4">No. Dokumen Invoice</th>
                        <th class="py-3 px-4">Kategori Layanan</th>
                        <th class="py-3 px-4">Tanggal Transaksi</th>
                        <th class="py-3 px-4 text-right">Total Tagihan</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Bukti Bayar & Dokumen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/30">
                    <?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $order = $inv->invoiceable;
                            $isPaid = $inv->status->value === 'paid' || $inv->status === 'paid';
                        ?>
                        <tr class="hover:bg-steel/10 transition-premium">
                            <td class="py-3.5 px-4 font-mono">
                                <a href="<?php echo e(route('customer.invoices.show', $inv)); ?>" class="font-bold text-giallo hover:underline">
                                    <?php echo e($inv->number); ?>

                                </a>
                                <?php if($order): ?>
                                    <p class="text-[10px] text-silver-dim font-mono">Ref Order: #<?php echo e($order->code); ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-heading font-bold uppercase tracking-wider
                                    <?php if(($order?->type->value ?? $order?->type) === 'car_sales'): ?> bg-giallo/10 text-giallo border border-giallo/30
                                    <?php elseif(($order?->type->value ?? $order?->type) === 'service'): ?> bg-arancio/10 text-arancio border border-arancio/30
                                    <?php else: ?> bg-blue-500/10 text-blue-400 border border-blue-500/30 <?php endif; ?>">
                                    <?php echo e(str_replace('_', ' ', $order?->type->value ?? ($order?->type ?? 'Transaksi'))); ?>

                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-silver-dim">
                                <?php echo e($inv->created_at->format('d M Y H:i')); ?>

                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-silver-bright text-right">
                                Rp <?php echo e(number_format($inv->total_idr, 0, ',', '.')); ?>

                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <?php if($isPaid): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-mono font-bold uppercase bg-verde/15 text-verde border border-verde/40">
                                        ✓ Lunas
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-mono font-bold uppercase bg-amber-500/15 text-amber-400 border border-amber-500/40">
                                        Menunggu
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="<?php echo e(route('customer.invoices.show', $inv)); ?>" class="px-2.5 py-1 bg-steel/50 hover:bg-steel border border-gunmetal text-silver-bright rounded text-[10px] font-heading uppercase tracking-wider transition-premium">
                                        Lihat
                                    </a>
                                    <a href="<?php echo e(route('customer.invoices.pdf', $inv)); ?>" class="px-2.5 py-1 bg-giallo/20 hover:bg-giallo/30 border border-giallo/40 text-giallo rounded text-[10px] font-heading uppercase tracking-wider transition-premium flex items-center gap-1">
                                        <span>PDF</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="py-12 text-center text-silver-dim">
                                <span class="text-3xl block mb-2 opacity-50">🧾</span>
                                <p class="font-display font-bold text-silver-bright text-sm tracking-wide">Belum Ada Riwayat Invoice</p>
                                <p class="text-xs text-silver-dim mt-1">Tagihan transaksi mobil, servis, atau pembelian suku cadang Anda akan tersimpan di sini.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($invoices->hasPages()): ?>
            <div class="p-4 border-t border-gunmetal bg-steel/20">
                <?php echo e($invoices->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.customer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\BINTANG\Desktop\WEB Programming\Universitas Pamulang\Semester 7\Pemrograman Web 2\AUDRIVE-WORKSHOP\resources\views/customer/invoices/index.blade.php ENDPATH**/ ?>