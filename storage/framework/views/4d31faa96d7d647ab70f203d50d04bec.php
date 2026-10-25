<?php $__env->startSection('title', 'Manajemen Work Order Workshop'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">
                MANAJEMEN WORK ORDER (WO)
            </h1>
            <p class="text-sm text-silver-dim mt-1">
                Pantau progres pengerjaan servis mekanik, pergantian suku cadang, dan rincian biaya
            </p>
        </div>
    </div>

    
    <div class="panel border border-gunmetal bg-carbon/90 p-4">
        <form method="GET" action="<?php echo e(route('admin.work-orders.index')); ?>" class="flex flex-wrap items-center gap-3">
            <select name="status" class="bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                <option value="">Semua Status</option>
                <option value="draft" <?php echo e(request('status') === 'draft' ? 'selected' : ''); ?>>Draft</option>
                <option value="in_progress" <?php echo e(request('status') === 'in_progress' ? 'selected' : ''); ?>>Sedang Dikerjakan (In Progress)</option>
                <option value="waiting_parts" <?php echo e(request('status') === 'waiting_parts' ? 'selected' : ''); ?>>Menunggu Part</option>
                <option value="completed" <?php echo e(request('status') === 'completed' ? 'selected' : ''); ?>>Selesai (Completed)</option>
                <option value="delivered" <?php echo e(request('status') === 'delivered' ? 'selected' : ''); ?>>Diserahkan (Delivered)</option>
                <option value="cancelled" <?php echo e(request('status') === 'cancelled' ? 'selected' : ''); ?>>Dibatalkan</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright font-heading text-xs uppercase tracking-wider transition-premium">
                Filter
            </button>
            <?php if(request('status')): ?>
                <a href="<?php echo e(route('admin.work-orders.index')); ?>" class="text-xs text-silver-dim hover:text-giallo uppercase tracking-wider">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    
    <div class="panel border border-gunmetal bg-carbon overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-steel/40 border-b border-gunmetal text-[11px] font-heading uppercase tracking-wider text-silver-dim">
                        <th class="py-3 px-4">Kode WO</th>
                        <th class="py-3 px-4">Customer & Kendaraan</th>
                        <th class="py-3 px-4">Mekanik</th>
                        <th class="py-3 px-4">Total Biaya</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/40 text-xs">
                    <?php $__empty_1 = true; $__currentLoopData = $workOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-steel/20 transition-premium">
                            <td class="py-3.5 px-4 font-mono">
                                <span class="font-bold text-giallo"><?php echo e($wo->code); ?></span>
                                <span class="block text-[10px] text-silver-dim"><?php echo e($wo->created_at->format('d M Y')); ?></span>
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="font-bold text-silver-bright"><?php echo e($wo->vehicle?->customer?->user?->name ?? 'Customer'); ?></p>
                                <p class="font-mono text-[10px] text-silver-dim">
                                    <?php echo e($wo->vehicle ? "{$wo->vehicle->brand} {$wo->vehicle->model} ({$wo->vehicle->plate_number})" : '-'); ?>

                                </p>
                            </td>
                            <td class="py-3.5 px-4">
                                <?php if($wo->mechanic): ?>
                                    <span class="text-silver-bright font-semibold">👨‍🔧 <?php echo e($wo->mechanic->name); ?></span>
                                <?php else: ?>
                                    <span class="text-silver-dim italic">Belum Ditugaskan</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-silver-bright">
                                Rp <?php echo e(number_format($wo->total_idr, 0, ',', '.')); ?>

                            </td>
                            <td class="py-3.5 px-4">
                                <?php if (isset($component)) { $__componentOriginald271113ca69d52f8dbc84ae4f8468e44 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald271113ca69d52f8dbc84ae4f8468e44 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.status-pill','data' => ['status' => $wo->status->value ?? $wo->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.status-pill'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($wo->status->value ?? $wo->status)]); ?>
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
                            <td class="py-3.5 px-4 text-right">
                                <a href="<?php echo e(route('admin.work-orders.show', $wo)); ?>" class="px-3 py-1.5 bg-steel/50 hover:bg-giallo hover:text-nero border border-gunmetal text-silver-bright font-heading text-[10px] uppercase rounded transition-premium">
                                    Detail / Kelola
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="py-8 text-center text-silver-dim font-heading uppercase text-xs tracking-wider">
                                Belum ada data Work Order
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($workOrders->hasPages()): ?>
            <div class="p-4 border-t border-gunmetal bg-steel/20">
                <?php echo e($workOrders->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/admin/work-orders/index.blade.php ENDPATH**/ ?>