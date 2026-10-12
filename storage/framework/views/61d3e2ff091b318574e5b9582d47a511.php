<?php $__env->startSection('title', 'Work Order ' . $workOrder->code); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="<?php echo e(route('admin.work-orders.index')); ?>" class="font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider">&larr; Daftar Work Order</a>
                <span class="text-silver-dim text-xs">/</span>
                <span class="font-mono text-xs text-silver-dim"><?php echo e($workOrder->code); ?></span>
            </div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">
                WORK ORDER: <?php echo e($workOrder->code); ?>

            </h1>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <?php if (isset($component)) { $__componentOriginald271113ca69d52f8dbc84ae4f8468e44 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald271113ca69d52f8dbc84ae4f8468e44 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.status-pill','data' => ['status' => $workOrder->status->value ?? $workOrder->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.status-pill'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($workOrder->status->value ?? $workOrder->status)]); ?>
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

            <?php $statusStr = $workOrder->status->value ?? $workOrder->status; ?>

            <?php if($workOrder->total_idr > 0): ?>
                <a href="<?php echo e(route('admin.pos.index', ['work_order_id' => $workOrder->id])); ?>"
                   class="px-4 py-2 bg-giallo hover:bg-arancio text-nero font-heading font-bold text-xs uppercase tracking-wider rounded shadow-lg shadow-giallo/20 transition-premium flex items-center gap-1.5">
                    💳 Bayar di Kasir (POS)
                </a>
            <?php endif; ?>

            <?php if($statusStr === 'in_progress'): ?>
                <form action="<?php echo e(route('admin.work-orders.complete', $workOrder)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="px-4 py-2 bg-verde hover:bg-verde/80 text-nero font-heading font-bold text-xs uppercase tracking-wider rounded transition-premium">
                        ✓ Selesaikan Pengerjaan
                    </button>
                </form>
            <?php elseif($statusStr === 'completed'): ?>
                <form action="<?php echo e(route('admin.work-orders.deliver', $workOrder)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="px-4 py-2 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright font-heading font-bold text-xs uppercase tracking-wider rounded transition-premium">
                        🏎 Serahkan ke Pelanggan
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="panel border border-gunmetal bg-carbon p-5 space-y-2 text-xs">
            <h3 class="font-heading text-[11px] uppercase tracking-wider text-silver-dim">Customer & Kendaraan</h3>
            <p class="font-bold text-silver-bright text-sm"><?php echo e($workOrder->vehicle?->customer?->user?->name ?? 'Customer'); ?></p>
            <p class="font-mono text-silver-dim"><?php echo e($workOrder->vehicle?->customer?->user?->phone ?? '-'); ?></p>
            <div class="pt-2 border-t border-gunmetal/40">
                <p class="font-bold text-giallo"><?php echo e($workOrder->vehicle ? "{$workOrder->vehicle->brand} {$workOrder->vehicle->model} ({$workOrder->vehicle->year})" : '-'); ?></p>
                <p class="font-mono text-silver-dim"><?php echo e($workOrder->vehicle?->plate_number ?: 'Tanpa Plat'); ?></p>
            </div>
        </div>

        <div class="panel border border-gunmetal bg-carbon p-5 space-y-2 text-xs">
            <h3 class="font-heading text-[11px] uppercase tracking-wider text-silver-dim">Mekanik yang Bertugas</h3>
            <?php if($workOrder->mechanic): ?>
                <p class="font-bold text-silver-bright text-sm">👨‍🔧 <?php echo e($workOrder->mechanic->name); ?></p>
                <p class="font-mono text-silver-dim"><?php echo e($workOrder->mechanic->phone ?? '-'); ?></p>
            <?php else: ?>
                <p class="text-silver-dim italic">Belum ditugaskan ke mekanik.</p>
            <?php endif; ?>
            <div class="pt-2 border-t border-gunmetal/40 font-mono text-[10px] text-silver-dim">
                <p>Mulai: <?php echo e($workOrder->started_at ? $workOrder->started_at->format('d M Y H:i') : '-'); ?></p>
                <p>Selesai: <?php echo e($workOrder->completed_at ? $workOrder->completed_at->format('d M Y H:i') : '-'); ?></p>
            </div>
        </div>

        <div class="panel border border-gunmetal bg-carbon p-5 space-y-2 text-xs">
            <h3 class="font-heading text-[11px] uppercase tracking-wider text-silver-dim">Ringkasan Biaya</h3>
            <div class="space-y-1 font-mono">
                <div class="flex justify-between text-silver-dim">
                    <span>Biaya Jasa:</span>
                    <span>Rp <?php echo e(number_format($workOrder->total_jasa_idr, 0, ',', '.')); ?></span>
                </div>
                <div class="flex justify-between text-silver-dim">
                    <span>Suku Cadang:</span>
                    <span>Rp <?php echo e(number_format($workOrder->total_part_idr, 0, ',', '.')); ?></span>
                </div>
                <div class="pt-2 border-t border-gunmetal flex justify-between font-bold text-giallo text-sm">
                    <span>Total IDR:</span>
                    <span>Rp <?php echo e(number_format($workOrder->total_idr, 0, ',', '.')); ?></span>
                </div>
            </div>
        </div>
    </div>

    
    <div class="panel border border-gunmetal bg-carbon overflow-hidden">
        <div class="bg-steel/30 px-6 py-4 border-b border-gunmetal flex items-center justify-between">
            <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright">
                Rincian Item Pekerjaan & Suku Cadang
            </h3>
        </div>

        <div class="p-6 space-y-6">
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gunmetal text-[10px] font-heading uppercase tracking-wider text-silver-dim">
                            <th class="py-2 px-3">Tipe</th>
                            <th class="py-2 px-3">Deskripsi / Item</th>
                            <th class="py-2 px-3 text-center">Qty</th>
                            <th class="py-2 px-3 text-right">Harga Satuan</th>
                            <th class="py-2 px-3 text-right">Subtotal</th>
                            <th class="py-2 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gunmetal/30 text-xs font-mono">
                        <?php $__empty_1 = true; $__currentLoopData = $workOrder->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-steel/10">
                                <td class="py-2.5 px-3">
                                    <span class="px-2 py-0.5 text-[9px] uppercase font-bold rounded <?php echo e($item->item_type === 'service' ? 'bg-giallo/10 text-giallo' : 'bg-verde/10 text-verde'); ?>">
                                        <?php echo e($item->item_type); ?>

                                    </span>
                                </td>
                                <td class="py-2.5 px-3 text-silver-bright font-sans">
                                    <?php echo e($item->description ?: ($item->sparePart ? $item->sparePart->name : '-')); ?>

                                </td>
                                <td class="py-2.5 px-3 text-center"><?php echo e($item->qty); ?></td>
                                <td class="py-2.5 px-3 text-right text-silver-dim">Rp <?php echo e(number_format($item->unit_price_idr, 0, ',', '.')); ?></td>
                                <td class="py-2.5 px-3 text-right font-bold text-silver-bright">Rp <?php echo e(number_format($item->subtotal_idr, 0, ',', '.')); ?></td>
                                <td class="py-2.5 px-3 text-right">
                                    <form action="<?php echo e(route('admin.work-orders.items.destroy', $item)); ?>" method="POST" onsubmit="return confirm('Hapus item ini?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-danger hover:underline text-[10px] font-sans uppercase">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="py-6 text-center text-silver-dim font-sans text-xs italic">
                                    Belum ada item jasa atau spare part yang ditambahkan ke Work Order ini.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gunmetal">
                
                <div class="p-4 bg-steel/20 border border-gunmetal rounded-sm space-y-3">
                    <h4 class="font-heading text-xs uppercase tracking-wider text-giallo font-semibold">+ Tambah Jasa Servis / Tune-up</h4>
                    <form action="<?php echo e(route('admin.work-orders.service-items.store', $workOrder)); ?>" method="POST" class="space-y-3">
                        <?php echo csrf_field(); ?>
                        <div>
                            <input type="text" name="description" required placeholder="Deskripsi Jasa (cth: Engine Tuning & Oil Flush)" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" name="qty" required min="1" value="1" placeholder="Qty" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                            <input type="number" name="unit_price_idr" required min="0" placeholder="Biaya IDR" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                        </div>
                        <button type="submit" class="w-full py-2 bg-giallo text-nero font-heading font-bold text-xs uppercase tracking-wider hover:bg-arancio transition">
                            Tambah Jasa
                        </button>
                    </form>
                </div>

                
                <div class="p-4 bg-steel/20 border border-gunmetal rounded-sm space-y-3">
                    <h4 class="font-heading text-xs uppercase tracking-wider text-silver-bright font-semibold">⚙ Ubah Status Work Order</h4>
                    <form action="<?php echo e(route('admin.work-orders.status', $workOrder)); ?>" method="POST" class="space-y-3">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <select name="status" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                            <?php $__currentLoopData = \App\Enums\WorkOrderStatus::cases(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sCase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($sCase->value); ?>" <?php echo e(($workOrder->status->value ?? $workOrder->status) === $sCase->value ? 'selected' : ''); ?>>
                                    <?php echo e(ucfirst(str_replace('_', ' ', $sCase->value))); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <button type="submit" class="w-full py-2 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright font-heading font-semibold text-xs uppercase tracking-wider transition">
                            Perbarui Status
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/admin/work-orders/show.blade.php ENDPATH**/ ?>