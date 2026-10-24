<?php $__env->startSection('title', 'Manajemen Tiket Layanan & Komplain'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <span class="text-giallo font-heading uppercase tracking-wider">Tiket Layanan</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Customer Care & Support</p>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                TIKET LAYANAN & KELUHAN CUSTOMER
            </h1>
            <p class="text-xs text-silver-dim mt-1">
                Pantau tiket konsultasi, bantuan teknis bengkel, klaim garansi, dan komplain pelanggan secara real-time.
            </p>
        </div>
    </div>

    
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="panel border <?php echo e($stats['open'] > 0 ? 'border-amber-500/60 bg-carbon' : 'border-gunmetal/60 bg-carbon'); ?> p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Tiket Terbuka</p>
                    <p class="font-display text-2xl font-bold <?php echo e($stats['open'] > 0 ? 'text-amber-400' : 'text-silver-bright'); ?> mt-1">
                        <?php echo e($stats['open']); ?>

                    </p>
                </div>
                <div class="w-10 h-10 rounded bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-lg">
                    📬
                </div>
            </div>
        </div>

        <div class="panel border border-gunmetal/60 bg-carbon p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Sedang Diproses</p>
                    <p class="font-display text-2xl font-bold text-blue-400 mt-1">
                        <?php echo e($stats['in_progress']); ?>

                    </p>
                </div>
                <div class="w-10 h-10 rounded bg-blue-500/10 border border-blue-500/30 flex items-center justify-center text-lg">
                    ⚙️
                </div>
            </div>
        </div>

        <div class="panel border border-gunmetal/60 bg-carbon p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Terselesaikan</p>
                    <p class="font-display text-2xl font-bold text-emerald-400 mt-1">
                        <?php echo e($stats['resolved']); ?>

                    </p>
                </div>
                <div class="w-10 h-10 rounded bg-emerald-950/40 border border-emerald-800/40 flex items-center justify-center text-lg">
                    ✅
                </div>
            </div>
        </div>

        <div class="panel border border-gunmetal/60 bg-carbon p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Total Tiket</p>
                    <p class="font-display text-2xl font-bold text-silver-bright mt-1">
                        <?php echo e($stats['total']); ?>

                    </p>
                </div>
                <div class="w-10 h-10 rounded bg-steel/30 border border-gunmetal flex items-center justify-center text-lg">
                    🏷️
                </div>
            </div>
        </div>
    </div>

    
    <div class="panel border border-gunmetal/60 bg-carbon p-4">
        <form method="GET" action="<?php echo e(route('admin.tickets.index')); ?>" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari Kode, Judul, Customer..." 
                       class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 focus:border-giallo focus:outline-none placeholder-silver-dim">
            </div>
            <div>
                <select name="status" class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 focus:border-giallo focus:outline-none">
                    <option value="">Semua Status</option>
                    <option value="open" <?php echo e(request('status') === 'open' ? 'selected' : ''); ?>>Open (Terbuka)</option>
                    <option value="in_progress" <?php echo e(request('status') === 'in_progress' ? 'selected' : ''); ?>>In Progress (Diproses)</option>
                    <option value="resolved" <?php echo e(request('status') === 'resolved' ? 'selected' : ''); ?>>Resolved (Selesai)</option>
                    <option value="closed" <?php echo e(request('status') === 'closed' ? 'selected' : ''); ?>>Closed (Ditutup)</option>
                </select>
            </div>
            <div>
                <select name="priority" class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2.5 focus:border-giallo focus:outline-none">
                    <option value="">Semua Prioritas</option>
                    <option value="low" <?php echo e(request('priority') === 'low' ? 'selected' : ''); ?>>Low (Rendah)</option>
                    <option value="medium" <?php echo e(request('priority') === 'medium' ? 'selected' : ''); ?>>Medium (Sedang)</option>
                    <option value="high" <?php echo e(request('priority') === 'high' ? 'selected' : ''); ?>>High (Tinggi)</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-giallo hover:bg-giallo-bright text-carbon font-heading text-xs uppercase font-bold py-2.5 px-4 rounded transition-premium">
                    Filter
                </button>
                <?php if(request()->hasAny(['search', 'status', 'priority'])): ?>
                    <a href="<?php echo e(route('admin.tickets.index')); ?>" class="bg-steel hover:bg-gunmetal text-silver-bright font-heading text-xs uppercase py-2.5 px-3 rounded flex items-center justify-center">
                        Reset
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    
    <div class="panel border border-gunmetal/60 bg-carbon overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-steel/40 text-silver-dim font-heading uppercase text-[10px] tracking-wider border-b border-gunmetal">
                    <tr>
                        <th class="py-3 px-4">Kode Tiket</th>
                        <th class="py-3 px-4">Customer</th>
                        <th class="py-3 px-4">Subjek / Masalah</th>
                        <th class="py-3 px-4">Prioritas</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Petugas</th>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/40">
                    <?php $__empty_1 = true; $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-steel/20 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-giallo">
                                #<?php echo e($ticket->code); ?>

                            </td>
                            <td class="py-3.5 px-4">
                                <p class="font-bold text-silver-bright"><?php echo e($ticket->customer->user->name ?? 'Customer'); ?></p>
                                <p class="text-[10px] text-silver-dim"><?php echo e($ticket->customer->user->email ?? $ticket->customer->phone); ?></p>
                            </td>
                            <td class="py-3.5 px-4 max-w-xs">
                                <p class="font-medium text-silver-bright truncate"><?php echo e($ticket->subject); ?></p>
                                <p class="text-[10px] text-silver-dim truncate"><?php echo e(Str::limit($ticket->description, 50)); ?></p>
                            </td>
                            <td class="py-3.5 px-4">
                                <?php
                                    $p = $ticket->priority->value ?? $ticket->priority;
                                ?>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                    <?php if($p === 'high'): ?> bg-danger/20 text-danger border border-danger/40
                                    <?php elseif($p === 'medium'): ?> bg-amber-500/20 text-amber-400 border border-amber-500/40
                                    <?php else: ?> bg-silver-dim/20 text-silver-dim border border-silver-dim/40 <?php endif; ?>">
                                    <?php echo e($p); ?>

                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <?php
                                    $s = $ticket->status->value ?? $ticket->status;
                                ?>
                                <span class="px-2.5 py-1 rounded text-[10px] font-bold uppercase tracking-wider
                                    <?php if($s === 'open'): ?> bg-amber-500/20 text-amber-300 border border-amber-500/40 animate-pulse
                                    <?php elseif($s === 'in_progress'): ?> bg-blue-500/20 text-blue-300 border border-blue-500/40
                                    <?php elseif($s === 'resolved'): ?> bg-emerald-950/60 text-emerald-400 border border-emerald-800
                                    <?php else: ?> bg-steel text-silver-dim border border-gunmetal <?php endif; ?>">
                                    <?php echo e(str_replace('_', ' ', $s)); ?>

                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-silver-dim text-[11px]">
                                <?php echo e($ticket->assignee->name ?? '-'); ?>

                            </td>
                            <td class="py-3.5 px-4 text-silver-dim text-[11px]">
                                <?php echo e($ticket->created_at->format('d M Y, H:i')); ?>

                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="<?php echo e(route('admin.tickets.show', $ticket)); ?>" class="inline-flex items-center gap-1 px-3 py-1.5 bg-steel hover:bg-giallo hover:text-carbon text-silver-bright text-xs font-heading font-medium rounded transition-colors">
                                    <span>Tinjau</span>
                                    <span>→</span>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center py-10 text-silver-dim">
                                Tidak ada tiket layanan yang sesuai kriteria pencarian.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($tickets->hasPages()): ?>
            <div class="p-4 border-t border-gunmetal">
                <?php echo e($tickets->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/admin/tickets/index.blade.php ENDPATH**/ ?>