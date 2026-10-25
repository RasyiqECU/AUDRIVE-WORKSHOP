<?php $__env->startSection('title', 'Jadwal Uji Kendaraan & Test Drive'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <span class="text-giallo font-heading uppercase tracking-wider">Jadwal Test Drive</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Showroom & Sales Activity</p>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                JADWAL UJI KENDARAAN (TEST DRIVE)
            </h1>
            <p class="text-xs text-silver-dim mt-1">
                Kelola permohonan test drive, konfirmasi jadwal kepada customer, dan pantau aktivitas showroom.
            </p>
        </div>
    </div>

    
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="panel border <?php echo e($pendingCount > 0 ? 'border-giallo/60 bg-carbon' : 'border-gunmetal/60 bg-carbon'); ?> p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Menunggu Konfirmasi</p>
                    <p class="font-display text-2xl font-bold <?php echo e($pendingCount > 0 ? 'text-giallo' : 'text-silver-bright'); ?> mt-1">
                        <?php echo e($pendingCount); ?>

                    </p>
                </div>
                <div class="w-10 h-10 rounded bg-giallo/10 border border-giallo/30 flex items-center justify-center text-lg">
                    🏎️
                </div>
            </div>
        </div>

        <div class="panel border border-gunmetal/60 bg-carbon p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Telah Dikonfirmasi</p>
                    <p class="font-display text-2xl font-bold text-emerald-400 mt-1">
                        <?php echo e($confirmedCount); ?>

                    </p>
                </div>
                <div class="w-10 h-10 rounded bg-emerald-950/40 border border-emerald-800/40 flex items-center justify-center text-lg">
                    📅
                </div>
            </div>
        </div>

        <div class="panel border border-gunmetal/60 bg-carbon p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Total Pengajuan</p>
                    <p class="font-display text-2xl font-bold text-silver-bright mt-1">
                        <?php echo e($totalCount); ?>

                    </p>
                </div>
                <div class="w-10 h-10 rounded bg-steel/30 border border-gunmetal flex items-center justify-center text-lg">
                    🏁
                </div>
            </div>
        </div>
    </div>

    
    <div class="panel border border-gunmetal/70 bg-carbon p-4">
        <form action="<?php echo e(route('admin.appointments.index')); ?>" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="md:col-span-2">
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari nama customer, email, unit mobil..." class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2 focus:border-giallo focus:outline-none">
            </div>
            <div>
                <select name="status" class="w-full bg-steel/60 border border-gunmetal text-silver-bright text-xs rounded p-2 focus:border-giallo focus:outline-none">
                    <option value="">Semua Status</option>
                    <option value="pending" <?php echo e(request('status') === 'pending' ? 'selected' : ''); ?>>Menunggu Konfirmasi</option>
                    <option value="confirmed" <?php echo e(request('status') === 'confirmed' ? 'selected' : ''); ?>>Dikonfirmasi</option>
                    <option value="completed" <?php echo e(request('status') === 'completed' ? 'selected' : ''); ?>>Selesai</option>
                    <option value="cancelled" <?php echo e(request('status') === 'cancelled' ? 'selected' : ''); ?>>Dibatalkan</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-giallo hover:bg-giallo-bright text-carbon text-xs font-heading uppercase font-bold rounded clip-angular-button transition-premium">
                    Filter
                </button>
                <a href="<?php echo e(route('admin.appointments.index')); ?>" class="px-3 py-2 bg-carbon border border-gunmetal text-silver-dim hover:text-silver-bright text-xs font-heading uppercase rounded">
                    Reset
                </a>
            </div>
        </form>
    </div>

    
    <div class="panel border border-gunmetal/70 bg-carbon overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-silver-muted">
                <thead class="bg-steel/50 text-silver-bright font-heading uppercase text-[10px] tracking-wider border-b border-gunmetal">
                    <tr>
                        <th class="p-3">Unit Kendaraan</th>
                        <th class="p-3">Customer</th>
                        <th class="p-3 text-center">Jadwal Diminta</th>
                        <th class="p-3">Catatan</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 text-right">Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/40">
                    <?php $__empty_1 = true; $__currentLoopData = $appointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $apt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-steel/20 transition-colors <?php echo e($apt->status->value === 'pending' ? 'bg-giallo/5' : ''); ?>">
                            <td class="p-3">
                                <?php if($apt->carUnit): ?>
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-9 rounded bg-nero border border-gunmetal overflow-hidden flex items-center justify-center shrink-0">
                                            <?php if($apt->carUnit->primaryPhoto): ?>
                                                <img src="<?php echo e(asset('storage/' . $apt->carUnit->primaryPhoto->path)); ?>" alt="<?php echo e($apt->carUnit->model); ?>" class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <span class="text-xs">🏎️</span>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <a href="<?php echo e(route('admin.cars.show', $apt->carUnit)); ?>" class="font-heading font-bold text-silver-bright hover:text-giallo block">
                                                <?php echo e($apt->carUnit->brand); ?> <?php echo e($apt->carUnit->model); ?>

                                            </a>
                                            <span class="font-mono text-[10px] text-giallo"><?php echo e($apt->carUnit->code); ?></span>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span class="text-silver-dim">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-3">
                                <span class="font-heading font-bold text-silver-bright block"><?php echo e($apt->customer?->user?->name ?? 'Customer'); ?></span>
                                <span class="font-mono text-[11px] text-emerald-400 block"><?php echo e($apt->customer?->user?->phone ?? '-'); ?></span>
                                <span class="text-silver-dim text-[10px]"><?php echo e($apt->customer?->user?->email ?? '-'); ?></span>
                            </td>
                            <td class="p-3 text-center font-mono">
                                <span class="text-silver-bright font-semibold block"><?php echo e($apt->scheduled_at->format('d M Y')); ?></span>
                                <span class="text-giallo text-[11px] block"><?php echo e($apt->scheduled_at->format('H:i')); ?> WIB</span>
                            </td>
                            <td class="p-3 text-silver text-[11px] max-w-xs truncate">
                                <?php echo e($apt->notes ?: '-'); ?>

                            </td>
                            <td class="p-3 text-center">
                                <?php $st = $apt->status->value; ?>
                                <span class="px-2 py-0.5 text-[10px] font-heading uppercase tracking-wider rounded font-bold
                                    <?php if($st === 'pending'): ?> bg-giallo/20 text-giallo border border-giallo/40
                                    <?php elseif($st === 'confirmed'): ?> bg-emerald-950/60 text-emerald-400 border border-emerald-800
                                    <?php elseif($st === 'completed'): ?> bg-blue-950/60 text-blue-400 border border-blue-800
                                    <?php else: ?> bg-gray-800 text-silver-dim border border-gunmetal <?php endif; ?>">
                                    <?php echo e($apt->status->label()); ?>

                                </span>
                            </td>
                            <td class="p-3 text-right space-x-1.5 whitespace-nowrap">
                                <?php if($apt->status->value === 'pending'): ?>
                                    <form action="<?php echo e(route('admin.appointments.status', $apt)); ?>" method="POST" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <input type="hidden" name="status" value="confirmed">
                                        <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-white font-heading text-[10px] uppercase font-bold rounded shadow transition">
                                            ✓ Konfirmasi
                                        </button>
                                    </form>
                                <?php elseif($apt->status->value === 'confirmed'): ?>
                                    <form action="<?php echo e(route('admin.appointments.status', $apt)); ?>" method="POST" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="px-2.5 py-1 bg-blue-600 hover:bg-blue-500 text-white font-heading text-[10px] uppercase font-bold rounded shadow transition">
                                            🏁 Selesai
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <?php if(in_array($apt->status->value, ['pending', 'confirmed'])): ?>
                                    <form action="<?php echo e(route('admin.appointments.status', $apt)); ?>" method="POST" class="inline" onsubmit="return confirm('Batalkan jadwal test drive ini?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" class="px-2 py-1 bg-steel hover:bg-steel/80 text-silver-dim hover:text-red-400 text-[10px] font-heading uppercase rounded">
                                            ✕ Batal
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <a href="<?php echo e(route('admin.appointments.show', $apt)); ?>" class="px-2 py-1 text-xs text-giallo hover:underline font-heading uppercase font-semibold">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="p-8 text-center text-silver-dim">
                                Tidak ada jadwal uji kendaraan / test drive yang ditemukan.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if($appointments->hasPages()): ?>
        <div><?php echo e($appointments->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/admin/appointments/index.blade.php ENDPATH**/ ?>