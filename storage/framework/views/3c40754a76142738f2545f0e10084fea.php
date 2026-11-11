<?php $__env->startSection('title', 'Riwayat Servis - ' . ($customer->user->name ?? 'Customer')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="<?php echo e(route('admin.customers.index')); ?>" class="text-giallo font-mono text-sm tracking-widest uppercase hover:underline">Database Pelanggan</a>
                <span class="text-silver-dim text-xs">/</span>
                <a href="<?php echo e(route('admin.customers.show', $customer)); ?>" class="text-silver-bright font-mono text-xs hover:underline"><?php echo e($customer->code); ?></a>
                <span class="text-silver-dim text-xs">/</span>
                <span class="text-silver-dim text-xs font-mono">Riwayat Servis</span>
            </div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide mt-1">
                RIWAYAT SERVIS BENGKEL
            </h1>
            <p class="text-sm text-silver-dim mt-1 font-mono">
                Klien: <span class="text-silver-bright font-bold"><?php echo e($customer->user->name ?? 'Pelanggan'); ?></span> (<?php echo e($customer->code); ?>)
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?php echo e(route('admin.customers.show', $customer)); ?>" class="px-4 py-2 bg-steel/40 hover:bg-steel text-silver-bright border border-gunmetal font-heading text-xs uppercase tracking-wider rounded transition-premium">
                Kembali ke Profil
            </a>
        </div>
    </div>

    
    <div class="panel border border-gunmetal bg-carbon overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-steel/30 text-silver-dim font-heading uppercase text-[10px] border-b border-gunmetal">
                        <th class="py-3 px-4">Layanan Servis</th>
                        <th class="py-3 px-4">Kendaraan</th>
                        <th class="py-3 px-4">Jadwal Kedatangan</th>
                        <th class="py-3 px-4">Catatan Servis</th>
                        <th class="py-3 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/30">
                    <?php $__empty_1 = true; $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-steel/10 transition-premium">
                            <td class="py-3.5 px-4 font-bold text-silver-bright uppercase">
                                <?php echo e(str_replace('_', ' ', $booking->service_type)); ?>

                            </td>
                            <td class="py-3.5 px-4">
                                <?php if($booking->vehicle): ?>
                                    <p class="font-bold text-silver-bright"><?php echo e($booking->vehicle->brand); ?> <?php echo e($booking->vehicle->model); ?></p>
                                    <p class="text-[10px] font-mono text-giallo"><?php echo e($booking->vehicle->plate_number ?: 'Tanpa Plat'); ?></p>
                                <?php else: ?>
                                    <span class="text-silver-dim">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-silver-dim">
                                <span class="text-silver-bright"><?php echo e($booking->preferred_date ? $booking->preferred_date->format('d M Y') : '-'); ?></span>
                                <span class="block text-[10px]"><?php echo e($booking->preferred_time); ?></span>
                            </td>
                            <td class="py-3.5 px-4 text-silver-dim">
                                <?php echo e($booking->notes ?: '-'); ?>

                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex px-2 py-0.5 rounded text-[9px] font-mono uppercase font-bold <?php echo e($booking->status->value === 'confirmed' ? 'bg-verde/20 text-verde border border-verde/40' : 'bg-steel/40 text-silver-dim'); ?>">
                                    <?php echo e($booking->status->value ?? $booking->status); ?>

                                </span>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="py-10 text-center text-silver-dim">Belum ada riwayat pengerjaan servis untuk klien ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/admin/customers/history.blade.php ENDPATH**/ ?>