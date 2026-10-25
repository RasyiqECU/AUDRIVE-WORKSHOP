<?php $__env->startSection('title', 'Daftar Penawaran Harga Mobil — AUDrive'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="font-display text-2xl font-bold text-silver-bright">PENAWARAN HARGA SAYA</h1>
        <p class="text-xs text-silver-dim mt-1">Daftar penawaran harga mobil yang Anda ajukan ke showroom AUDrive.</p>
    </div>

    <?php if($offers->isEmpty()): ?>
        <div class="bg-carbon border border-gunmetal rounded-lg p-12 text-center">
            <?php if (isset($component)) { $__componentOriginal3607a477fdef7402bc742abad5df9c51 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3607a477fdef7402bc742abad5df9c51 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.empty-state','data' => ['icon' => '🏷️','title' => 'Belum Ada Penawaran','description' => 'Anda belum mengajukan penawaran harga untuk unit mobil manapun.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => '🏷️','title' => 'Belum Ada Penawaran','description' => 'Anda belum mengajukan penawaran harga untuk unit mobil manapun.']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3607a477fdef7402bc742abad5df9c51)): ?>
<?php $attributes = $__attributesOriginal3607a477fdef7402bc742abad5df9c51; ?>
<?php unset($__attributesOriginal3607a477fdef7402bc742abad5df9c51); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3607a477fdef7402bc742abad5df9c51)): ?>
<?php $component = $__componentOriginal3607a477fdef7402bc742abad5df9c51; ?>
<?php unset($__componentOriginal3607a477fdef7402bc742abad5df9c51); ?>
<?php endif; ?>
            <div class="mt-4">
                <a href="<?php echo e(route('marketplace.index')); ?>" class="inline-block px-5 py-2.5 bg-giallo text-nero font-heading font-semibold text-xs uppercase tracking-wider rounded">
                    Jelajahi Mobil
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php $__currentLoopData = $offers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $offer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-carbon border border-gunmetal rounded-lg p-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h4 class="font-heading font-bold text-silver-bright text-sm"><?php echo e($offer->carUnit?->full_name); ?></h4>
                        <p class="text-xs text-silver-dim mt-0.5">
                            Penawaran Anda: <span class="text-giallo font-semibold">Rp <?php echo e(number_format($offer->amount_idr, 0, ',', '.')); ?></span>
                        </p>
                        <p class="text-[11px] text-silver-dim font-mono mt-0.5">
                            Status: <span class="uppercase font-semibold text-silver-bright"><?php echo e($offer->status->value ?? $offer->status); ?></span>
                            • Diajukan pada: <?php echo e($offer->created_at->format('d M Y')); ?>

                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <?php if($offer->carUnit): ?>
                            <a href="<?php echo e(route('marketplace.show', $offer->carUnit->slug)); ?>" class="px-3 py-1.5 border border-gunmetal text-xs text-silver-bright rounded hover:border-giallo transition">
                                Lihat Unit
                            </a>
                        <?php endif; ?>
                        <?php if(($offer->status->value ?? $offer->status) === 'pending'): ?>
                            <form action="<?php echo e(route('marketplace.offer.destroy', $offer)); ?>" method="POST" onsubmit="return confirm('Batalkan penawaran ini?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="px-3 py-1.5 border border-red-500/30 text-xs text-red-400 rounded hover:bg-red-500/10 transition">
                                    Batal
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <div class="mt-4">
                <?php echo e($offers->links()); ?>

            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/marketplace/offers.blade.php ENDPATH**/ ?>