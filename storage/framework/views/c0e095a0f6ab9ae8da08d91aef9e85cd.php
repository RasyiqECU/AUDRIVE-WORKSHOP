

<?php
    $user = auth()->user();
    $role = $user->role ?? 'admin';
    $roleTitles = [
        'super-admin' => ['title' => 'Executive Command Center', 'desc' => 'Ringkasan komprehensif performa seluruh divisi AUDrive'],
        'manager'     => ['title' => 'Operations & Management Dashboard', 'desc' => 'Monitoring performa operasional, approval, dan sales pipeline'],
        'sales'       => ['title' => 'Sales & Showroom Dashboard', 'desc' => 'Performa penjualan supercar, pipeline negosiasi, dan database prospek'],
        'inventory'   => ['title' => 'Inventory & Logistics Dashboard', 'desc' => 'Pengawasan stok spare part, movement pergudangan, dan purchase order'],
        'cashier'     => ['title' => 'Cashier & POS Terminal', 'desc' => 'Pusat kasir, penagihan work order bengkel, dan transaksi ritel'],
        'mechanic'    => ['title' => 'Workshop & Mechanic Bay', 'desc' => 'Antrean unit servis, manajemen work order, dan diagnostik audio/mesin'],
    ];
    $headerInfo = $roleTitles[$role] ?? ['title' => 'Internal Dashboard', 'desc' => 'Selamat datang di portal internal AUDrive'];
?>

<?php $__env->startSection('title', $headerInfo['title']); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="relative overflow-hidden border border-gunmetal bg-carbon p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        
        <img src="<?php echo e(\App\Services\SettingService::getDashboardBackground()); ?>" 
             alt="Dashboard Banner" 
             class="absolute inset-0 w-full h-full object-cover object-center pointer-events-none opacity-20 select-none z-0" />
        <div class="absolute inset-0 bg-gradient-to-r from-carbon via-carbon/95 to-carbon/75 z-0 pointer-events-none"></div>

        <div class="relative z-10">
            <div class="flex items-center gap-3">
                <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">
                    <?php echo e($headerInfo['title']); ?>

                </h1>
            </div>
            <p class="text-xs text-silver-dim mt-1">
                Halo, <span class="text-giallo font-semibold"><?php echo e($user->name); ?></span> — <?php echo e($headerInfo['desc']); ?>

            </p>
        </div>

        
        <div class="relative z-10 flex flex-wrap items-center gap-2">
            <?php if(in_array($role, ['super-admin', 'manager', 'sales'], true)): ?>
                <a href="<?php echo e(route('admin.cars.create')); ?>" class="px-4 py-2 bg-giallo hover:bg-arancio text-nero font-heading font-bold text-xs uppercase tracking-wider transition-premium clip-angular-button shadow-md shadow-giallo/10">
                    + Tambah Mobil
                </a>
            <?php endif; ?>

            <?php if(in_array($role, ['super-admin', 'manager', 'inventory'], true)): ?>
                <a href="<?php echo e(route('admin.spare-parts.create')); ?>" class="px-4 py-2 bg-steel/60 hover:bg-steel text-silver-bright border border-gunmetal font-heading text-xs uppercase tracking-wider transition-premium">
                    + Input Spare Part
                </a>
            <?php endif; ?>

            <?php if(in_array($role, ['super-admin', 'manager', 'cashier'], true)): ?>
                <a href="<?php echo e(route('admin.pos.index')); ?>" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-heading font-bold text-xs uppercase tracking-wider transition-premium shadow-md shadow-blue-500/20">
                    💳 Buka Kasir POS
                </a>
            <?php endif; ?>

            <?php if(in_array($role, ['super-admin', 'manager', 'mechanic'], true)): ?>
                <a href="<?php echo e(route('admin.work-orders.index')); ?>" class="px-4 py-2 bg-verde hover:bg-emerald-600 text-nero font-heading font-bold text-xs uppercase tracking-wider transition-premium shadow-md shadow-verde/20">
                    🔧 Work Order Aktif
                </a>
            <?php endif; ?>
        </div>
    </div>

    
    
    
    <?php if($role === 'sales'): ?>
        <div>
            <h2 class="font-heading text-sm uppercase tracking-wider text-silver-bright mb-3">
                Performa Penjualan Showroom
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="panel p-5 border border-gunmetal">
                    <p class="font-heading text-xs uppercase tracking-wider text-silver-dim">Revenue Mobil Bulan Ini</p>
                    <p class="font-display text-2xl font-bold text-giallo mt-2">
                        Rp <?php echo e(number_format($stats['sales']['revenue_current_month'], 0, ',', '.')); ?>

                    </p>
                    <p class="font-mono text-xs <?php echo e($stats['sales']['revenue_growth'] >= 0 ? 'text-verde' : 'text-danger'); ?> mt-2">
                        <?php echo e($stats['sales']['revenue_growth'] >= 0 ? '↑' : '↓'); ?> <?php echo e(abs($stats['sales']['revenue_growth'])); ?>% vs bulan lalu
                    </p>
                </div>

                <div class="panel p-5 border border-gunmetal">
                    <p class="font-heading text-xs uppercase tracking-wider text-silver-dim">Mobil Terjual</p>
                    <p class="font-display text-2xl font-bold text-arancio mt-2">
                        <?php echo e($stats['sales']['cars_sold']); ?> <span class="text-xs text-silver-dim font-normal">Unit</span>
                    </p>
                    <p class="font-mono text-xs text-silver-dim mt-2">Bulan berjalan</p>
                </div>

                <div class="panel p-5 border border-gunmetal">
                    <p class="font-heading text-xs uppercase tracking-wider text-silver-dim">Unit Siap Jual</p>
                    <p class="font-display text-2xl font-bold text-verde mt-2">
                        <?php echo e($stats['sales']['available_units']); ?> <span class="text-xs text-silver-dim font-normal">Ready Stock</span>
                    </p>
                    <a href="<?php echo e(route('admin.cars.index')); ?>" class="inline-block font-mono text-xs text-giallo hover:underline mt-2">Lihat Inventory Mobil &rarr;</a>
                </div>

                <div class="panel p-5 border border-gunmetal">
                    <p class="font-heading text-xs uppercase tracking-wider text-silver-dim">Total Prospek / Customer</p>
                    <p class="font-display text-2xl font-bold text-silver-bright mt-2">
                        <?php echo e($stats['customers']['total_customers']); ?> <span class="text-xs text-silver-dim font-normal">Orang</span>
                    </p>
                    <a href="<?php echo e(route('admin.customers.index')); ?>" class="inline-block font-mono text-xs text-giallo hover:underline mt-2">Buka Data Pelanggan &rarr;</a>
                </div>
            </div>
        </div>

        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="<?php echo e(route('admin.cars.index')); ?>" class="panel p-5 border border-gunmetal hover:border-giallo transition-premium group flex items-center justify-between">
                <div>
                    <h3 class="font-heading text-sm text-silver-bright font-semibold group-hover:text-giallo">Showroom Unit Mobil</h3>
                    <p class="text-xs text-silver-dim mt-1">Kelola spesifikasi, harga listing, dan upload galeri foto</p>
                </div>
                <span class="text-2xl">🏎</span>
            </a>
            <a href="<?php echo e(route('admin.offers.index')); ?>" class="panel p-5 border border-gunmetal hover:border-giallo transition-premium group flex items-center justify-between">
                <div>
                    <h3 class="font-heading text-sm text-silver-bright font-semibold group-hover:text-giallo">Penawaran Masuk</h3>
                    <p class="text-xs text-silver-dim mt-1">Review penawaran harga dari calon pembeli</p>
                </div>
                <span class="text-2xl">💬</span>
            </a>
            <a href="<?php echo e(route('admin.service-bookings.index')); ?>" class="panel p-5 border border-gunmetal hover:border-giallo transition-premium group flex items-center justify-between">
                <div>
                    <h3 class="font-heading text-sm text-silver-bright font-semibold group-hover:text-giallo">Jadwal Booking Servis</h3>
                    <p class="text-xs text-silver-dim mt-1">Bantu klien menjadwalkan servis berkala</p>
                </div>
                <span class="text-2xl">📅</span>
            </a>
        </div>
    <?php endif; ?>

    
    
    
    <?php if($role === 'inventory'): ?>
        <div>
            <h2 class="font-heading text-sm uppercase tracking-wider text-silver-bright mb-3">
                Status Inventori & Pergudangan
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="panel p-5 border border-gunmetal">
                    <p class="font-heading text-xs uppercase tracking-wider text-silver-dim">Total Spare Part Aktif</p>
                    <p class="font-display text-3xl font-bold text-silver-bright mt-2">
                        <?php echo e($stats['inventory']['total_parts_count']); ?> <span class="text-sm font-normal text-silver-dim">SKU</span>
                    </p>
                    <a href="<?php echo e(route('admin.spare-parts.index')); ?>" class="inline-block font-mono text-xs text-giallo hover:underline mt-2">Katalog Spare Part &rarr;</a>
                </div>

                <div class="panel p-5 border border-gunmetal">
                    <p class="font-heading text-xs uppercase tracking-wider text-silver-dim">Total Nilai Stok Gudang</p>
                    <p class="font-display text-3xl font-bold text-giallo mt-2">
                        Rp <?php echo e(number_format($stats['inventory']['total_parts_value'], 0, ',', '.')); ?>

                    </p>
                    <p class="font-mono text-xs text-silver-dim mt-2">Valuasi stok berjalan</p>
                </div>

                <div class="panel p-5 border border-gunmetal <?php echo e($stats['inventory']['low_stock_parts'] > 0 ? 'bg-danger/10 border-danger/40' : ''); ?>">
                    <p class="font-heading text-xs uppercase tracking-wider <?php echo e($stats['inventory']['low_stock_parts'] > 0 ? 'text-danger font-bold' : 'text-silver-dim'); ?>">
                        Stok Kritis / Rendah
                    </p>
                    <p class="font-display text-3xl font-bold <?php echo e($stats['inventory']['low_stock_parts'] > 0 ? 'text-danger' : 'text-verde'); ?> mt-2">
                        <?php echo e($stats['inventory']['low_stock_parts']); ?> <span class="text-sm font-normal text-silver-dim">Item Butuh Restock</span>
                    </p>
                    <a href="<?php echo e(route('admin.inventory.index')); ?>" class="inline-block font-mono text-xs text-danger hover:underline mt-2">Cek Stok Kritis &rarr;</a>
                </div>
            </div>
        </div>

        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="<?php echo e(route('admin.spare-parts.create')); ?>" class="panel p-5 border border-gunmetal hover:border-giallo transition-premium group flex items-center justify-between">
                <div>
                    <h3 class="font-heading text-sm text-silver-bright font-semibold group-hover:text-giallo">+ Tambah Spare Part</h3>
                    <p class="text-xs text-silver-dim mt-1">Daftarkan SKU part supercar baru</p>
                </div>
                <span class="text-2xl">⚙️</span>
            </a>
            <a href="<?php echo e(route('admin.inventory.index')); ?>" class="panel p-5 border border-gunmetal hover:border-giallo transition-premium group flex items-center justify-between">
                <div>
                    <h3 class="font-heading text-sm text-silver-bright font-semibold group-hover:text-giallo">Stock Movement & Adjustment</h3>
                    <p class="text-xs text-silver-dim mt-1">Audit mutasi barang masuk / keluar</p>
                </div>
                <span class="text-2xl">📦</span>
            </a>
            <a href="<?php echo e(route('admin.purchase-orders.index')); ?>" class="panel p-5 border border-gunmetal hover:border-giallo transition-premium group flex items-center justify-between">
                <div>
                    <h3 class="font-heading text-sm text-silver-bright font-semibold group-hover:text-giallo">Purchase Order (PO)</h3>
                    <p class="text-xs text-silver-dim mt-1">Kelola pesanan barang ke supplier</p>
                </div>
                <span class="text-2xl">🛒</span>
            </a>
        </div>
    <?php endif; ?>

    
    
    
    <?php if($role === 'cashier'): ?>
        <div>
            <h2 class="font-heading text-sm uppercase tracking-wider text-silver-bright mb-3">
                Ringkasan Transaksi Kasir
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="panel p-5 border border-gunmetal">
                    <p class="font-heading text-xs uppercase tracking-wider text-silver-dim">Omzet Bengkel Bulan Ini</p>
                    <p class="font-display text-3xl font-bold text-giallo mt-2">
                        Rp <?php echo e(number_format($stats['workshop']['revenue_current_month'], 0, ',', '.')); ?>

                    </p>
                </div>

                <div class="panel p-5 border border-gunmetal">
                    <p class="font-heading text-xs uppercase tracking-wider text-silver-dim">Work Order Aktif</p>
                    <p class="font-display text-3xl font-bold text-arancio mt-2">
                        <?php echo e($stats['workshop']['active_work_orders']); ?> <span class="text-sm font-normal text-silver-dim">WO</span>
                    </p>
                    <a href="<?php echo e(route('admin.work-orders.index')); ?>" class="inline-block font-mono text-xs text-giallo hover:underline mt-2">Buka Work Orders &rarr;</a>
                </div>

                <div class="panel p-5 border border-gunmetal">
                    <p class="font-heading text-xs uppercase tracking-wider text-silver-dim">Booking Servis Hari Ini</p>
                    <p class="font-display text-3xl font-bold text-verde mt-2">
                        <?php echo e($stats['workshop']['bookings_today']); ?> <span class="text-sm font-normal text-silver-dim">Jadwal</span>
                    </p>
                    <a href="<?php echo e(route('admin.service-bookings.index')); ?>" class="inline-block font-mono text-xs text-verde hover:underline mt-2">Cek Jadwal &rarr;</a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="<?php echo e(route('admin.pos.index')); ?>" class="panel p-6 border-2 border-giallo bg-carbon hover:bg-steel/30 transition-premium group flex items-center justify-between">
                <div>
                    <h3 class="font-display text-lg text-giallo font-bold tracking-wide">BUKA POINT OF SALES (POS)</h3>
                    <p class="text-xs text-silver-dim mt-1">Layani transaksi ritel spare part & pembayaran pelanggan langsung</p>
                </div>
                <span class="text-3xl">💳</span>
            </a>
            <a href="<?php echo e(route('admin.work-orders.index')); ?>" class="panel p-6 border border-gunmetal bg-carbon hover:border-giallo transition-premium group flex items-center justify-between">
                <div>
                    <h3 class="font-display text-lg text-silver-bright font-bold tracking-wide">PENAGIHAN WORK ORDER</h3>
                    <p class="text-xs text-silver-dim mt-1">Selesaikan pembayaran untuk unit servis yang telah selesai dikerjakan</p>
                </div>
                <span class="text-3xl">🔧</span>
            </a>
        </div>
    <?php endif; ?>

    
    
    
    <?php if($role === 'mechanic'): ?>
        <div>
            <h2 class="font-heading text-sm uppercase tracking-wider text-silver-bright mb-3">
                Status Antrean & Diagnostik Workshop
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="panel p-5 border border-gunmetal">
                    <p class="font-heading text-xs uppercase tracking-wider text-silver-dim">Work Order Aktif</p>
                    <p class="font-display text-3xl font-bold text-arancio mt-2">
                        <?php echo e($stats['workshop']['active_work_orders']); ?> <span class="text-sm font-normal text-silver-dim">Unit Pengerjaan</span>
                    </p>
                    <a href="<?php echo e(route('admin.work-orders.index')); ?>" class="inline-block font-mono text-xs text-giallo hover:underline mt-2">Buka Antrean Bengkel &rarr;</a>
                </div>

                <div class="panel p-5 border border-gunmetal">
                    <p class="font-heading text-xs uppercase tracking-wider text-silver-dim">Booking Servis Hari Ini</p>
                    <p class="font-display text-3xl font-bold text-verde mt-2">
                        <?php echo e($stats['workshop']['bookings_today']); ?> <span class="text-sm font-normal text-silver-dim">Mobil Terjadwal</span>
                    </p>
                    <a href="<?php echo e(route('admin.service-bookings.index')); ?>" class="inline-block font-mono text-xs text-verde hover:underline mt-2">Lihat Jadwal Masuk &rarr;</a>
                </div>

                <div class="panel p-5 border border-gunmetal">
                    <p class="font-heading text-xs uppercase tracking-wider text-silver-dim">Diagnostik AI Sound</p>
                    <p class="font-display text-3xl font-bold text-giallo mt-2">
                        <?php echo e($stats['ai_scans']['completed_scans'] ?? 0); ?> <span class="text-sm font-normal text-silver-dim">Analisis Suara</span>
                    </p>
                    <a href="<?php echo e(route('admin.ai-scans.index')); ?>" class="inline-block font-mono text-xs text-giallo hover:underline mt-2">Buka AI Sound Scan &rarr;</a>
                </div>

                <div class="panel p-5 border border-gunmetal">
                    <p class="font-heading text-xs uppercase tracking-wider text-silver-dim">Ketersediaan Spare Part</p>
                    <p class="font-display text-3xl font-bold text-silver-bright mt-2">
                        <?php echo e($stats['inventory']['total_parts_count'] ?? 0); ?> <span class="text-sm font-normal text-silver-dim">SKU Part</span>
                    </p>
                    <a href="<?php echo e(route('admin.spare-parts.index')); ?>" class="inline-block font-mono text-xs text-silver-dim hover:underline mt-2">Katalog Suku Cadang &rarr;</a>
                </div>
            </div>
        </div>

        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="<?php echo e(route('admin.work-orders.index')); ?>" class="panel p-6 border-2 border-giallo bg-carbon hover:bg-steel/30 transition-premium group flex items-center justify-between">
                <div>
                    <h3 class="font-display text-base text-giallo font-bold tracking-wide">WORK ORDERS & ANTREAN BENGKEL</h3>
                    <p class="text-xs text-silver-dim mt-1">Periksa checklist inspeksi, pasang spare part, dan update progres pekerjaan</p>
                </div>
                <span class="text-3xl">🔧</span>
            </a>
            <a href="<?php echo e(route('admin.ai-scans.index')); ?>" class="panel p-6 border border-gunmetal bg-carbon hover:border-giallo transition-premium group flex items-center justify-between">
                <div>
                    <h3 class="font-display text-base text-silver-bright font-bold tracking-wide">AI SOUND DIAGNOSTICS</h3>
                    <p class="text-xs text-silver-dim mt-1">Deteksi kerusakan mesin, transmisi, & knalpot via analisis frekuensi audio</p>
                </div>
                <span class="text-3xl">🤖</span>
            </a>
            <a href="<?php echo e(route('admin.spare-parts.index')); ?>" class="panel p-6 border border-gunmetal bg-carbon hover:border-giallo transition-premium group flex items-center justify-between">
                <div>
                    <h3 class="font-display text-base text-silver-bright font-bold tracking-wide">KATALOG SUKU CADANG</h3>
                    <p class="text-xs text-silver-dim mt-1">Cek ketersediaan dan lokasi bin rak part sebelum penggantian unit</p>
                </div>
                <span class="text-3xl">📦</span>
            </a>
        </div>
    <?php endif; ?>

    
    
    
    <?php if(in_array($role, ['super-admin', 'manager'], true)): ?>
        
        <div>
            <h2 class="font-heading text-sm uppercase tracking-wider text-silver-bright mb-3">
                Performa Penjualan Showroom
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="panel p-5 border border-gunmetal">
                    <p class="font-heading text-xs uppercase tracking-wider text-silver-dim">Revenue Mobil Bulan Ini</p>
                    <p class="font-display text-3xl font-bold text-giallo mt-2">
                        Rp <?php echo e(number_format($stats['sales']['revenue_current_month'], 0, ',', '.')); ?>

                    </p>
                    <p class="font-mono text-xs <?php echo e($stats['sales']['revenue_growth'] >= 0 ? 'text-verde' : 'text-danger'); ?> mt-2">
                        <?php echo e($stats['sales']['revenue_growth'] >= 0 ? '↑' : '↓'); ?> <?php echo e(abs($stats['sales']['revenue_growth'])); ?>% dari bulan lalu
                    </p>
                </div>

                <div class="panel p-5 border border-gunmetal">
                    <p class="font-heading text-xs uppercase tracking-wider text-silver-dim">Mobil Terjual</p>
                    <p class="font-display text-3xl font-bold text-arancio mt-2">
                        <?php echo e($stats['sales']['cars_sold']); ?>

                    </p>
                    <p class="font-mono text-xs text-silver-dim mt-2">unit bulan ini</p>
                </div>

                <div class="panel p-5 border border-gunmetal">
                    <p class="font-heading text-xs uppercase tracking-wider text-silver-dim">Unit Siap Jual</p>
                    <p class="font-display text-3xl font-bold text-verde mt-2">
                        <?php echo e($stats['sales']['available_units']); ?>

                    </p>
                    <p class="font-mono text-xs text-silver-dim mt-2">unit siap jual</p>
                </div>
            </div>
        </div>

        
        <div>
            <h2 class="font-heading text-sm uppercase tracking-wider text-silver-bright mb-3">
                Performa Bengkel & Workshop
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="panel p-5 border border-gunmetal">
                    <p class="font-heading text-xs uppercase tracking-wider text-silver-dim">Omzet Bengkel</p>
                    <p class="font-display text-3xl font-bold text-giallo mt-2">
                        Rp <?php echo e(number_format($stats['workshop']['revenue_current_month'], 0, ',', '.')); ?>

                    </p>
                    <p class="font-mono text-xs text-silver-dim mt-2">bulan ini</p>
                </div>

                <div class="panel p-5 border border-gunmetal">
                    <p class="font-heading text-xs uppercase tracking-wider text-silver-dim">Work Order Aktif</p>
                    <p class="font-display text-3xl font-bold text-arancio mt-2">
                        <?php echo e($stats['workshop']['active_work_orders']); ?>

                    </p>
                    <p class="font-mono text-xs text-silver-dim mt-2">sedang dikerjakan</p>
                </div>

                <div class="panel p-5 border border-gunmetal">
                    <p class="font-heading text-xs uppercase tracking-wider text-silver-dim">Booking Hari Ini</p>
                    <p class="font-display text-3xl font-bold text-verde mt-2">
                        <?php echo e($stats['workshop']['bookings_today']); ?>

                    </p>
                    <p class="font-mono text-xs text-silver-dim mt-2">jadwal servis</p>
                </div>
            </div>
        </div>

        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div>
                <h2 class="font-heading text-sm uppercase tracking-wider text-silver-bright mb-3">
                    Inventori Spare Part
                </h2>
                <div class="panel p-5 border border-gunmetal space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="font-body text-sm text-silver-dim">Total Spare Part</span>
                        <span class="font-display text-xl font-bold text-silver-bright">
                            <?php echo e($stats['inventory']['total_parts_count']); ?>

                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="font-body text-sm text-silver-dim">Nilai Inventory</span>
                        <span class="font-display text-xl font-bold text-giallo">
                            Rp <?php echo e(number_format($stats['inventory']['total_parts_value'], 0, ',', '.')); ?>

                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="font-body text-sm text-silver-dim">Stok Rendah</span>
                        <span class="font-display text-xl font-bold <?php echo e($stats['inventory']['low_stock_parts'] > 0 ? 'text-danger' : 'text-verde'); ?>">
                            <?php echo e($stats['inventory']['low_stock_parts']); ?>

                        </span>
                    </div>
                </div>
            </div>

            
            <div>
                <h2 class="font-heading text-sm uppercase tracking-wider text-silver-bright mb-3">
                    AI Sound Diagnostic
                </h2>
                <div class="panel p-5 border border-gunmetal space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="font-body text-sm text-silver-dim">Total Scan Bulan Ini</span>
                        <span class="font-display text-xl font-bold text-silver-bright">
                            <?php echo e($stats['ai_scans']['total_scans']); ?>

                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="font-body text-sm text-silver-dim">Scan Berhasil</span>
                        <span class="font-display text-xl font-bold text-verde">
                            <?php echo e($stats['ai_scans']['completed_scans']); ?>

                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="font-body text-sm text-silver-dim">Success Rate</span>
                        <span class="font-display text-xl font-bold text-giallo">
                            <?php echo e($stats['ai_scans']['success_rate']); ?>%
                        </span>
                    </div>
                </div>
            </div>
        </div>

        
        <?php if($pendingApprovals->count() > 0): ?>
        <div>
            <h2 class="font-heading text-sm uppercase tracking-wider text-silver-bright mb-3">
                Approval Pending Butuh Review
            </h2>
            <div class="panel border border-gunmetal overflow-hidden">
                <div class="divide-y divide-gunmetal/30">
                    <?php $__currentLoopData = $pendingApprovals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $approval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="p-4 hover:bg-steel/20 transition-premium">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-heading text-sm text-silver-bright">
                                    <?php echo e(ucfirst(str_replace('_', ' ', $approval->type))); ?>

                                </p>
                                <p class="font-body text-xs text-silver-dim mt-1">
                                    Oleh: <?php echo e($approval->requester->name ?? 'Staf'); ?> • <?php echo e($approval->created_at->diffForHumans()); ?>

                                </p>
                            </div>
                            <a href="<?php echo e(route('admin.approvals.index')); ?>"
                               class="px-4 py-2 bg-giallo text-nero font-heading text-xs uppercase tracking-wider hover:bg-arancio transition-premium">
                                Review
                            </a>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/admin/dashboard/index.blade.php ENDPATH**/ ?>