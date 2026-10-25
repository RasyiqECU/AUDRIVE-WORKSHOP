<?php $__env->startSection('title', 'Manajemen Booking Servis'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">
                BOOKING SERVIS WORKSHOP
            </h1>
            <p class="text-sm text-silver-dim mt-1">
                Daftar antrian booking servis berkala & perbaikan supercar dari pelanggan
            </p>
        </div>
    </div>

    
    <div class="panel border border-gunmetal bg-carbon overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-steel/40 border-b border-gunmetal text-[11px] font-heading uppercase tracking-wider text-silver-dim">
                        <th class="py-3 px-4">Customer</th>
                        <th class="py-3 px-4">Kendaraan</th>
                        <th class="py-3 px-4">Layanan</th>
                        <th class="py-3 px-4">Jadwal Kedatangan</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/40 text-xs">
                    <?php $__empty_1 = true; $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-steel/20 transition-premium">
                            <td class="py-3.5 px-4">
                                <p class="font-bold text-silver-bright"><?php echo e($booking->customer->user->name ?? 'Customer'); ?></p>
                                <p class="font-mono text-[10px] text-silver-dim"><?php echo e($booking->customer->user->phone ?? '-'); ?></p>
                            </td>
                            <td class="py-3.5 px-4">
                                <?php if($booking->vehicle): ?>
                                    <p class="font-bold text-silver-bright"><?php echo e($booking->vehicle->brand); ?> <?php echo e($booking->vehicle->model); ?></p>
                                    <span class="font-mono text-[10px] text-giallo"><?php echo e($booking->vehicle->plate_number ?: 'Tanpa Plat'); ?></span>
                                <?php else: ?>
                                    <span class="text-silver-dim">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4 font-heading uppercase tracking-wider text-silver-bright">
                                <?php echo e(ucfirst(str_replace('_', ' ', $booking->service_type))); ?>

                            </td>
                            <td class="py-3.5 px-4 font-mono">
                                <p class="text-silver-bright"><?php echo e($booking->preferred_date ? $booking->preferred_date->format('d M Y') : '-'); ?></p>
                                <p class="text-silver-dim text-[10px]"><?php echo e($booking->preferred_time); ?></p>
                            </td>
                            <td class="py-3.5 px-4">
                                <?php if (isset($component)) { $__componentOriginald271113ca69d52f8dbc84ae4f8468e44 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald271113ca69d52f8dbc84ae4f8468e44 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.status-pill','data' => ['status' => $booking->status->value ?? $booking->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.status-pill'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($booking->status->value ?? $booking->status)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald271113ca69d52f8dbc84ae4f8468e44)): ?>
<?php $attributes = $__attributesOriginald271113ca69d52f8dbc84ae4f8468e44; ?>
<?php unset($__attributesOriginald271113ca69d52f8dbc84ae4f8468e44); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald271113ca69d52f8dbc84ae4f8468e44)): ?>
<?php $component = $__componentOriginald271113ca69d52f8dbc84ae4f8468e44; ?>
<?php unset($__componentOriginald271113ca69d52f8dbc84ae4f8468e44); ?>
<?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4 text-right space-x-2">
                                <a href="<?php echo e(route('admin.service-bookings.show', $booking)); ?>" class="px-3 py-1.5 bg-steel/50 hover:bg-giallo hover:text-nero border border-gunmetal text-silver-bright font-heading text-[10px] uppercase rounded transition-premium">
                                    Detail / Konversi
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="py-8 text-center text-silver-dim font-heading uppercase text-xs tracking-wider">
                                Belum ada antrian booking servis
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($bookings->hasPages()): ?>
            <div class="p-4 border-t border-gunmetal bg-steel/20">
                <?php echo e($bookings->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/admin/service-bookings/index.blade.php ENDPATH**/ ?>