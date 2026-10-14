<?php $__env->startSection('title', 'Detail Booking Servis'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-4xl mx-auto">
    
    <div class="flex items-center justify-between">
        <a href="<?php echo e(route('admin.service-bookings.index')); ?>" class="inline-flex items-center gap-2 font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider transition-premium">
            <span>&larr;</span> Kembali ke Daftar Booking
        </a>
    </div>

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">
                DETAIL BOOKING SERVIS #<?php echo e($serviceBooking->id); ?>

            </h1>
            <p class="text-xs text-silver-dim mt-1">
                Dibuat pada <?php echo e($serviceBooking->created_at->format('d M Y H:i')); ?>

            </p>
        </div>
        <div>
            <?php if (isset($component)) { $__componentOriginald271113ca69d52f8dbc84ae4f8468e44 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald271113ca69d52f8dbc84ae4f8468e44 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.status-pill','data' => ['status' => $serviceBooking->status->value ?? $serviceBooking->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.status-pill'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($serviceBooking->status->value ?? $serviceBooking->status)]); ?>
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
        </div>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <div class="panel border border-gunmetal bg-carbon p-5 space-y-4">
            <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright border-b border-gunmetal pb-2">
                Informasi Pelanggan & Kendaraan
            </h3>
            <dl class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <dt class="text-silver-dim">Nama Pelanggan:</dt>
                    <dd class="font-bold text-silver-bright"><?php echo e($serviceBooking->customer->user->name ?? '-'); ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-silver-dim">No. Telepon / WA:</dt>
                    <dd class="font-mono text-silver-bright"><?php echo e($serviceBooking->customer->user->phone ?? '-'); ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-silver-dim">Email:</dt>
                    <dd class="text-silver-bright"><?php echo e($serviceBooking->customer->user->email ?? '-'); ?></dd>
                </div>
                <div class="pt-2 border-t border-gunmetal/40 flex justify-between">
                    <dt class="text-silver-dim">Kendaraan:</dt>
                    <dd class="font-bold text-giallo">
                        <?php if($serviceBooking->vehicle): ?>
                            <?php echo e($serviceBooking->vehicle->year); ?> <?php echo e($serviceBooking->vehicle->brand); ?> <?php echo e($serviceBooking->vehicle->model); ?>

                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-silver-dim">Nomor Polisi:</dt>
                    <dd class="font-mono text-silver-bright"><?php echo e($serviceBooking->vehicle->plate_number ?? '-'); ?></dd>
                </div>
            </dl>
        </div>

        
        <div class="panel border border-gunmetal bg-carbon p-5 space-y-4">
            <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright border-b border-gunmetal pb-2">
                Jadwal & Layanan
            </h3>
            <dl class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <dt class="text-silver-dim">Jenis Layanan:</dt>
                    <dd class="font-heading font-semibold text-silver-bright uppercase"><?php echo e(ucfirst(str_replace('_', ' ', $serviceBooking->service_type))); ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-silver-dim">Tanggal Booking:</dt>
                    <dd class="font-mono font-bold text-silver-bright"><?php echo e($serviceBooking->preferred_date ? $serviceBooking->preferred_date->format('d M Y') : '-'); ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-silver-dim">Waktu Pilihan:</dt>
                    <dd class="font-mono text-silver-bright"><?php echo e($serviceBooking->preferred_time ?? '-'); ?></dd>
                </div>
                <div class="pt-2 border-t border-gunmetal/40">
                    <dt class="text-silver-dim mb-1">Catatan Keluhan:</dt>
                    <dd class="bg-nero/50 p-2.5 border border-gunmetal text-silver italic">
                        "<?php echo e($serviceBooking->notes ?: 'Tidak ada catatan khusus.'); ?>"
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    
    <div class="panel border border-gunmetal bg-carbon p-6 space-y-4">
        <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright">
            Tindakan & Konversi Work Order
        </h3>

        <div class="flex flex-wrap items-center gap-4">
            <?php if($serviceBooking->workOrder): ?>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-verde">✓ Sudah dikonversi ke Work Order:</span>
                    <a href="<?php echo e(route('admin.work-orders.show', $serviceBooking->workOrder)); ?>" class="px-3 py-1.5 bg-giallo text-nero font-heading text-xs font-bold uppercase rounded">
                        Lihat WO #<?php echo e($serviceBooking->workOrder->code); ?>

                    </a>
                </div>
            <?php else: ?>
                <form action="<?php echo e(route('admin.service-bookings.convert', $serviceBooking)); ?>" method="POST" class="flex flex-wrap items-center gap-3">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="px-5 py-2.5 bg-giallo hover:bg-arancio text-nero font-heading font-bold text-xs uppercase tracking-wider rounded shadow-lg shadow-giallo/20 transition-premium">
                        🔧 Konversi ke Work Order (Mulai Pengerjaan)
                    </button>
                </form>
            <?php endif; ?>

            
            <form action="<?php echo e(route('admin.service-bookings.status', $serviceBooking)); ?>" method="POST" class="flex items-center gap-2 ml-auto">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PATCH'); ?>
                <select name="status" class="bg-nero border border-gunmetal px-3 py-1.5 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                    <?php $cStatus = $serviceBooking->status->value ?? $serviceBooking->status; ?>
                    <option value="pending" <?php echo e($cStatus === 'pending' ? 'selected' : ''); ?>>Pending</option>
                    <option value="confirmed" <?php echo e($cStatus === 'confirmed' ? 'selected' : ''); ?>>Confirmed</option>
                    <option value="in_progress" <?php echo e($cStatus === 'in_progress' ? 'selected' : ''); ?>>In Progress</option>
                    <option value="completed" <?php echo e($cStatus === 'completed' ? 'selected' : ''); ?>>Completed</option>
                    <option value="cancelled" <?php echo e($cStatus === 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                </select>
                <button type="submit" class="px-3 py-1.5 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright font-heading text-xs uppercase rounded transition-premium">
                    Update Status
                </button>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/admin/service-bookings/show.blade.php ENDPATH**/ ?>