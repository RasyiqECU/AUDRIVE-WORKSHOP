<?php $__env->startSection('title', 'Pengajuan Approval #' . $approval->id); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-3xl mx-auto">
    
    <div>
        <a href="<?php echo e(route('admin.approvals.index')); ?>" class="inline-flex items-center gap-2 font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider transition-premium">
            <span>&larr;</span> Kembali ke Pusat Approval
        </a>
        <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-3">
            PENGAJUAN APPROVAL #<?php echo e($approval->id); ?>

        </h1>
        <p class="text-xs text-silver-dim mt-1">
            <?php echo e(ucfirst(str_replace('_', ' ', $approval->type))); ?> • Diajukan <?php echo e($approval->created_at->format('d M Y, H:i')); ?>

        </p>
    </div>

    <?php if(session('success')): ?>
        <?php if (isset($component)) { $__componentOriginal746de018ded8594083eb43be3f1332e1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal746de018ded8594083eb43be3f1332e1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.alert','data' => ['type' => 'success','message' => session('success')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'success','message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(session('success'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal746de018ded8594083eb43be3f1332e1)): ?>
<?php $attributes = $__attributesOriginal746de018ded8594083eb43be3f1332e1; ?>
<?php unset($__attributesOriginal746de018ded8594083eb43be3f1332e1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal746de018ded8594083eb43be3f1332e1)): ?>
<?php $component = $__componentOriginal746de018ded8594083eb43be3f1332e1; ?>
<?php unset($__componentOriginal746de018ded8594083eb43be3f1332e1); ?>
<?php endif; ?>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <?php if (isset($component)) { $__componentOriginal746de018ded8594083eb43be3f1332e1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal746de018ded8594083eb43be3f1332e1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.alert','data' => ['type' => 'danger','message' => session('error')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'danger','message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(session('error'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal746de018ded8594083eb43be3f1332e1)): ?>
<?php $attributes = $__attributesOriginal746de018ded8594083eb43be3f1332e1; ?>
<?php unset($__attributesOriginal746de018ded8594083eb43be3f1332e1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal746de018ded8594083eb43be3f1332e1)): ?>
<?php $component = $__componentOriginal746de018ded8594083eb43be3f1332e1; ?>
<?php unset($__componentOriginal746de018ded8594083eb43be3f1332e1); ?>
<?php endif; ?>
    <?php endif; ?>

    
    <div class="panel border border-gunmetal bg-carbon p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div>
                <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Pemohon</p>
                <p class="text-silver-bright font-heading font-semibold mt-1"><?php echo e($approval->requester?->name ?? '-'); ?></p>
                <p class="font-mono text-[10px] text-silver-dim"><?php echo e($approval->requester?->role ?? ''); ?> • <?php echo e($approval->requester?->email ?? ''); ?></p>
            </div>
            <div>
                <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Status</p>
                <p class="mt-1">
                    <?php if($approval->status->value === 'pending'): ?>
                        <span class="px-2.5 py-1 bg-giallo/10 text-giallo font-heading text-[10px] uppercase tracking-wider border border-giallo/40">⏳ Pending</span>
                    <?php elseif($approval->status->value === 'approved'): ?>
                        <span class="px-2.5 py-1 bg-verde/10 text-verde font-heading text-[10px] uppercase tracking-wider border border-verde/40">✓ Disetujui</span>
                    <?php else: ?>
                        <span class="px-2.5 py-1 bg-danger/10 text-danger font-heading text-[10px] uppercase tracking-wider border border-danger/40">✕ Ditolak</span>
                    <?php endif; ?>
                </p>
            </div>
            <div>
                <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Tipe Pengajuan</p>
                <p class="text-silver-bright font-heading font-semibold mt-1 uppercase"><?php echo e(str_replace('_', ' ', $approval->type)); ?></p>
            </div>
            <div>
                <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Keputusan</p>
                <?php if($approval->approver): ?>
                    <p class="text-silver-bright font-heading font-semibold mt-1"><?php echo e($approval->approver->name); ?></p>
                    <p class="text-[10px] text-silver-dim"><?php echo e($approval->decided_at?->format('d M Y, H:i')); ?></p>
                <?php else: ?>
                    <p class="text-silver-dim mt-1">—</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="panel border border-giallo/30 bg-carbon p-6 space-y-3">
        <h2 class="font-heading text-xs uppercase tracking-wider text-giallo">Rincian Pengajuan</h2>
        <table class="w-full text-xs">
            <tbody class="divide-y divide-gunmetal/30">
                <?php
                    $fieldLabels = [
                        'sales_order_id' => 'ID Sales Order',
                        'payment_id' => 'ID Pembayaran',
                        'spare_part_id' => 'ID Spare Part',
                        'amount_idr' => 'Nominal (IDR)',
                        'new_price_idr' => 'Harga Baru (IDR)',
                        'old_price_idr' => 'Harga Lama (IDR)',
                        'new_stock' => 'Stok Baru',
                        'old_stock' => 'Stok Lama',
                        'reason' => 'Alasan Pengajuan',
                    ];
                ?>
                <?php $__empty_1 = true; $__currentLoopData = $approval->payload ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="py-2.5 font-heading text-[10px] uppercase tracking-wider text-silver-dim align-top w-1/3">
                            <?php echo e($fieldLabels[$key] ?? ucfirst(str_replace('_', ' ', $key))); ?>

                        </td>
                        <td class="py-2.5 text-silver-bright font-mono">
                            <?php if($value === null || $value === ''): ?>
                                -
                            <?php elseif($key === 'reason'): ?>
                                <span class="text-silver-bright italic">"<?php echo e($value); ?>"</span>
                            <?php else: ?>
                                <?php echo e($value); ?>

                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td class="py-2.5 text-silver-dim">Payload kosong.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php if($approval->type === 'discount' && isset($approval->payload['sales_order_id'])): ?>
            <?php $detailOrder = \App\Models\SalesOrder::with('invoice')->find($approval->payload['sales_order_id']); ?>
            <?php if($detailOrder?->invoice): ?>
                <a href="<?php echo e(route('admin.invoices.show', $detailOrder->invoice->id)); ?>" class="inline-block font-heading text-[10px] uppercase tracking-wider text-giallo hover:text-arancio">
                    Lihat Invoice Terkait &rarr;
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    
    <div class="panel border border-gunmetal bg-carbon p-6 space-y-4">
        <h2 class="font-heading text-xs uppercase tracking-wider text-silver-bright">Keputusan</h2>

        <?php if($approval->isPending()): ?>
            <?php if($approval->requested_by === auth()->id()): ?>
                <p class="text-xs text-danger flex items-center gap-2">
                    <span>🚫</span> Anda tidak dapat menyetujui pengajuan yang Anda buat sendiri.
                </p>
            <?php else: ?>
                <div class="space-y-4">
                    <form action="<?php echo e(route('admin.approvals.approve', $approval)); ?>" method="POST" class="space-y-3">
                        <?php echo csrf_field(); ?>
                        <div>
                            <label class="block font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1.5">Catatan Persetujuan (opsional)</label>
                            <textarea name="notes" rows="2" maxlength="500" placeholder="mis. sesuai SLA, dasar keputusan, dsb." class="w-full bg-nero border border-verde/40 px-3 py-2 text-xs text-silver-bright focus:border-verde focus:outline-none resize-none"></textarea>
                        </div>
                        <button type="submit" class="px-5 py-2.5 bg-verde hover:bg-emerald-600 text-nero font-heading text-xs font-bold uppercase tracking-wider transition-premium">
                            ✓ Setujui Pengajuan
                        </button>
                    </form>

                    <form action="<?php echo e(route('admin.approvals.reject', $approval)); ?>" method="POST" class="space-y-3 pt-3 border-t border-gunmetal/40">
                        <?php echo csrf_field(); ?>
                        <div>
                            <label class="block font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1.5">Alasan Penolakan <span class="text-danger">(wajib)</span></label>
                            <textarea name="notes" rows="2" required maxlength="500" placeholder="Tuliskan dasar penolakan agar pengaju memahami." class="w-full bg-nero border border-danger/40 px-3 py-2 text-xs text-silver-bright focus:border-danger focus:outline-none resize-none"></textarea>
                        </div>
                        <button type="submit" class="px-5 py-2.5 bg-danger hover:bg-red-600 text-white font-heading text-xs font-bold uppercase tracking-wider transition-premium">
                            ✕ Tolak Pengajuan
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <?php if($approval->reason): ?>
                <p class="text-xs text-silver-dim italic">Catatan keputusan: "<?php echo e($approval->reason); ?>"</p>
            <?php endif; ?>
            <p class="text-xs text-silver-dim">Pengajuan ini telah diputuskan dan tidak dapat diubah. Pembuat pengajuan perlu mengajukan baru bila diperlukan.</p>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/admin/approvals/show.blade.php ENDPATH**/ ?>