<?php $__env->startSection('title', 'Garasi Kendaraan - ' . ($customer->user->name ?? 'Customer')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="<?php echo e(route('admin.customers.index')); ?>" class="text-giallo font-mono text-sm tracking-widest uppercase hover:underline">Database Pelanggan</a>
                <span class="text-silver-dim text-xs">/</span>
                <a href="<?php echo e(route('admin.customers.show', $customer)); ?>" class="text-silver-bright font-mono text-xs hover:underline"><?php echo e($customer->code); ?></a>
                <span class="text-silver-dim text-xs">/</span>
                <span class="text-silver-dim text-xs font-mono">Garasi Kendaraan</span>
            </div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide mt-1">
                GARASI KENDARAAN KLIEN
            </h1>
            <p class="text-sm text-silver-dim mt-1 font-mono">
                Pemilik: <span class="text-silver-bright font-bold"><?php echo e($customer->user->name ?? 'Pelanggan'); ?></span> (<?php echo e($customer->code); ?>)
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?php echo e(route('admin.customers.show', $customer)); ?>" class="px-4 py-2 bg-steel/40 hover:bg-steel text-silver-bright border border-gunmetal font-heading text-xs uppercase tracking-wider rounded transition-premium">
                Kembali ke Profil
            </a>
        </div>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php $__empty_1 = true; $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="panel border border-gunmetal bg-carbon p-5 space-y-4 hover:border-giallo/40 transition-premium">
                <div class="flex items-center justify-between border-b border-gunmetal pb-3">
                    <div>
                        <span class="text-[10px] font-mono text-silver-dim uppercase block">Tahun <?php echo e($vehicle->year); ?></span>
                        <h3 class="font-heading text-sm font-bold text-silver-bright">
                            <?php echo e($vehicle->brand); ?> <?php echo e($vehicle->model); ?>

                        </h3>
                    </div>
                    <span class="w-8 h-8 rounded bg-steel/60 border border-gunmetal flex items-center justify-center text-sm">
                        🏎
                    </span>
                </div>

                <div class="space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-silver-dim">Nomor Polisi:</span>
                        <span class="font-mono font-bold text-giallo"><?php echo e($vehicle->plate_number ?: 'Tanpa Plat'); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-silver-dim">Nomor Rangka (VIN):</span>
                        <span class="font-mono text-silver-bright"><?php echo e($vehicle->vin ?: '-'); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-silver-dim">Warna:</span>
                        <span class="text-silver-bright"><?php echo e($vehicle->color ?: '-'); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-silver-dim">Transmisi:</span>
                        <span class="text-silver-bright uppercase"><?php echo e($vehicle->transmission ?: '-'); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-silver-dim">Odometer:</span>
                        <span class="font-mono text-silver-bright"><?php echo e(number_format($vehicle->current_mileage ?? 0, 0, ',', '.')); ?> km</span>
                    </div>
                </div>

                <?php if($vehicle->notes): ?>
                    <div class="p-2.5 bg-steel/20 border border-gunmetal/60 rounded text-[11px] text-silver-dim">
                        <span class="font-bold text-silver-bright block text-[10px] uppercase mb-0.5">Catatan Khusus:</span>
                        <?php echo e($vehicle->notes); ?>

                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-full panel border border-gunmetal bg-carbon p-12 text-center text-silver-dim">
                <span class="text-4xl block mb-2">🏎</span>
                <p class="font-heading text-sm uppercase">Belum Ada Kendaraan Terdaftar</p>
                <p class="text-xs text-silver-dim mt-1">Klien ini belum menambahkan kendaraan ke dalam garasi digital.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/admin/customers/vehicles.blade.php ENDPATH**/ ?>