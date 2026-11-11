<?php
    $user = auth()->user();
?>

<?php $__env->startSection('title', 'Executive & Owner Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">

    
    
    
    <div class="print-only hidden p-4 mb-6 border-b-2 border-black">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 75px; vertical-align: middle;">
                    <img src="<?php echo e(\App\Services\SettingService::getLogo()); ?>" alt="Logo" style="height: 60px; width: auto; object-fit: contain;">
                </td>
                <td style="vertical-align: middle; padding-left: 15px;">
                    <h1 style="font-size: 18px; font-weight: 800; text-transform: uppercase; margin: 0; color: #000; letter-spacing: 1px;">
                        <?php echo e(\App\Services\SettingService::getName()); ?>

                    </h1>
                    <p style="font-size: 11px; margin: 2px 0; color: #444;">
                        Supercar Showroom, Authorized Workshop & High-Performance Parts
                    </p>
                    <p style="font-size: 9px; margin: 0; color: #666;">
                        <?php echo e(\App\Services\SettingService::get('company_address', 'Jl. Boulevard Supercar No. 88, Jakarta Selatan')); ?> | Telp: <?php echo e(\App\Services\SettingService::get('company_phone', '+62 21 8888 9999')); ?>

                    </p>
                </td>
                <td style="width: 200px; text-align: right; vertical-align: middle;">
                    <div style="border-left: 2px solid #ccc; padding-left: 10px;">
                        <span style="font-size: 10px; font-weight: bold; text-transform: uppercase; display: block; color: #000;">
                            LAPORAN EKSEKUTIF OWNER
                        </span>
                        <span style="font-size: 9px; color: #555; display: block;">
                            Periode: <?php echo e($periodLabel); ?>

                        </span>
                        <span style="font-size: 8px; color: #777;">
                            Dicetak: <?php echo e(now()->translatedFormat('d F Y H:i')); ?> WIB
                        </span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    
    
    
    <div class="relative overflow-hidden border border-gunmetal bg-carbon p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <img src="<?php echo e(\App\Services\SettingService::getDashboardBackground()); ?>" 
             alt="Banner" 
             class="absolute inset-0 w-full h-full object-cover object-center pointer-events-none opacity-20 select-none z-0" />
        <div class="absolute inset-0 bg-gradient-to-r from-carbon via-carbon/95 to-carbon/75 z-0 pointer-events-none"></div>

        <div class="relative z-10">
            <div class="flex items-center gap-3">
                <span class="px-2.5 py-0.5 rounded border text-[9px] font-heading font-bold uppercase tracking-widest text-amber-400 border-amber-400/40 bg-amber-400/10">
                    BUSINESS OWNER CONSOLE
                </span>
                <span class="text-silver-dim text-xs font-mono">• Terverifikasi Real-Time</span>
            </div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide mt-2">
                Executive Command &amp; Reporting Center
            </h1>
            <p class="text-xs text-silver-dim mt-1">
                Halo, <span class="text-giallo font-semibold"><?php echo e($user->name); ?></span> — Laporan kinerja komprehensif seluruh lini bisnis AUDrive.
            </p>
        </div>

        <div class="relative z-10 no-print flex items-center gap-2">
            <button onclick="window.print()" class="px-4 py-2.5 bg-giallo hover:bg-arancio text-nero font-heading font-bold text-xs uppercase tracking-wider transition-premium clip-angular-button shadow-md shadow-giallo/10 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak Laporan A4 / PDF</span>
            </button>
        </div>
    </div>

    
    
    
    <div class="no-print panel p-4 border border-gunmetal bg-carbon">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            
            <div class="flex flex-wrap items-center gap-1.5">
                <span class="font-heading text-[10px] uppercase tracking-wider text-silver-dim mr-2">Filter Periode:</span>
                
                <a href="<?php echo e(route('admin.dashboard', ['preset' => 'today'])); ?>" 
                   class="px-2.5 py-1 text-xs font-heading font-medium rounded transition-premium border <?php echo e($preset === 'today' ? 'bg-giallo text-nero border-giallo font-bold shadow-sm' : 'bg-steel/30 text-silver-dim border-gunmetal hover:text-silver-bright hover:border-silver-dim'); ?>">
                    Hari Ini
                </a>

                <a href="<?php echo e(route('admin.dashboard', ['preset' => 'yesterday'])); ?>" 
                   class="px-2.5 py-1 text-xs font-heading font-medium rounded transition-premium border <?php echo e($preset === 'yesterday' ? 'bg-giallo text-nero border-giallo font-bold shadow-sm' : 'bg-steel/30 text-silver-dim border-gunmetal hover:text-silver-bright hover:border-silver-dim'); ?>">
                    Kemarin
                </a>

                <a href="<?php echo e(route('admin.dashboard', ['preset' => 'last_7_days'])); ?>" 
                   class="px-2.5 py-1 text-xs font-heading font-medium rounded transition-premium border <?php echo e($preset === 'last_7_days' ? 'bg-giallo text-nero border-giallo font-bold shadow-sm' : 'bg-steel/30 text-silver-dim border-gunmetal hover:text-silver-bright hover:border-silver-dim'); ?>">
                    7 Hari Terakhir
                </a>

                <a href="<?php echo e(route('admin.dashboard', ['preset' => 'this_month'])); ?>" 
                   class="px-2.5 py-1 text-xs font-heading font-medium rounded transition-premium border <?php echo e($preset === 'this_month' ? 'bg-giallo text-nero border-giallo font-bold shadow-sm' : 'bg-steel/30 text-silver-dim border-gunmetal hover:text-silver-bright hover:border-silver-dim'); ?>">
                    Bulan Ini (Saat Ini)
                </a>

                <a href="<?php echo e(route('admin.dashboard', ['preset' => 'last_month'])); ?>" 
                   class="px-2.5 py-1 text-xs font-heading font-medium rounded transition-premium border <?php echo e($preset === 'last_month' ? 'bg-giallo text-nero border-giallo font-bold shadow-sm' : 'bg-steel/30 text-silver-dim border-gunmetal hover:text-silver-bright hover:border-silver-dim'); ?>">
                    Bulan Lalu
                </a>

                <a href="<?php echo e(route('admin.dashboard', ['preset' => 'this_year'])); ?>" 
                   class="px-2.5 py-1 text-xs font-heading font-medium rounded transition-premium border <?php echo e($preset === 'this_year' ? 'bg-giallo text-nero border-giallo font-bold shadow-sm' : 'bg-steel/30 text-silver-dim border-gunmetal hover:text-silver-bright hover:border-silver-dim'); ?>">
                    Tahun Ini
                </a>

                <a href="<?php echo e(route('admin.dashboard', ['preset' => 'last_year'])); ?>" 
                   class="px-2.5 py-1 text-xs font-heading font-medium rounded transition-premium border <?php echo e($preset === 'last_year' ? 'bg-giallo text-nero border-giallo font-bold shadow-sm' : 'bg-steel/30 text-silver-dim border-gunmetal hover:text-silver-bright hover:border-silver-dim'); ?>">
                    Tahun Lalu
                </a>
            </div>

            
            <form method="GET" action="<?php echo e(route('admin.dashboard')); ?>" class="flex flex-wrap items-center gap-2">
                <input type="hidden" name="preset" value="custom">
                <span class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Kustom:</span>
                <input type="date" name="start_date" value="<?php echo e($startDate->format('Y-m-d')); ?>" 
                       class="bg-nero border border-gunmetal px-2.5 py-1 text-xs text-silver-bright focus:border-giallo focus:outline-none rounded">
                <span class="text-silver-dim text-xs">s/d</span>
                <input type="date" name="end_date" value="<?php echo e($endDate->format('Y-m-d')); ?>" 
                       class="bg-nero border border-gunmetal px-2.5 py-1 text-xs text-silver-bright focus:border-giallo focus:outline-none rounded">
                <button type="submit" class="px-3 py-1 bg-steel hover:bg-steel/80 text-silver-bright text-xs font-heading uppercase tracking-wider rounded border border-gunmetal transition-premium">
                    Terapkan
                </button>
            </form>
        </div>

        
        <div class="mt-3 pt-3 border-t border-gunmetal/60 flex items-center justify-between text-xs">
            <div class="flex items-center gap-2">
                <span class="inline-block w-2 h-2 rounded-full bg-verde animate-pulse"></span>
                <span class="text-silver-dim">Menampilkan Pelaporan Periode:</span>
                <strong class="text-giallo font-mono"><?php echo e($periodLabel); ?></strong>
            </div>
            <div class="text-silver-dim font-mono text-[11px]">
                Rentang Data: <?php echo e($startDate->translatedFormat('d F Y')); ?> — <?php echo e($endDate->translatedFormat('d F Y')); ?>

            </div>
        </div>
    </div>

    
    
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <div class="panel p-5 border border-gunmetal bg-carbon relative overflow-hidden">
            <div class="flex items-center justify-between text-silver-dim mb-1">
                <span class="font-heading text-[11px] uppercase tracking-wider">Total Omzet Bersih</span>
                <span class="text-base">💰</span>
            </div>
            <p class="font-display text-2xl md:text-3xl font-bold text-giallo mt-1">
                Rp <?php echo e(number_format($totalRevenue, 0, ',', '.')); ?>

            </p>
            <div class="flex items-center justify-between mt-3 pt-3 border-t border-gunmetal/60 text-xs font-mono">
                <span class="<?php echo e($revenueGrowth >= 0 ? 'text-verde font-bold' : 'text-danger font-bold'); ?> flex items-center gap-1">
                    <?php echo e($revenueGrowth >= 0 ? '↑ +' : '↓ '); ?><?php echo e($revenueGrowth); ?>%
                    <span class="text-[10px] text-silver-dim font-normal">vs periode lalu</span>
                </span>
                <span class="text-silver-dim"><?php echo e($totalOrdersCount); ?> Trx Lunas</span>
            </div>
        </div>

        
        <div class="panel p-5 border border-gunmetal bg-carbon relative overflow-hidden">
            <div class="flex items-center justify-between text-silver-dim mb-1">
                <span class="font-heading text-[11px] uppercase tracking-wider">Showroom Supercar</span>
                <span class="text-base">🏎️</span>
            </div>
            <p class="font-display text-2xl md:text-3xl font-bold text-arancio mt-1">
                Rp <?php echo e(number_format($carRevenue, 0, ',', '.')); ?>

            </p>
            <div class="flex items-center justify-between mt-3 pt-3 border-t border-gunmetal/60 text-xs font-mono">
                <span class="text-silver-bright font-semibold">
                    <?php echo e($carsSoldCount); ?> Unit <span class="text-silver-dim font-normal">Terjual</span>
                </span>
                <span class="text-silver-dim text-[11px]">
                    Avg: Rp <?php echo e(number_format($avgCarPrice / 1000000, 1, ',', '.')); ?> Jt
                </span>
            </div>
        </div>

        
        <div class="panel p-5 border border-gunmetal bg-carbon relative overflow-hidden">
            <div class="flex items-center justify-between text-silver-dim mb-1">
                <span class="font-heading text-[11px] uppercase tracking-wider">Workshop &amp; Servis</span>
                <span class="text-base">🔧</span>
            </div>
            <p class="font-display text-2xl md:text-3xl font-bold text-blue-400 mt-1">
                Rp <?php echo e(number_format($workshopRevenue, 0, ',', '.')); ?>

            </p>
            <div class="flex items-center justify-between mt-3 pt-3 border-t border-gunmetal/60 text-xs font-mono">
                <span class="text-silver-bright font-semibold">
                    <?php echo e($completedWorkOrders); ?> WO <span class="text-silver-dim font-normal">Selesai</span>
                </span>
                <span class="text-giallo font-semibold">
                    <?php echo e($activeWorkOrders); ?> Aktif di Bay
                </span>
            </div>
        </div>

        
        <div class="panel p-5 border border-gunmetal bg-carbon relative overflow-hidden">
            <div class="flex items-center justify-between text-silver-dim mb-1">
                <span class="font-heading text-[11px] uppercase tracking-wider">Spare Parts &amp; Retail</span>
                <span class="text-base">⚙️</span>
            </div>
            <p class="font-display text-2xl md:text-3xl font-bold text-verde mt-1">
                Rp <?php echo e(number_format($partRevenue, 0, ',', '.')); ?>

            </p>
            <div class="flex items-center justify-between mt-3 pt-3 border-t border-gunmetal/60 text-xs font-mono">
                <span class="text-silver-bright font-semibold">
                    <?php echo e($partOrdersCount); ?> Pesanan Part
                </span>
                <?php if($pendingRevenue > 0): ?>
                    <span class="text-amber-400 text-[11px]">Pending: Rp <?php echo e(number_format($pendingRevenue / 1000000, 1, ',', '.')); ?> Jt</span>
                <?php else: ?>
                    <span class="text-verde text-[11px]">Semua Lunas</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    
    
    <div class="panel p-5 border border-gunmetal bg-carbon">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="font-heading text-sm uppercase tracking-wider text-silver-bright">
                    Ringkasan Finansial & Arus Kas Operasional
                </h2>
                <p class="text-xs text-silver-dim mt-0.5">Komparasi pendapatan masuk vs pengadaan suku cadang dalam periode terpilih</p>
            </div>
            <span class="font-mono text-xs text-silver-dim bg-steel/30 px-2 py-1 rounded border border-gunmetal">
                Periode: <?php echo e($periodLabel); ?>

            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="p-4 bg-nero border border-gunmetal rounded">
                <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Kas Masuk (Omzet Diterima)</p>
                <p class="font-display text-xl font-bold text-verde mt-1">
                    Rp <?php echo e(number_format($totalRevenue, 0, ',', '.')); ?>

                </p>
                <p class="font-mono text-[11px] text-silver-dim mt-1">Dari <?php echo e($totalOrdersCount); ?> transaksi terbayar</p>
            </div>

            <div class="p-4 bg-nero border border-gunmetal rounded">
                <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Pengeluaran Pengadaan (PO Stok)</p>
                <p class="font-display text-xl font-bold text-rosso mt-1">
                    Rp <?php echo e(number_format($totalPurchases, 0, ',', '.')); ?>

                </p>
                <p class="font-mono text-[11px] text-silver-dim mt-1">Belanja stok spare part gudang</p>
            </div>

            <div class="p-4 bg-nero border border-gunmetal rounded">
                <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Estimasi Kas Bersih (Net Inflow)</p>
                <p class="font-display text-xl font-bold <?php echo e($netInflow >= 0 ? 'text-giallo' : 'text-danger'); ?> mt-1">
                    Rp <?php echo e(number_format($netInflow, 0, ',', '.')); ?>

                </p>
                <p class="font-mono text-[11px] text-silver-dim mt-1">Selisih penjualan vs belanja stok</p>
            </div>

            <div class="p-4 bg-nero border border-gunmetal rounded">
                <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Piutang / Tagihan Pending</p>
                <p class="font-display text-xl font-bold text-amber-400 mt-1">
                    Rp <?php echo e(number_format($pendingRevenue, 0, ',', '.')); ?>

                </p>
                <p class="font-mono text-[11px] text-silver-dim mt-1"><?php echo e($pendingCount); ?> tagihan menunggu settlement</p>
            </div>
        </div>
    </div>

    
    
    
    <div class="panel p-5 border border-gunmetal bg-carbon">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
            <div>
                <h2 class="font-heading text-sm uppercase tracking-wider text-silver-bright">
                    Grafik Distribusi Pendapatan
                </h2>
                <p class="text-xs text-silver-dim mt-0.5">Visualisasi omzet harian/bulanan berdasarkan divisi bisnis AUDrive</p>
            </div>
            <div class="flex items-center gap-4 text-xs font-mono">
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 bg-arancio rounded-sm inline-block"></span>
                    <span class="text-silver-bright">Showroom</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 bg-blue-500 rounded-sm inline-block"></span>
                    <span class="text-silver-bright">Workshop</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 bg-verde rounded-sm inline-block"></span>
                    <span class="text-silver-bright">Spare Parts</span>
                </div>
            </div>
        </div>

        <?php
            $maxPointRev = max(1, collect($trendData)->max('total_rev') ?? 1);
        ?>

        <?php if(count($trendData) > 0): ?>
            <div class="overflow-x-auto pb-2">
                <div class="min-w-[650px] space-y-2">
                    <?php $__currentLoopData = $trendData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $point): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $carPercent = $maxPointRev > 0 ? ($point['car_rev'] / $maxPointRev) * 100 : 0;
                            $wsPercent  = $maxPointRev > 0 ? ($point['workshop_rev'] / $maxPointRev) * 100 : 0;
                            $ptPercent  = $maxPointRev > 0 ? ($point['part_rev'] / $maxPointRev) * 100 : 0;
                            $totPercent = min(100, $carPercent + $wsPercent + $ptPercent);
                        ?>
                        <div class="flex items-center gap-3 text-xs">
                            <span class="w-20 font-mono text-[11px] text-silver-dim truncate" title="<?php echo e($point['full_date']); ?>">
                                <?php echo e($point['label']); ?>

                            </span>
                            <div class="flex-1 bg-nero rounded h-5 p-0.5 flex overflow-hidden border border-gunmetal/60 relative group cursor-pointer" title="<?php echo e($point['full_date']); ?>: Rp <?php echo e(number_format($point['total_rev'], 0, ',', '.')); ?>">
                                <?php if($point['car_rev'] > 0): ?>
                                    <div style="width: <?php echo e($carPercent); ?>%" class="bg-arancio h-full transition-all duration-300"></div>
                                <?php endif; ?>
                                <?php if($point['workshop_rev'] > 0): ?>
                                    <div style="width: <?php echo e($wsPercent); ?>%" class="bg-blue-500 h-full transition-all duration-300"></div>
                                <?php endif; ?>
                                <?php if($point['part_rev'] > 0): ?>
                                    <div style="width: <?php echo e($ptPercent); ?>%" class="bg-verde h-full transition-all duration-300"></div>
                                <?php endif; ?>
                                <?php if($point['total_rev'] === 0): ?>
                                    <div class="w-full h-full flex items-center justify-center text-[10px] text-gunmetal font-mono">
                                        -
                                    </div>
                                <?php endif; ?>
                            </div>
                            <span class="w-32 font-mono text-[11px] text-right font-semibold <?php echo e($point['total_rev'] > 0 ? 'text-giallo' : 'text-silver-dim'); ?>">
                                Rp <?php echo e(number_format($point['total_rev'], 0, ',', '.')); ?>

                            </span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center py-8 text-silver-dim text-xs font-mono">
                Tidak ada data transaksi terverifikasi pada rentang waktu ini.
            </div>
        <?php endif; ?>
    </div>

    
    
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="panel p-5 border border-gunmetal bg-carbon">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright">Inventaris Showroom Mobil</h3>
                <span class="text-xs">🏎️</span>
            </div>
            <div class="space-y-3 text-xs">
                <div class="flex items-center justify-between py-1.5 border-b border-gunmetal/60">
                    <span class="text-silver-dim">Unit Siap Jual (Ready Stock)</span>
                    <strong class="font-mono text-verde text-sm"><?php echo e($readyCarsCount); ?> Unit</strong>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-gunmetal/60">
                    <span class="text-silver-dim">Estimasi Valuasi Showroom</span>
                    <strong class="font-mono text-giallo text-sm">Rp <?php echo e(number_format($readyCarsValuation, 0, ',', '.')); ?></strong>
                </div>
                <div class="flex items-center justify-between py-1.5">
                    <span class="text-silver-dim">Unit Terjual Periode Ini</span>
                    <strong class="font-mono text-arancio text-sm"><?php echo e($carsSoldCount); ?> Unit</strong>
                </div>
            </div>
        </div>

        
        <div class="panel p-5 border border-gunmetal bg-carbon">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright">Valuasi Gudang Spare Part</h3>
                <span class="text-xs">📦</span>
            </div>
            <div class="space-y-3 text-xs">
                <div class="flex items-center justify-between py-1.5 border-b border-gunmetal/60">
                    <span class="text-silver-dim">Total Fisik Suku Cadang</span>
                    <strong class="font-mono text-silver-bright text-sm"><?php echo e(number_format($totalPartsStock, 0, ',', '.')); ?> Pcs</strong>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-gunmetal/60">
                    <span class="text-silver-dim">Valuasi Aset Pergudangan</span>
                    <strong class="font-mono text-giallo text-sm">Rp <?php echo e(number_format($totalPartsValuation, 0, ',', '.')); ?></strong>
                </div>
                <div class="flex items-center justify-between py-1.5">
                    <span class="text-silver-dim">Peringatan Stok Menipis</span>
                    <?php if($lowStockCount > 0): ?>
                        <strong class="font-mono text-rosso text-sm font-bold"><?php echo e($lowStockCount); ?> Item Kritis</strong>
                    <?php else: ?>
                        <strong class="font-mono text-verde text-sm">Stok Aman</strong>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="panel p-5 border border-gunmetal bg-carbon">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright">Basis Pelanggan & CRM</h3>
                <span class="text-xs">👥</span>
            </div>
            <div class="space-y-3 text-xs">
                <div class="flex items-center justify-between py-1.5 border-b border-gunmetal/60">
                    <span class="text-silver-dim">Total Akun Pelanggan</span>
                    <strong class="font-mono text-silver-bright text-sm"><?php echo e($totalCustomers); ?> Akun</strong>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-gunmetal/60">
                    <span class="text-silver-dim">Pelanggan Baru Periode Ini</span>
                    <strong class="font-mono text-verde text-sm">+<?php echo e($newCustomers); ?> Orang</strong>
                </div>
                <div class="flex items-center justify-between py-1.5">
                    <span class="text-silver-dim">Pelanggan Loyal (Repeat Order)</span>
                    <strong class="font-mono text-giallo text-sm"><?php echo e($repeatCustomers); ?> Customer</strong>
                </div>
            </div>
        </div>
    </div>

    
    
    
    <div class="panel p-5 border border-gunmetal bg-carbon">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-heading text-sm uppercase tracking-wider text-silver-bright">
                    Transaksi Terkini Dalam Periode
                </h3>
                <p class="text-xs text-silver-dim mt-0.5">Daftar transaksi penjualan mobil, bengkel, dan suku cadang terbaru</p>
            </div>
            <span class="text-xs font-mono text-silver-dim bg-steel/20 px-2 py-0.5 rounded border border-gunmetal">
                <?php echo e(count($recentTransactions)); ?> Transaksi
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-nero text-silver-dim font-heading uppercase text-[10px] tracking-wider border-b border-gunmetal">
                    <tr>
                        <th class="py-2.5 px-3">Kode Order</th>
                        <th class="py-2.5 px-3">Tanggal</th>
                        <th class="py-2.5 px-3">Customer</th>
                        <th class="py-2.5 px-3">Kategori</th>
                        <th class="py-2.5 px-3">Metode Bayar</th>
                        <th class="py-2.5 px-3">Status</th>
                        <th class="py-2.5 px-3 text-right">Total Transaksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/50 font-mono">
                    <?php $__empty_1 = true; $__currentLoopData = $recentTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-steel/20 transition-colors">
                            <td class="py-3 px-3 text-silver-bright font-bold">
                                <?php echo e($order->order_number ?? ('TRX-' . str_pad((string)$order->id, 5, '0', STR_PAD_LEFT))); ?>

                            </td>
                            <td class="py-3 px-3 text-silver-dim">
                                <?php echo e($order->created_at->translatedFormat('d M Y H:i')); ?>

                            </td>
                            <td class="py-3 px-3 font-sans text-silver-bright">
                                <?php echo e($order->customer?->user?->name ?? 'Guest Customer'); ?>

                            </td>
                            <td class="py-3 px-3">
                                <?php if($order->type === \App\Enums\SalesOrderType::CarSales): ?>
                                    <span class="px-2 py-0.5 rounded text-[10px] bg-arancio/20 border border-arancio/40 text-arancio font-bold">
                                        Supercar
                                    </span>
                                <?php elseif($order->type === \App\Enums\SalesOrderType::Service): ?>
                                    <span class="px-2 py-0.5 rounded text-[10px] bg-blue-500/20 border border-blue-500/40 text-blue-400 font-bold">
                                        Workshop
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 py-0.5 rounded text-[10px] bg-verde/20 border border-verde/40 text-verde font-bold">
                                        Spare Part
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-3 text-silver-dim uppercase">
                                <?php echo e($order->payment_method ?? 'Transfer'); ?>

                            </td>
                            <td class="py-3 px-3">
                                <?php if(in_array($order->status, [\App\Enums\SalesOrderStatus::Paid, \App\Enums\SalesOrderStatus::Completed])): ?>
                                    <span class="px-2 py-0.5 rounded text-[10px] bg-verde/20 border border-verde text-verde font-bold">
                                        LUNAS
                                    </span>
                                <?php elseif($order->status === \App\Enums\SalesOrderStatus::Pending): ?>
                                    <span class="px-2 py-0.5 rounded text-[10px] bg-amber-500/20 border border-amber-500 text-amber-400 font-bold">
                                        PENDING
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 py-0.5 rounded text-[10px] bg-steel border border-gunmetal text-silver-dim">
                                        <?php echo e(strtoupper($order->status->value ?? 'DRAFT')); ?>

                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-3 text-right font-bold text-giallo">
                                Rp <?php echo e(number_format($order->total_idr, 0, ',', '.')); ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="py-6 text-center text-silver-dim font-mono">
                                Tidak ada catatan transaksi pada periode yang dipilih.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    
    
    <div class="print-only hidden pt-8 mt-6 border-t-2 border-black" style="page-break-inside: avoid;">
        <table style="width: 100%; border-collapse: collapse; text-align: center; color: #000;">
            <tr>
                <td style="width: 50%; padding-bottom: 70px; vertical-align: top;">
                    <p style="font-size: 11px; margin: 0; font-weight: bold; text-transform: uppercase;">Mengetahui & Menyetujui,</p>
                    <p style="font-size: 10px; margin: 2px 0 0; color: #555;">Chief Financial Officer (CFO)</p>
                </td>
                <td style="width: 50%; padding-bottom: 70px; vertical-align: top;">
                    <p style="font-size: 11px; margin: 0; font-weight: bold; text-transform: uppercase;">Jakarta, <?php echo e(now()->translatedFormat('d F Y')); ?></p>
                    <p style="font-size: 10px; margin: 2px 0 0; color: #555;">Business Owner / Komisaris Utama</p>
                </td>
            </tr>
            <tr>
                <td>
                    <p style="font-size: 12px; margin: 0; font-weight: bold; text-decoration: underline;">( .................................................... )</p>
                    <p style="font-size: 9px; margin: 2px 0 0; color: #666;">Direktorat Keuangan AUDrive</p>
                </td>
                <td>
                    <p style="font-size: 12px; margin: 0; font-weight: bold; text-decoration: underline;">( <?php echo e($user->name); ?> )</p>
                    <p style="font-size: 9px; margin: 2px 0 0; color: #666;">Business Owner</p>
                </td>
            </tr>
        </table>
    </div>

</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/admin/dashboard/owner.blade.php ENDPATH**/ ?>