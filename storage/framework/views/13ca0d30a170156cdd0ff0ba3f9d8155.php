<?php $__env->startSection('title', 'Laporan Resmi Suku Cadang & Stok'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="no-print flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="<?php echo e(route('admin.reports.index')); ?>" class="text-giallo font-mono text-sm tracking-widest uppercase hover:underline">Laporan</a>
                <span class="text-silver-dim text-xs">/</span>
                <span class="text-silver-dim text-xs font-mono">Suku Cadang & Stok</span>
            </div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide mt-1">
                LAPORAN RESMI VALUASI SUKU CADANG & STOK
            </h1>
            <p class="text-sm text-silver-dim mt-1">
                Rekapitulasi nilai aset persediaan suku cadang, batas minimum restock, dan status ketersediaan di gudang.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-steel/60 hover:bg-steel text-silver-bright border border-gunmetal font-heading text-xs uppercase tracking-wider rounded transition-premium flex items-center gap-2 shadow-sm">
                <span>🖨️</span> Cetak Laporan Formal
            </button>
        </div>
    </div>

    
    <div class="no-print flex items-center gap-2 border-b border-gunmetal overflow-x-auto pb-2 print:hidden">
        <a href="<?php echo e(route('admin.reports.index')); ?>" class="px-4 py-2 border-b-2 border-transparent text-silver-dim hover:text-silver-bright font-heading text-xs uppercase tracking-wider transition-premium flex items-center gap-2">
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
            <a href="<?php echo e(route('admin.reports.inventory')); ?>" class="px-4 py-2 border-b-2 border-giallo text-giallo font-heading text-xs uppercase tracking-wider flex items-center gap-2 font-bold">
                <span>⚙️</span> Suku Cadang & Stok
            </a>
        <?php endif; ?>
        <?php if(in_array(auth()->user()->role ?? '', ['super-admin', 'owner', 'manager', 'cashier'], true)): ?>
            <a href="<?php echo e(route('admin.reports.finance')); ?>" class="px-4 py-2 border-b-2 border-transparent text-silver-dim hover:text-silver-bright font-heading text-xs uppercase tracking-wider transition-premium flex items-center gap-2">
                <span>💳</span> Finansial & Kasir
            </a>
        <?php endif; ?>
    </div>

    
    <div class="no-print panel border border-gunmetal bg-carbon p-4 print:hidden">
        <form method="GET" action="<?php echo e(route('admin.reports.inventory')); ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            
            <div>
                <label class="block text-[10px] font-heading uppercase tracking-wider text-silver-dim mb-1">Cari Nama / Part Number / SKU</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Ketik kata kunci..."
                       class="w-full bg-steel/20 border border-gunmetal rounded px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none font-mono">
            </div>

            
            <div>
                <label class="block text-[10px] font-heading uppercase tracking-wider text-silver-dim mb-1">Kategori Suku Cadang</label>
                <select name="category_id" class="w-full bg-steel/20 border border-gunmetal rounded px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                    <option value="all" <?php echo e(request('category_id') === 'all' ? 'selected' : ''); ?>>Semua Kategori</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cat->id); ?>" <?php echo e(request('category_id') == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            
            <div>
                <label class="block text-[10px] font-heading uppercase tracking-wider text-silver-dim mb-1">Status Ketersediaan</label>
                <select name="stock_status" class="w-full bg-steel/20 border border-gunmetal rounded px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                    <option value="all" <?php echo e(request('stock_status') === 'all' ? 'selected' : ''); ?>>Semua Status</option>
                    <option value="low_stock" <?php echo e(request('stock_status') === 'low_stock' ? 'selected' : ''); ?>>Stok Kritis / Rendah (Low)</option>
                    <option value="out_of_stock" <?php echo e(request('stock_status') === 'out_of_stock' ? 'selected' : ''); ?>>Habis (Out of Stock)</option>
                    <option value="in_stock" <?php echo e(request('stock_status') === 'in_stock' ? 'selected' : ''); ?>>Stok Aman (In Stock)</option>
                </select>
            </div>

            
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-giallo hover:bg-giallo/90 text-carbon font-heading text-xs uppercase font-bold rounded transition-premium">
                    Filter
                </button>
                <a href="<?php echo e(route('admin.reports.inventory')); ?>" class="px-3 py-2 bg-steel/40 hover:bg-steel border border-gunmetal text-silver-dim hover:text-silver-bright text-xs rounded transition-premium" title="Reset filter">
                    Reset
                </a>
            </div>
        </form>
    </div>

    
    <div class="hidden print:block border-b-2 border-black pb-4 mb-6">
        <table class="w-full text-black">
            <tr>
                <td style="width: 60%; vertical-align: top;">
                    <h2 class="text-2xl font-bold uppercase tracking-wider">PT AUDRIVE SUPERCAR INDONESIA</h2>
                    <p class="text-xs font-semibold uppercase text-gray-600">Divisi Logistik, Pergudangan & Suku Cadang</p>
                    <p class="text-[10px] text-gray-600">Graha AUDrive Warehouse Hub, SCBD, Jakarta Selatan 12190 &bull; Telp: +62 21 8088 9900</p>
                </td>
                <td style="width: 40%; text-align: right; vertical-align: top;">
                    <h3 class="text-lg font-bold uppercase">LAPORAN VALUASI & STOK PERSUSUN</h3>
                    <p class="text-xs font-mono">No. Dokumen: RPT-INV-<?php echo e(date('Ymd-His')); ?></p>
                    <p class="text-xs">Tanggal Cetak: <?php echo e(now()->format('d F Y, H:i')); ?> WIB</p>
                    <p class="text-[10px] text-gray-500">Petugas Gudang: <?php echo e(auth()->user()->name); ?></p>
                </td>
            </tr>
        </table>
    </div>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="panel border border-gunmetal bg-carbon p-5 relative overflow-hidden group hover:border-giallo/40 transition-premium">
            <span class="text-silver-dim text-xs font-heading uppercase tracking-wider">Total Nilai Aset Stok</span>
            <p class="font-mono text-xl sm:text-2xl font-bold text-giallo mt-2">
                Rp <?php echo e(number_format($totalPartsValue, 0, ',', '.')); ?>

            </p>
            <p class="text-[10px] text-silver-dim font-mono mt-1">Valuasi seluruh katalog aktif</p>
        </div>
        <div class="panel border border-gunmetal bg-carbon p-5 relative overflow-hidden group hover:border-blue-400/40 transition-premium">
            <span class="text-silver-dim text-xs font-heading uppercase tracking-wider">Valuasi Data Terfilter</span>
            <p class="font-mono text-xl sm:text-2xl font-bold text-blue-400 mt-2">
                Rp <?php echo e(number_format($totalFilteredValue, 0, ',', '.')); ?>

            </p>
            <p class="text-[10px] text-silver-dim font-mono mt-1">Sesuai kategori & status dipilih</p>
        </div>
        <div class="panel border border-gunmetal bg-carbon p-5 relative overflow-hidden group hover:border-verde/40 transition-premium">
            <span class="text-silver-dim text-xs font-heading uppercase tracking-wider">Total Jenis SKU</span>
            <p class="font-mono text-2xl font-bold text-verde mt-2">
                <?php echo e($totalItems); ?> <span class="text-xs font-normal text-silver-dim">SKU</span>
            </p>
            <p class="text-[10px] text-silver-dim font-mono mt-1">Fisik: <?php echo e(number_format($totalStockUnits)); ?> unit barang</p>
        </div>
        <div class="panel border border-gunmetal bg-carbon p-5 relative overflow-hidden group hover:border-arancio/40 transition-premium">
            <span class="text-silver-dim text-xs font-heading uppercase tracking-wider">Stok Kritis (Low Stock)</span>
            <p class="font-mono text-2xl font-bold text-arancio mt-2">
                <?php echo e($lowStockCount); ?> <span class="text-xs font-normal text-silver-dim">Item</span>
            </p>
            <p class="text-[10px] text-silver-dim font-mono mt-1">&le; threshold batas aman</p>
        </div>
        <div class="panel border border-gunmetal bg-carbon p-5 relative overflow-hidden group hover:border-red-500/40 transition-premium">
            <span class="text-silver-dim text-xs font-heading uppercase tracking-wider">Stok Habis (Kosong)</span>
            <p class="font-mono text-2xl font-bold text-red-400 mt-2">
                <?php echo e($outOfStockCount); ?> <span class="text-xs font-normal text-silver-dim">SKU</span>
            </p>
            <p class="text-[10px] text-silver-dim font-mono mt-1">Perlu purchase order (PO)</p>
        </div>
    </div>

    
    <div class="panel border border-gunmetal bg-carbon overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-steel/30 text-silver-dim font-heading uppercase text-[10px] border-b border-gunmetal">
                        <th class="py-3 px-4">Part No / SKU</th>
                        <th class="py-3 px-4">Nama Komponen Suku Cadang</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4 text-right">Harga Satuan</th>
                        <th class="py-3 px-4 text-center">Stok Fisik</th>
                        <th class="py-3 px-4 text-center">Threshold</th>
                        <th class="py-3 px-4 text-center">Status Stok</th>
                        <th class="py-3 px-4 text-right">Valuasi Total Aset</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/30">
                    <?php $__empty_1 = true; $__currentLoopData = $spareParts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $part): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $valItem = (int) ($part->stock * $part->price_idr);
                            $isLow = $part->stock <= $part->low_stock_threshold;
                            $isOut = $part->stock <= 0;
                        ?>
                        <tr class="hover:bg-steel/10 transition-premium">
                            <td class="py-3 px-4 font-mono font-bold text-silver-bright">
                                <?php echo e($part->part_number ?? ($part->sku ?? 'SKU-' . $part->id)); ?>

                            </td>
                            <td class="py-3 px-4">
                                <p class="font-bold text-silver-bright"><?php echo e($part->name); ?></p>
                                <p class="text-[10px] text-silver-dim"><?php echo e(Str::limit($part->description, 50)); ?></p>
                            </td>
                            <td class="py-3 px-4 text-silver-dim">
                                <?php echo e($part->category?->name ?? 'Umum'); ?>

                            </td>
                            <td class="py-3 px-4 font-mono text-silver-dim text-right">
                                Rp <?php echo e(number_format($part->price_idr, 0, ',', '.')); ?>

                            </td>
                            <td class="py-3 px-4 font-mono font-bold text-center <?php echo e($isOut ? 'text-red-400' : ($isLow ? 'text-arancio' : 'text-silver-bright')); ?>">
                                <?php echo e($part->stock); ?>

                            </td>
                            <td class="py-3 px-4 font-mono text-silver-dim text-center">
                                <?php echo e($part->low_stock_threshold); ?>

                            </td>
                            <td class="py-3 px-4 text-center">
                                <?php if($isOut): ?>
                                    <span class="inline-flex px-2 py-0.5 rounded text-[9px] font-mono uppercase font-bold bg-red-950/40 text-red-400 border border-red-800">
                                        Habis
                                    </span>
                                <?php elseif($isLow): ?>
                                    <span class="inline-flex px-2 py-0.5 rounded text-[9px] font-mono uppercase font-bold bg-amber-950/40 text-amber-400 border border-amber-800">
                                        Low Stock
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex px-2 py-0.5 rounded text-[9px] font-mono uppercase font-bold bg-verde/15 text-verde border border-verde/40">
                                        Aman
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 font-mono font-bold text-giallo text-right">
                                Rp <?php echo e(number_format($valItem, 0, ',', '.')); ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="py-12 text-center text-silver-dim">
                                <span class="text-3xl block mb-2 opacity-50">⚙️</span>
                                <p class="font-display font-bold text-silver-bright text-sm tracking-wide">Tidak Ditemukan Suku Cadang</p>
                                <p class="text-xs text-silver-dim mt-1">Sesuaikan kategori atau kata kunci pencarian Anda.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                
                <?php if(count($spareParts) > 0): ?>
                    <tfoot>
                        <tr class="bg-steel/40 border-t-2 border-gunmetal font-mono font-bold text-silver-bright text-xs">
                            <td colspan="4" class="py-3 px-4 text-right uppercase font-heading text-[11px] text-giallo">
                                REKAPITULASI VALUASI TERPILIH:
                            </td>
                            <td class="py-3 px-4 text-center text-silver-bright">
                                <?php echo e($spareParts->sum('stock')); ?> unit
                            </td>
                            <td colspan="2"></td>
                            <td class="py-3 px-4 text-right text-giallo text-sm">
                                Rp <?php echo e(number_format($spareParts->sum(fn($p) => $p->stock * $p->price_idr), 0, ',', '.')); ?>

                            </td>
                        </tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>

        <?php if(!$isPrint && method_exists($spareParts, 'hasPages') && $spareParts->hasPages()): ?>
            <div class="p-4 border-t border-gunmetal bg-steel/20 print:hidden">
                <?php echo e($spareParts->links()); ?>

            </div>
        <?php endif; ?>
    </div>

    
    <div class="hidden print:block pt-12">
        <table class="w-full text-center text-black text-xs">
            <tr>
                <td style="width: 33%;">
                    <p class="text-[10px] font-semibold text-gray-600 mb-14">Dibuat Oleh (Inventory Staff):</p>
                    <p class="font-bold underline"><?php echo e(auth()->user()->name); ?></p>
                    <p class="text-[9px] text-gray-500">Logistik & Manajemen Gudang</p>
                </td>
                <td style="width: 33%;">
                    <p class="text-[10px] font-semibold text-gray-600 mb-14">Diperiksa Oleh (Operations Manager):</p>
                    <p class="font-bold underline">Operations Manager</p>
                    <p class="text-[9px] text-gray-500">Kepala Operasional Persediaan</p>
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

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/admin/reports/inventory.blade.php ENDPATH**/ ?>