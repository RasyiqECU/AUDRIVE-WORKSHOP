<?php $__env->startSection('title', 'Laporan & Analitik Performa'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-giallo font-mono text-sm tracking-widest uppercase">AUDrive Intelligence</span>
                <span class="text-silver-dim text-xs">/</span>
                <span class="text-silver-dim text-xs font-mono">Executive Summary</span>
            </div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide mt-1">
                LAPORAN & ANALITIK PERFORMA
            </h1>
            <p class="text-sm text-silver-dim mt-1">
                Ringkasan komprehensif performa penjualan supercar, workshop servis, perputaran inventori, dan arus kas.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-steel/40 hover:bg-steel text-silver-bright border border-gunmetal font-heading text-xs uppercase tracking-wider rounded transition-premium flex items-center gap-2">
                <span>🖨️</span> Cetak / Simpan PDF
            </button>
        </div>
    </div>

    
    <div class="flex items-center gap-2 border-b border-gunmetal overflow-x-auto pb-2">
        <a href="<?php echo e(route('admin.reports.index')); ?>" class="px-4 py-2 border-b-2 border-giallo text-giallo font-heading text-xs uppercase tracking-wider flex items-center gap-2">
            <span>📊</span> Ikhtisar Utama
        </a>
        <a href="<?php echo e(route('admin.reports.sales')); ?>" class="px-4 py-2 border-b-2 border-transparent text-silver-dim hover:text-silver-bright font-heading text-xs uppercase tracking-wider transition-premium flex items-center gap-2">
            <span>🏎</span> Penjualan Supercar
        </a>
        <a href="<?php echo e(route('admin.reports.workshop')); ?>" class="px-4 py-2 border-b-2 border-transparent text-silver-dim hover:text-silver-bright font-heading text-xs uppercase tracking-wider transition-premium flex items-center gap-2">
            <span>🔧</span> Servis & Workshop
        </a>
        <a href="<?php echo e(route('admin.reports.inventory')); ?>" class="px-4 py-2 border-b-2 border-transparent text-silver-dim hover:text-silver-bright font-heading text-xs uppercase tracking-wider transition-premium flex items-center gap-2">
            <span>⚙️</span> Suku Cadang & Stok
        </a>
        <a href="<?php echo e(route('admin.reports.finance')); ?>" class="px-4 py-2 border-b-2 border-transparent text-silver-dim hover:text-silver-bright font-heading text-xs uppercase tracking-wider transition-premium flex items-center gap-2">
            <span>💳</span> Finansial & Kasir
        </a>
    </div>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <div class="panel border border-gunmetal bg-carbon p-5 relative overflow-hidden group hover:border-giallo/40 transition-premium">
            <div class="flex items-center justify-between text-silver-dim text-xs font-heading uppercase tracking-wider">
                <span>Total Omzet Terverifikasi</span>
                <span class="text-giallo">💰</span>
            </div>
            <p class="font-mono text-xl lg:text-2xl font-bold text-giallo mt-2">
                Rp <?php echo e(number_format($totalRevenue, 0, ',', '.')); ?>

            </p>
            <p class="text-[10px] text-silver-dim font-mono mt-1">Akumulasi transaksi status Paid/Completed</p>
        </div>

        
        <div class="panel border border-gunmetal bg-carbon p-5 relative overflow-hidden group hover:border-arancio/40 transition-premium">
            <div class="flex items-center justify-between text-silver-dim text-xs font-heading uppercase tracking-wider">
                <span>Supercar Terjual</span>
                <span class="text-arancio">🏎</span>
            </div>
            <p class="font-mono text-2xl font-bold text-arancio mt-2">
                <?php echo e($carSalesCount); ?> <span class="text-xs font-normal text-silver-dim">Unit</span>
            </p>
            <p class="text-[10px] text-silver-dim font-mono mt-1">Tersedia: <?php echo e($stats['sales']['available_units'] ?? 0); ?> unit di showroom</p>
        </div>

        
        <div class="panel border border-gunmetal bg-carbon p-5 relative overflow-hidden group hover:border-verde/40 transition-premium">
            <div class="flex items-center justify-between text-silver-dim text-xs font-heading uppercase tracking-wider">
                <span>Perbaikan WO Selesai</span>
                <span class="text-verde">✅</span>
            </div>
            <p class="font-mono text-2xl font-bold text-verde mt-2">
                <?php echo e($completedWorkOrders); ?> <span class="text-xs font-normal text-silver-dim">Order</span>
            </p>
            <p class="text-[10px] text-silver-dim font-mono mt-1">Aktif di workshop: <?php echo e($stats['workshop']['active_work_orders'] ?? 0); ?> WO</p>
        </div>

        
        <div class="panel border border-gunmetal bg-carbon p-5 relative overflow-hidden group hover:border-blue-400/40 transition-premium">
            <div class="flex items-center justify-between text-silver-dim text-xs font-heading uppercase tracking-wider">
                <span>Total Basis Pelanggan</span>
                <span class="text-blue-400">👥</span>
            </div>
            <p class="font-mono text-2xl font-bold text-blue-400 mt-2">
                <?php echo e($totalCustomers); ?> <span class="text-xs font-normal text-silver-dim">Akun</span>
            </p>
            <p class="text-[10px] text-silver-dim font-mono mt-1">Pelanggan showroom & servis terdaftar</p>
        </div>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <div class="panel border border-gunmetal bg-carbon overflow-hidden">
            <div class="p-4 border-b border-gunmetal flex items-center justify-between bg-steel/20">
                <div class="flex items-center gap-2">
                    <span class="text-arancio">🏎</span>
                    <h2 class="font-heading text-xs uppercase tracking-wider text-silver-bright font-bold">
                        Transaksi Penjualan Terkini
                    </h2>
                </div>
                <a href="<?php echo e(route('admin.reports.sales')); ?>" class="text-[10px] font-heading uppercase text-giallo hover:underline">
                    Lihat Semua &rarr;
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-steel/30 text-silver-dim font-heading uppercase text-[10px] border-b border-gunmetal">
                            <th class="py-2.5 px-4">No. Order</th>
                            <th class="py-2.5 px-4">Customer</th>
                            <th class="py-2.5 px-4 text-right">Total</th>
                            <th class="py-2.5 px-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gunmetal/30">
                        <?php $__empty_1 = true; $__currentLoopData = $recentSales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-steel/10 transition-premium">
                                <td class="py-3 px-4 font-mono text-silver-bright">
                                    <?php echo e($sale->code); ?>

                                </td>
                                <td class="py-3 px-4">
                                    <p class="font-medium text-silver-bright"><?php echo e($sale->customer->user->name ?? 'Customer'); ?></p>
                                    <p class="text-[10px] font-mono text-silver-dim"><?php echo e($sale->created_at->format('d/m/Y')); ?></p>
                                </td>
                                <td class="py-3 px-4 font-mono font-bold text-giallo text-right">
                                    Rp <?php echo e(number_format($sale->total_idr, 0, ',', '.')); ?>

                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[9px] font-mono uppercase font-bold <?php echo e($sale->status->value === 'paid' ? 'bg-verde/20 text-verde border border-verde/40' : 'bg-steel/40 text-silver-dim'); ?>">
                                        <?php echo e($sale->status->value ?? $sale->status); ?>

                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="py-8 text-center text-silver-dim">Belum ada data transaksi penjualan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        
        <div class="panel border border-gunmetal bg-carbon overflow-hidden">
            <div class="p-4 border-b border-gunmetal flex items-center justify-between bg-steel/20">
                <div class="flex items-center gap-2">
                    <span class="text-verde">🔧</span>
                    <h2 class="font-heading text-xs uppercase tracking-wider text-silver-bright font-bold">
                        Pekerjaan Servis Terkini
                    </h2>
                </div>
                <a href="<?php echo e(route('admin.reports.workshop')); ?>" class="text-[10px] font-heading uppercase text-giallo hover:underline">
                    Lihat Semua &rarr;
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-steel/30 text-silver-dim font-heading uppercase text-[10px] border-b border-gunmetal">
                            <th class="py-2.5 px-4">WO Code</th>
                            <th class="py-2.5 px-4">Kendaraan</th>
                            <th class="py-2.5 px-4 text-right">Total Biaya</th>
                            <th class="py-2.5 px-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gunmetal/30">
                        <?php $__empty_1 = true; $__currentLoopData = $recentWorkOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-steel/10 transition-premium">
                                <td class="py-3 px-4 font-mono text-silver-bright">
                                    <?php echo e($wo->code); ?>

                                </td>
                                <td class="py-3 px-4">
                                    <p class="font-medium text-silver-bright"><?php echo e($wo->vehicle?->brand); ?> <?php echo e($wo->vehicle?->model); ?></p>
                                    <p class="text-[10px] font-mono text-silver-dim"><?php echo e($wo->vehicle?->plate_number ?? '-'); ?></p>
                                </td>
                                <td class="py-3 px-4 font-mono font-bold text-silver-bright text-right">
                                    Rp <?php echo e(number_format($wo->total_idr ?? 0, 0, ',', '.')); ?>

                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[9px] font-mono uppercase font-bold <?php echo e($wo->status->value === 'completed' ? 'bg-verde/20 text-verde border border-verde/40' : 'bg-arancio/20 text-arancio border border-arancio/40'); ?>">
                                        <?php echo e($wo->status->value ?? $wo->status); ?>

                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="py-8 text-center text-silver-dim">Belum ada data pengerjaan servis workshop.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
    <div class="panel border border-gunmetal bg-carbon p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-gunmetal pb-4">
            <div>
                <h3 class="font-heading text-sm uppercase tracking-wider text-silver-bright font-bold">
                    Ikhtisar Aset & Suku Cadang (Inventory Valuation)
                </h3>
                <p class="text-xs text-silver-dim mt-0.5">
                    Estimasi nilai aset suku cadang dan kesiapan stok di gudang AUDrive
                </p>
            </div>
            <a href="<?php echo e(route('admin.reports.inventory')); ?>" class="px-3 py-1.5 bg-steel/50 hover:bg-steel border border-gunmetal text-silver-bright font-heading text-[10px] uppercase rounded transition-premium">
                Buka Laporan Inventori Lengkap
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-5">
            <div>
                <span class="text-silver-dim text-[11px] font-heading uppercase">Total Item Terkatalog</span>
                <p class="font-mono text-2xl font-bold text-silver-bright mt-1">
                    <?php echo e($stats['inventory']['total_items'] ?? 0); ?> <span class="text-xs font-normal text-silver-dim">SKU</span>
                </p>
            </div>
            <div>
                <span class="text-silver-dim text-[11px] font-heading uppercase">Total Valuasi Suku Cadang</span>
                <p class="font-mono text-2xl font-bold text-giallo mt-1">
                    Rp <?php echo e(number_format($stats['inventory']['total_parts_value'] ?? 0, 0, ',', '.')); ?>

                </p>
            </div>
            <div>
                <span class="text-silver-dim text-[11px] font-heading uppercase">Stok Di Bawah Batas Minimum</span>
                <p class="font-mono text-2xl font-bold text-danger mt-1">
                    <?php echo e($stats['inventory']['low_stock_parts'] ?? 0); ?> <span class="text-xs font-normal text-silver-dim">Perlu Restock</span>
                </p>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/admin/reports/index.blade.php ENDPATH**/ ?>