<?php $__env->startSection('title', 'Laporan & Analitik Performa'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="no-print flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-giallo font-mono text-sm tracking-widest uppercase">AUDrive Intelligence</span>
                <span class="text-silver-dim text-xs">/</span>
                <span class="text-silver-dim text-xs font-mono">Executive Summary</span>
            </div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide mt-1">
                LAPORAN & ANALITIK EKSEKUTIF
            </h1>
            <p class="text-sm text-silver-dim mt-1">
                Ikhtisar komprehensif seluruh divisi: Penjualan Supercar, Workshop Servis, Suku Cadang, dan Kasir Keuangan.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-steel/60 hover:bg-steel text-silver-bright border border-gunmetal font-heading text-xs uppercase tracking-wider rounded transition-premium flex items-center gap-2 shadow-sm">
                <span>🖨️</span> Cetak Laporan Eksekutif
            </button>
        </div>
    </div>

    
    <div class="no-print flex items-center gap-2 border-b border-gunmetal overflow-x-auto pb-2">
        <a href="<?php echo e(route('admin.reports.index')); ?>" class="px-4 py-2 border-b-2 border-giallo text-giallo font-heading text-xs uppercase tracking-wider flex items-center gap-2 font-bold">
            <span>📊</span> Ikhtisar Utama
        </a>
        <?php if(in_array(auth()->user()->role ?? '', ['super-admin', 'owner', 'manager', 'sales'], true)): ?>
            <a href="<?php echo e(route('admin.reports.sales')); ?>" class="px-4 py-2 border-b-2 border-transparent text-silver-dim hover:text-silver-bright font-heading text-xs uppercase tracking-wider transition-premium flex items-center gap-2">
                <span>🏎</span> Penjualan Supercar
            </a>
        <?php endif; ?>
        <?php if(in_array(auth()->user()->role ?? '', ['super-admin', 'owner', 'manager'], true)): ?>
            <a href="<?php echo e(route('admin.reports.workshop')); ?>" class="px-4 py-2 border-b-2 border-transparent text-silver-dim hover:text-silver-bright font-heading text-xs uppercase tracking-wider transition-premium flex items-center gap-2">
                <span>🔧</span> Servis & Workshop
            </a>
        <?php endif; ?>
        <?php if(in_array(auth()->user()->role ?? '', ['super-admin', 'owner', 'manager', 'inventory'], true)): ?>
            <a href="<?php echo e(route('admin.reports.inventory')); ?>" class="px-4 py-2 border-b-2 border-transparent text-silver-dim hover:text-silver-bright font-heading text-xs uppercase tracking-wider transition-premium flex items-center gap-2">
                <span>⚙️</span> Suku Cadang & Stok
            </a>
        <?php endif; ?>
        <?php if(in_array(auth()->user()->role ?? '', ['super-admin', 'owner', 'manager', 'cashier'], true)): ?>
            <a href="<?php echo e(route('admin.reports.finance')); ?>" class="px-4 py-2 border-b-2 border-transparent text-silver-dim hover:text-silver-bright font-heading text-xs uppercase tracking-wider transition-premium flex items-center gap-2">
                <span>💳</span> Finansial & Kasir
            </a>
        <?php endif; ?>
    </div>

    
    <div class="no-print panel border border-gunmetal bg-carbon p-4">
        <form method="GET" action="<?php echo e(route('admin.reports.index')); ?>" class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            
            <div class="flex items-center gap-1.5 flex-wrap">
                <span class="text-xs font-heading uppercase text-silver-dim mr-1">Periode:</span>
                <a href="<?php echo e(route('admin.reports.index', ['preset' => 'today'])); ?>"
                   class="px-3 py-1.5 rounded text-xs font-mono uppercase <?php echo e($preset === 'today' ? 'bg-giallo text-carbon font-bold' : 'bg-steel/30 text-silver-dim hover:text-silver-bright hover:bg-steel'); ?>">
                    Hari Ini
                </a>
                <a href="<?php echo e(route('admin.reports.index', ['preset' => 'this_week'])); ?>"
                   class="px-3 py-1.5 rounded text-xs font-mono uppercase <?php echo e($preset === 'this_week' ? 'bg-giallo text-carbon font-bold' : 'bg-steel/30 text-silver-dim hover:text-silver-bright hover:bg-steel'); ?>">
                    Minggu Ini
                </a>
                <a href="<?php echo e(route('admin.reports.index', ['preset' => 'this_month'])); ?>"
                   class="px-3 py-1.5 rounded text-xs font-mono uppercase <?php echo e($preset === 'this_month' ? 'bg-giallo text-carbon font-bold' : 'bg-steel/30 text-silver-dim hover:text-silver-bright hover:bg-steel'); ?>">
                    Bulan Ini
                </a>
                <a href="<?php echo e(route('admin.reports.index', ['preset' => 'last_month'])); ?>"
                   class="px-3 py-1.5 rounded text-xs font-mono uppercase <?php echo e($preset === 'last_month' ? 'bg-giallo text-carbon font-bold' : 'bg-steel/30 text-silver-dim hover:text-silver-bright hover:bg-steel'); ?>">
                    Bulan Lalu
                </a>
                <a href="<?php echo e(route('admin.reports.index', ['preset' => 'this_year'])); ?>"
                   class="px-3 py-1.5 rounded text-xs font-mono uppercase <?php echo e($preset === 'this_year' ? 'bg-giallo text-carbon font-bold' : 'bg-steel/30 text-silver-dim hover:text-silver-bright hover:bg-steel'); ?>">
                    Tahun Ini
                </a>
                <a href="<?php echo e(route('admin.reports.index', ['preset' => 'all'])); ?>"
                   class="px-3 py-1.5 rounded text-xs font-mono uppercase <?php echo e($preset === 'all' ? 'bg-giallo text-carbon font-bold' : 'bg-steel/30 text-silver-dim hover:text-silver-bright hover:bg-steel'); ?>">
                    Semua
                </a>
            </div>

            
            <div class="flex items-center gap-2">
                <input type="date" name="start_date" value="<?php echo e($startDate?->format('Y-m-d')); ?>"
                       class="bg-steel/20 border border-gunmetal rounded px-2.5 py-1.5 text-xs text-silver-bright focus:border-giallo focus:outline-none font-mono">
                <span class="text-silver-dim text-xs">s/d</span>
                <input type="date" name="end_date" value="<?php echo e($endDate?->format('Y-m-d')); ?>"
                       class="bg-steel/20 border border-gunmetal rounded px-2.5 py-1.5 text-xs text-silver-bright focus:border-giallo focus:outline-none font-mono">
                <button type="submit" class="px-3 py-1.5 bg-steel hover:bg-steel/80 text-silver-bright border border-gunmetal text-xs rounded font-heading uppercase">
                    Terapkan
                </button>
            </div>
        </form>
    </div>

    
    <div class="hidden print:block border-b-2 border-black pb-4 mb-6">
        <table class="w-full text-black">
            <tr>
                <td style="width: 60%; vertical-align: top;">
                    <h2 class="text-2xl font-bold uppercase tracking-wider">PT AUDRIVE SUPERCAR INDONESIA</h2>
                    <p class="text-xs font-semibold uppercase text-gray-600">Showroom, Authorized Workshop & Aftermarket Division</p>
                    <p class="text-[10px] text-gray-600">Graha AUDrive Kav. 88, SCBD, Jl. Jend. Sudirman, Jakarta Selatan 12190 &bull; Telp: +62 21 8088 9900</p>
                </td>
                <td style="width: 40%; text-align: right; vertical-align: top;">
                    <h3 class="text-lg font-bold uppercase">LAPORAN IKHTISAR EKSEKUTIF</h3>
                    <p class="text-xs font-mono">Dokumen: RPT-EXE-<?php echo e(date('Ymd-His')); ?></p>
                    <p class="text-xs">Periode: <?php echo e($startDate ? $startDate->format('d/m/Y') : 'Awal'); ?> - <?php echo e($endDate ? $endDate->format('d/m/Y') : 'Sekarang'); ?></p>
                    <p class="text-[10px] text-gray-500">Dicetak: <?php echo e(now()->format('d/m/Y H:i')); ?> WIB oleh <?php echo e(auth()->user()->name); ?></p>
                </td>
            </tr>
        </table>
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
            <p class="text-[10px] text-silver-dim font-mono mt-1">Nilai: Rp <?php echo e(number_format($carRevenue, 0, ',', '.')); ?></p>
        </div>

        
        <div class="panel border border-gunmetal bg-carbon p-5 relative overflow-hidden group hover:border-verde/40 transition-premium">
            <div class="flex items-center justify-between text-silver-dim text-xs font-heading uppercase tracking-wider">
                <span>Perbaikan WO Selesai</span>
                <span class="text-verde">✅</span>
            </div>
            <p class="font-mono text-2xl font-bold text-verde mt-2">
                <?php echo e($completedWorkOrders); ?> <span class="text-xs font-normal text-silver-dim">Order</span>
            </p>
            <p class="text-[10px] text-silver-dim font-mono mt-1">Nilai Servis: Rp <?php echo e(number_format($workshopRevenue, 0, ',', '.')); ?></p>
        </div>

        
        <div class="panel border border-gunmetal bg-carbon p-5 relative overflow-hidden group hover:border-blue-400/40 transition-premium">
            <div class="flex items-center justify-between text-silver-dim text-xs font-heading uppercase tracking-wider">
                <span>Suku Cadang & Kasir POS</span>
                <span class="text-blue-400">⚙️</span>
            </div>
            <p class="font-mono text-xl lg:text-2xl font-bold text-blue-400 mt-2">
                Rp <?php echo e(number_format($sparePartRevenue, 0, ',', '.')); ?>

            </p>
            <p class="text-[10px] text-silver-dim font-mono mt-1">Total pelanggan terdaftar: <?php echo e($totalCustomers); ?></p>
        </div>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="panel border border-gunmetal bg-carbon p-4 space-y-2">
            <div class="flex items-center justify-between">
                <span class="font-heading text-xs uppercase text-giallo font-bold">1. Showroom Supercar</span>
                <span class="font-mono text-xs text-silver-bright font-bold"><?php echo e($carSalesCount); ?> Unit</span>
            </div>
            <p class="font-mono text-lg font-bold text-silver-bright">Rp <?php echo e(number_format($carRevenue, 0, ',', '.')); ?></p>
            <div class="w-full bg-steel/30 rounded-full h-1.5 overflow-hidden">
                <div class="bg-giallo h-1.5 rounded-full" style="width: <?php echo e($totalRevenue > 0 ? min(100, round(($carRevenue / $totalRevenue) * 100)) : 0); ?>%"></div>
            </div>
            <p class="text-[10px] text-silver-dim"><?php echo e($totalRevenue > 0 ? round(($carRevenue / $totalRevenue) * 100, 1) : 0); ?>% dari total omzet</p>
        </div>

        <div class="panel border border-gunmetal bg-carbon p-4 space-y-2">
            <div class="flex items-center justify-between">
                <span class="font-heading text-xs uppercase text-arancio font-bold">2. Bengkel & Servis WO</span>
                <span class="font-mono text-xs text-silver-bright font-bold"><?php echo e($completedWorkOrders); ?> WO</span>
            </div>
            <p class="font-mono text-lg font-bold text-silver-bright">Rp <?php echo e(number_format($workshopRevenue, 0, ',', '.')); ?></p>
            <div class="w-full bg-steel/30 rounded-full h-1.5 overflow-hidden">
                <div class="bg-arancio h-1.5 rounded-full" style="width: <?php echo e($totalRevenue > 0 ? min(100, round(($workshopRevenue / $totalRevenue) * 100)) : 0); ?>%"></div>
            </div>
            <p class="text-[10px] text-silver-dim"><?php echo e($totalRevenue > 0 ? round(($workshopRevenue / $totalRevenue) * 100, 1) : 0); ?>% dari total omzet &bull; Aktif: <?php echo e($activeWorkOrders); ?> WO</p>
        </div>

        <div class="panel border border-gunmetal bg-carbon p-4 space-y-2">
            <div class="flex items-center justify-between">
                <span class="font-heading text-xs uppercase text-blue-400 font-bold">3. Spare Parts & POS</span>
                <span class="font-mono text-xs text-silver-bright font-bold">Komponen</span>
            </div>
            <p class="font-mono text-lg font-bold text-silver-bright">Rp <?php echo e(number_format($sparePartRevenue, 0, ',', '.')); ?></p>
            <div class="w-full bg-steel/30 rounded-full h-1.5 overflow-hidden">
                <div class="bg-blue-400 h-1.5 rounded-full" style="width: <?php echo e($totalRevenue > 0 ? min(100, round(($sparePartRevenue / $totalRevenue) * 100)) : 0); ?>%"></div>
            </div>
            <p class="text-[10px] text-silver-dim"><?php echo e($totalRevenue > 0 ? round(($sparePartRevenue / $totalRevenue) * 100, 1) : 0); ?>% dari total omzet</p>
        </div>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <div class="panel border border-gunmetal bg-carbon overflow-hidden">
            <div class="p-4 border-b border-gunmetal flex items-center justify-between bg-steel/20">
                <div class="flex items-center gap-2">
                    <span class="text-arancio">🏎</span>
                    <h2 class="font-heading text-xs uppercase tracking-wider text-silver-bright font-bold">
                        Transaksi Penjualan Periode Ini
                    </h2>
                </div>
                <a href="<?php echo e(route('admin.reports.sales', ['start_date' => $startDate?->format('Y-m-d'), 'end_date' => $endDate?->format('Y-m-d')])); ?>" class="no-print text-[10px] font-heading uppercase text-giallo hover:underline">
                    Laporan Lengkap &rarr;
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-steel/30 text-silver-dim font-heading uppercase text-[10px] border-b border-gunmetal">
                            <th class="py-2.5 px-4">No. Order</th>
                            <th class="py-2.5 px-4">Customer</th>
                            <th class="py-2.5 px-4 text-right">Total Transaksi</th>
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
                                    <p class="font-medium text-silver-bright"><?php echo e($sale->customer->user->name ?? 'Walk-in Customer'); ?></p>
                                    <p class="text-[10px] font-mono text-silver-dim"><?php echo e($sale->created_at->format('d/m/Y H:i')); ?></p>
                                </td>
                                <td class="py-3 px-4 font-mono font-bold text-giallo text-right">
                                    Rp <?php echo e(number_format($sale->total_idr, 0, ',', '.')); ?>

                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[9px] font-mono uppercase font-bold <?php echo e(in_array($sale->status->value ?? (string)$sale->status, ['paid', 'completed']) ? 'bg-verde/20 text-verde border border-verde/40' : 'bg-steel/40 text-silver-dim'); ?>">
                                        <?php echo e($sale->status->value ?? $sale->status); ?>

                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="py-8 text-center text-silver-dim">Tidak ada transaksi penjualan pada periode ini.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        
        <div class="panel border border-gunmetal bg-carbon overflow-hidden">
            <div class="p-4 border-b border-gunmetal flex items-center justify-between bg-steel/20">
                <div class="flex items-center gap-2">
                    <span class="text-giallo">🔧</span>
                    <h2 class="font-heading text-xs uppercase tracking-wider text-silver-bright font-bold">
                        Pengerjaan Servis Bengkel
                    </h2>
                </div>
                <a href="<?php echo e(route('admin.reports.workshop', ['start_date' => $startDate?->format('Y-m-d'), 'end_date' => $endDate?->format('Y-m-d')])); ?>" class="no-print text-[10px] font-heading uppercase text-giallo hover:underline">
                    Laporan Lengkap &rarr;
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-steel/30 text-silver-dim font-heading uppercase text-[10px] border-b border-gunmetal">
                            <th class="py-2.5 px-4">No. WO</th>
                            <th class="py-2.5 px-4">Kendaraan & Pelanggan</th>
                            <th class="py-2.5 px-4 text-right">Biaya Servis</th>
                            <th class="py-2.5 px-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gunmetal/30">
                        <?php $__empty_1 = true; $__currentLoopData = $recentWorkOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-steel/10 transition-premium">
                                <td class="py-3 px-4 font-mono text-silver-bright font-bold">
                                    <?php echo e($wo->code); ?>

                                </td>
                                <td class="py-3 px-4">
                                    <p class="font-medium text-silver-bright"><?php echo e($wo->vehicle?->brand); ?> <?php echo e($wo->vehicle?->model); ?></p>
                                    <p class="text-[10px] font-mono text-silver-dim"><?php echo e($wo->vehicle?->plate_number ?? '-'); ?> &bull; <?php echo e($wo->vehicle?->customer?->user?->name ?? 'Customer'); ?></p>
                                </td>
                                <td class="py-3 px-4 font-mono font-bold text-silver-bright text-right">
                                    Rp <?php echo e(number_format($wo->total_idr ?? 0, 0, ',', '.')); ?>

                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[9px] font-mono uppercase font-bold
                                        <?php if(($wo->status->value ?? (string)$wo->status) === 'completed'): ?> bg-verde/20 text-verde border border-verde/40
                                        <?php elseif(in_array($wo->status->value ?? (string)$wo->status, ['in_progress', 'queued'])): ?> bg-arancio/20 text-arancio border border-arancio/40
                                        <?php else: ?> bg-steel/40 text-silver-dim <?php endif; ?>">
                                        <?php echo e($wo->status->value ?? $wo->status); ?>

                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="py-8 text-center text-silver-dim">Tidak ada order servis pada periode ini.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
    <div class="hidden print:block pt-10">
        <table class="w-full text-center text-black text-xs">
            <tr>
                <td style="width: 33%;">
                    <p class="text-[10px] font-semibold text-gray-600 mb-14">Dibuat Oleh (Finance Staff):</p>
                    <p class="font-bold underline"><?php echo e(auth()->user()->name); ?></p>
                    <p class="text-[9px] text-gray-500">Divisi Keuangan</p>
                </td>
                <td style="width: 33%;">
                    <p class="text-[10px] font-semibold text-gray-600 mb-14">Diperiksa Oleh (Supervisor):</p>
                    <p class="font-bold underline">Operations Manager</p>
                    <p class="text-[9px] text-gray-500">Manajer Operasional</p>
                </td>
                <td style="width: 33%;">
                    <p class="text-[10px] font-semibold text-gray-600 mb-14">Disetujui Oleh (Direktur):</p>
                    <p class="font-bold underline">Super Admin AUDrive</p>
                    <p class="text-[9px] text-gray-500">Direktur Utama</p>
                </td>
            </tr>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/admin/reports/index.blade.php ENDPATH**/ ?>