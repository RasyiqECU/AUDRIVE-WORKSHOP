<?php $__env->startSection('title', 'Riwayat Servis Kendaraan — AUDrive'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <a href="<?php echo e(route('workshop.index')); ?>" class="text-xs text-giallo hover:underline uppercase tracking-wider font-heading">
            &larr; Kembali ke Workshop
        </a>
        <h1 class="font-display text-2xl font-bold text-silver-bright mt-2">RIWAYAT SERVIS</h1>
        <p class="text-xs text-silver-dim mt-1">Daftar servis kendaraan Anda yang telah selesai atau dibatalkan.</p>
    </div>

    <?php if($bookings->isEmpty()): ?>
        <div class="text-center py-16 bg-nero-dark border border-white/10 rounded-lg">
            <p class="text-silver-dim text-sm">Belum ada riwayat servis kendaraan.</p>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="p-4 bg-nero-dark border border-white/10 rounded-lg flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-heading font-bold text-silver-bright text-sm uppercase"><?php echo e($booking->service_type); ?></span>
                            <?php if($booking->workOrder && ($booking->workOrder->status->value ?? $booking->workOrder->status) === 'delivered'): ?>
                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded uppercase tracking-wider bg-green-500/20 text-green-400 border border-green-500/30">
                                    Diserahkan (Delivered)
                                </span>
                            <?php else: ?>
                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded uppercase tracking-wider <?php echo e(($booking->status->value ?? $booking->status) === 'completed' ? 'bg-green-500/20 text-green-400 border border-green-500/30' : 'bg-red-500/20 text-red-400 border border-red-500/30'); ?>">
                                    <?php echo e(($booking->status->value ?? $booking->status) === 'completed' ? 'Selesai' : 'Dibatalkan'); ?>

                                </span>
                            <?php endif; ?>
                        </div>
                        <p class="text-xs text-silver-dim mt-1">
                            Kendaraan: <span class="text-silver-bright"><?php echo e($booking->vehicle?->license_plate ?? 'N/A'); ?></span> — <?php echo e($booking->vehicle?->brand); ?> <?php echo e($booking->vehicle?->model); ?>

                        </p>
                        <p class="text-xs text-silver-dim">
                            Tanggal: <?php echo e($booking->preferred_date?->format('d M Y')); ?>

                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <?php if($booking->workOrder): ?>
                            <a href="<?php echo e(route('workshop.tracking.show', $booking->workOrder)); ?>" class="px-3 py-1.5 bg-giallo/20 text-giallo text-xs rounded hover:bg-giallo/30 transition">
                                Lacak Servis
                            </a>
                        <?php endif; ?>
                        <a href="<?php echo e(route('workshop.booking.show', $booking)); ?>" class="px-3 py-1.5 border border-white/20 text-xs text-silver-bright rounded hover:border-giallo transition">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <div class="mt-4">
                <?php echo e($bookings->links()); ?>

            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make(auth()->check() && auth()->user()->role === 'customer' ? 'layouts.customer' : (auth()->check() ? 'layouts.app' : 'layouts.public'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/workshop/history.blade.php ENDPATH**/ ?>