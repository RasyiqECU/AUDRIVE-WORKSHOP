<?php $__env->startSection('title', 'Laporan Resmi Finansial & Kasir'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="no-print flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="<?php echo e(route('admin.reports.index')); ?>" class="text-giallo font-mono text-sm tracking-widest uppercase hover:underline">Laporan</a>
                <span class="text-silver-dim text-xs">/</span>
                <span class="text-silver-dim text-xs font-mono">Finansial & Kasir</span>
            </div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide mt-1">
                LAPORAN RESMI FINANSIAL & ARUS KAS
            </h1>
            <p class="text-sm text-silver-dim mt-1">
                Rekapitulasi penerimaan kasir, rincian per metode pembayaran, potongan promo, pajak PPN, dan arus kas masuk per divisi.
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
            <a href="<?php echo e(route('admin.reports.inventory')); ?>" class="px-4 py-2 border-b-2 border-transparent text-silver-dim hover:text-silver-bright font-heading text-xs uppercase tracking-wider transition-premium flex items-center gap-2">
                <span>⚙️</span> Suku Cadang & Stok
            </a>
        <?php endif; ?>
        <a href="<?php echo e(route('admin.reports.finance')); ?>" class="px-4 py-2 border-b-2 border-giallo text-giallo font-heading text-xs uppercase tracking-wider flex items-center gap-2 font-bold">
            <span>💳</span> Finansial & Kasir
        </a>
    </div>

    
    <div class="no-print panel border border-gunmetal bg-carbon p-4 print:hidden">
        <form method="GET" action="<?php echo e(route('admin.reports.finance')); ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            
            <div class="sm:col-span-2 lg:col-span-6 flex items-center gap-1.5 flex-wrap pb-2 border-b border-gunmetal/50">
                <span class="text-xs font-heading uppercase text-silver-dim mr-1">Preset:</span>
                <a href="<?php echo e(route('admin.reports.finance', ['preset' => 'today'])); ?>" class="px-2.5 py-1 rounded text-xs font-mono uppercase <?php echo e($preset === 'today' ? 'bg-giallo text-carbon font-bold' : 'bg-steel/30 text-silver-dim hover:text-silver-bright'); ?>">Hari Ini</a>
                <a href="<?php echo e(route('admin.reports.finance', ['preset' => 'this_week'])); ?>" class="px-2.5 py-1 rounded text-xs font-mono uppercase <?php echo e($preset === 'this_week' ? 'bg-giallo text-carbon font-bold' : 'bg-steel/30 text-silver-dim hover:text-silver-bright'); ?>">Minggu Ini</a>
                <a href="<?php echo e(route('admin.reports.finance', ['preset' => 'this_month'])); ?>" class="px-2.5 py-1 rounded text-xs font-mono uppercase <?php echo e($preset === 'this_month' ? 'bg-giallo text-carbon font-bold' : 'bg-steel/30 text-silver-dim hover:text-silver-bright'); ?>">Bulan Ini</a>
                <a href="<?php echo e(route('admin.reports.finance', ['preset' => 'last_month'])); ?>" class="px-2.5 py-1 rounded text-xs font-mono uppercase <?php echo e($preset === 'last_month' ? 'bg-giallo text-carbon font-bold' : 'bg-steel/30 text-silver-dim hover:text-silver-bright'); ?>">Bulan Lalu</a>
                <a href="<?php echo e(route('admin.reports.finance', ['preset' => 'this_year'])); ?>" class="px-2.5 py-1 rounded text-xs font-mono uppercase <?php echo e($preset === 'this_year' ? 'bg-giallo text-carbon font-bold' : 'bg-steel/30 text-silver-dim hover:text-silver-bright'); ?>">Tahun Ini</a>
                <a href="<?php echo e(route('admin.reports.finance', ['preset' => 'all'])); ?>" class="px-2.5 py-1 rounded text-xs font-mono uppercase <?php echo e($preset === 'all' ? 'bg-giallo text-carbon font-bold' : 'bg-steel/30 text-silver-dim hover:text-silver-bright'); ?>">Semua</a>
            </div>

            
            <div>
                <label class="block text-[10px] font-heading uppercase tracking-wider text-silver-dim mb-1">Cari No. Order / Customer</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Ketik kata kunci..."
                       class="w-full bg-steel/20 border border-gunmetal rounded px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none font-mono">
            </div>

            
            <div>
                <label class="block text-[10px] font-heading uppercase tracking-wider text-silver-dim mb-1">Divisi Transaksi</label>
                <select name="type" class="w-full bg-steel/20 border border-gunmetal rounded px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                    <option value="all" <?php echo e(request('type') === 'all' ? 'selected' : ''); ?>>Semua Divisi</option>
                    <option value="car_sales" <?php echo e(request('type') === 'car_sales' ? 'selected' : ''); ?>>Penjualan Mobil</option>
                    <option value="service" <?php echo e(request('type') === 'service' ? 'selected' : ''); ?>>Bengkel & Servis</option>
                    <option value="spare_part" <?php echo e(request('type') === 'spare_part' ? 'selected' : ''); ?>>Suku Cadang</option>
                    <option value="pos" <?php echo e(request('type') === 'pos' ? 'selected' : ''); ?>>POS Kasir</option>
                </select>
            </div>

            
            <div>
                <label class="block text-[10px] font-heading uppercase tracking-wider text-silver-dim mb-1">Metode Bayar</label>
                <select name="method" class="w-full bg-steel/20 border border-gunmetal rounded px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                    <option value="all" <?php echo e(request('method') === 'all' ? 'selected' : ''); ?>>Semua Metode</option>
                    <option value="cash" <?php echo e(request('method') === 'cash' ? 'selected' : ''); ?>>Tunai (Cash)</option>
                    <option value="transfer" <?php echo e(request('method') === 'transfer' ? 'selected' : ''); ?>>Transfer Bank</option>
                    <option value="card" <?php echo e(request('method') === 'card' ? 'selected' : ''); ?>>Kartu Debit / Kredit</option>
                    <option value="ewallet" <?php echo e(request('method') === 'ewallet' ? 'selected' : ''); ?>>QRIS / E-Wallet</option>
                </select>
            </div>

            
            <div>
                <label class="block text-[10px] font-heading uppercase tracking-wider text-silver-dim mb-1">Mulai Tanggal</label>
                <input type="date" name="start_date" value="<?php echo e($startDate?->format('Y-m-d')); ?>"
                       class="w-full bg-steel/20 border border-gunmetal rounded px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none font-mono">
            </div>

            
            <div>
                <label class="block text-[10px] font-heading uppercase tracking-wider text-silver-dim mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="<?php echo e($endDate?->format('Y-m-d')); ?>"
                       class="w-full bg-steel/20 border border-gunmetal rounded px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none font-mono">
            </div>

            
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-giallo hover:bg-giallo/90 text-carbon font-heading text-xs uppercase font-bold rounded transition-premium">
                    Filter
                </button>
                <a href="<?php echo e(route('admin.reports.finance')); ?>" class="px-3 py-2 bg-steel/40 hover:bg-steel border border-gunmetal text-silver-dim hover:text-silver-bright text-xs rounded transition-premium" title="Reset filter">
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
                    <p class="text-xs font-semibold uppercase text-gray-600">Divisi Keuangan, Kasir & Pajak Korporasi</p>
                    <p class="text-[10px] text-gray-600">Graha AUDrive Kav. 88, SCBD, Jakarta Selatan 12190 &bull; Telp: +62 21 8088 9900</p>
                </td>
                <td style="width: 40%; text-align: right; vertical-align: top;">
                    <h3 class="text-lg font-bold uppercase">LAPORAN ARUS KAS & FINANSIAL</h3>
                    <p class="text-xs font-mono">No. Dokumen: RPT-FIN-<?php echo e(date('Ymd-His')); ?></p>
                    <p class="text-xs">Periode: <?php echo e($startDate ? $startDate->format('d/m/Y') : 'Awal'); ?> s/d <?php echo e($endDate ? $endDate->format('d/m/Y') : 'Sekarang'); ?></p>
                    <p class="text-[10px] text-gray-500">Dicetak: <?php echo e(now()->format('d/m/Y H:i')); ?> WIB oleh <?php echo e(auth()->user()->name); ?></p>
                </td>
            </tr>
        </table>
    </div>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="panel border border-gunmetal bg-carbon p-5 relative overflow-hidden group hover:border-giallo/40 transition-premium">
            <span class="text-silver-dim text-xs font-heading uppercase tracking-wider">Penerimaan Kas Bersih</span>
            <p class="font-mono text-xl sm:text-2xl font-bold text-giallo mt-2">
                Rp <?php echo e(number_format($totalIncome, 0, ',', '.')); ?>

            </p>
            <p class="text-[10px] text-silver-dim font-mono mt-1">Status Lunas (Paid)</p>
        </div>
        <div class="panel border border-gunmetal bg-carbon p-5 relative overflow-hidden group hover:border-blue-400/40 transition-premium">
            <span class="text-silver-dim text-xs font-heading uppercase tracking-wider">Subtotal Tagihan Kotor</span>
            <p class="font-mono text-xl sm:text-2xl font-bold text-blue-400 mt-2">
                Rp <?php echo e(number_format($totalSubtotal, 0, ',', '.')); ?>

            </p>
            <p class="text-[10px] text-silver-dim font-mono mt-1">Sebelum diskon & potongan</p>
        </div>
        <div class="panel border border-gunmetal bg-carbon p-5 relative overflow-hidden group hover:border-emerald-500/40 transition-premium">
            <span class="text-silver-dim text-xs font-heading uppercase tracking-wider">Total Diskon Diberikan</span>
            <p class="font-mono text-xl sm:text-2xl font-bold text-emerald-400 mt-2">
                Rp <?php echo e(number_format($totalDiscount, 0, ',', '.')); ?>

            </p>
            <p class="text-[10px] text-silver-dim font-mono mt-1">Potongan promo voucher</p>
        </div>
        <div class="panel border border-gunmetal bg-carbon p-5 relative overflow-hidden group hover:border-arancio/40 transition-premium">
            <span class="text-silver-dim text-xs font-heading uppercase tracking-wider">Total Pajak PPN (11%)</span>
            <p class="font-mono text-xl sm:text-2xl font-bold text-arancio mt-2">
                Rp <?php echo e(number_format($totalTax, 0, ',', '.')); ?>

            </p>
            <p class="text-[10px] text-silver-dim font-mono mt-1">Pajak pertambahan nilai</p>
        </div>
        <div class="panel border border-gunmetal bg-carbon p-5 relative overflow-hidden group hover:border-silver/40 transition-premium">
            <span class="text-silver-dim text-xs font-heading uppercase tracking-wider">Rata-rata Transaksi (AOV)</span>
            <p class="font-mono text-xl font-bold text-silver-bright mt-2">
                Rp <?php echo e(number_format($avgTransaction, 0, ',', '.')); ?>

            </p>
            <p class="text-[10px] text-silver-dim font-mono mt-1">Average order value</p>
        </div>
    </div>

    
    <div class="panel border border-gunmetal bg-carbon overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-steel/30 text-silver-dim font-heading uppercase text-[10px] border-b border-gunmetal">
                        <th class="py-3 px-4">No. Order & Invoice</th>
                        <th class="py-3 px-4">Customer</th>
                        <th class="py-3 px-4">Divisi Bisnis</th>
                        <th class="py-3 px-4">Tanggal Pembayaran</th>
                        <th class="py-3 px-4">Metode Bayar</th>
                        <th class="py-3 px-4 text-right">Subtotal</th>
                        <th class="py-3 px-4 text-right">Diskon</th>
                        <th class="py-3 px-4 text-right">Pajak</th>
                        <th class="py-3 px-4 text-right">Total Akhir</th>
                        <th class="py-3 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/30">
                    <?php $__empty_1 = true; $__currentLoopData = $allTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $isPaid = in_array($trx->status->value ?? (string)$trx->status, ['paid', 'completed'], true);
                            $pm = $trx->payments?->first();
                        ?>
                        <tr class="hover:bg-steel/10 transition-premium">
                            <td class="py-3.5 px-4 font-mono font-bold text-silver-bright">
                                <?php echo e($trx->code); ?>

                                <?php if($trx->invoice): ?>
                                    <p class="text-[10px] text-giallo font-normal"><?php echo e($trx->invoice->number); ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="font-bold text-silver-bright"><?php echo e($trx->customer?->user?->name ?? 'Walk-in Customer'); ?></p>
                                <p class="text-[10px] font-mono text-silver-dim"><?php echo e($trx->customer?->user?->phone ?? '-'); ?></p>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-heading font-bold uppercase tracking-wider
                                    <?php if(($trx->type->value ?? $trx->type) === 'car_sales'): ?> bg-giallo/15 text-giallo border border-giallo/30
                                    <?php elseif(($trx->type->value ?? $trx->type) === 'service'): ?> bg-arancio/15 text-arancio border border-arancio/30
                                    <?php else: ?> bg-blue-500/15 text-blue-400 border border-blue-500/30 <?php endif; ?>">
                                    <?php echo e(str_replace('_', ' ', $trx->type->value ?? ($trx->type ?? 'POS'))); ?>

                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-silver-dim">
                                <?php echo e($trx->created_at->format('d/m/Y H:i')); ?>

                            </td>
                            <td class="py-3.5 px-4 font-mono text-silver-bright uppercase font-bold text-[11px]">
                                <?php echo e($pm?->method ?? ($trx->payments?->first()?->method ?? '-')); ?>

                            </td>
                            <td class="py-3.5 px-4 font-mono text-silver-dim text-right">
                                Rp <?php echo e(number_format($trx->subtotal_idr, 0, ',', '.')); ?>

                            </td>
                            <td class="py-3.5 px-4 font-mono text-emerald-400 text-right">
                                <?php echo e($trx->discount_idr > 0 ? '- Rp ' . number_format($trx->discount_idr, 0, ',', '.') : '-'); ?>

                            </td>
                            <td class="py-3.5 px-4 font-mono text-silver-dim text-right">
                                <?php echo e($trx->tax_idr > 0 ? 'Rp ' . number_format($trx->tax_idr, 0, ',', '.') : '-'); ?>

                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-giallo text-right">
                                Rp <?php echo e(number_format($trx->total_idr, 0, ',', '.')); ?>

                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <?php if($isPaid): ?>
                                    <span class="inline-flex px-2 py-0.5 rounded text-[9px] font-mono uppercase font-bold bg-verde/15 text-verde border border-verde/40">
                                        ✓ Lunas
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex px-2 py-0.5 rounded text-[9px] font-mono uppercase font-bold bg-amber-500/15 text-amber-400 border border-amber-500/40">
                                        <?php echo e($trx->status->value ?? $trx->status); ?>

                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="10" class="py-12 text-center text-silver-dim">
                                <span class="text-3xl block mb-2 opacity-50">💳</span>
                                <p class="font-display font-bold text-silver-bright text-sm tracking-wide">Tidak Ditemukan Riwayat Kasir</p>
                                <p class="text-xs text-silver-dim mt-1">Tidak ada transaksi keuangan pada filter yang dipilih.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                
                <?php if(count($allTransactions) > 0): ?>
                    <tfoot>
                        <tr class="bg-steel/40 border-t-2 border-gunmetal font-mono font-bold text-silver-bright text-xs">
                            <td colspan="5" class="py-3 px-4 text-right uppercase font-heading text-[11px] text-giallo">
                                REKAPITULASI KAS TERFILTER:
                            </td>
                            <td class="py-3 px-4 text-right">
                                Rp <?php echo e(number_format($allTransactions->sum('subtotal_idr'), 0, ',', '.')); ?>

                            </td>
                            <td class="py-3 px-4 text-right text-emerald-400">
                                - Rp <?php echo e(number_format($allTransactions->sum('discount_idr'), 0, ',', '.')); ?>

                            </td>
                            <td class="py-3 px-4 text-right">
                                Rp <?php echo e(number_format($allTransactions->sum('tax_idr'), 0, ',', '.')); ?>

                            </td>
                            <td class="py-3 px-4 text-right text-giallo text-sm">
                                Rp <?php echo e(number_format($allTransactions->sum('total_idr'), 0, ',', '.')); ?>

                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>

        <?php if(!$isPrint && method_exists($allTransactions, 'hasPages') && $allTransactions->hasPages()): ?>
            <div class="p-4 border-t border-gunmetal bg-steel/20 print:hidden">
                <?php echo e($allTransactions->links()); ?>

            </div>
        <?php endif; ?>
    </div>

    
    <div class="hidden print:block pt-12">
        <table class="w-full text-center text-black text-xs">
            <tr>
                <td style="width: 33%;">
                    <p class="text-[10px] font-semibold text-gray-600 mb-14">Dibuat Oleh (Kasir / Petugas POS):</p>
                    <p class="font-bold underline"><?php echo e(auth()->user()->name); ?></p>
                    <p class="text-[9px] text-gray-500">Kasir Keuangan Harian</p>
                </td>
                <td style="width: 33%;">
                    <p class="text-[10px] font-semibold text-gray-600 mb-14">Diperiksa Oleh (Finance Supervisor):</p>
                    <p class="font-bold underline">Chief Finance Officer</p>
                    <p class="text-[9px] text-gray-500">Manajer Keuangan & Pajak</p>
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

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/admin/reports/finance.blade.php ENDPATH**/ ?>