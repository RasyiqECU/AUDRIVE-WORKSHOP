<?php $__env->startSection('title', 'Detail Promo ' . $promotion->name); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <a href="<?php echo e(route('admin.promotions.index')); ?>" class="text-silver-dim hover:text-giallo font-heading uppercase tracking-wider">Promo</a>
    <span class="text-silver-dim">/</span>
    <span class="text-giallo font-heading uppercase tracking-wider"><?php echo e($promotion->name); ?></span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="<?php echo e(route('admin.promotions.index')); ?>" class="text-xs text-silver-dim hover:text-giallo inline-flex items-center gap-1 mb-2 transition-colors">
            ← Kembali ke Daftar Promo
        </a>
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <h1 class="font-display text-2xl text-silver-bright font-bold tracking-wide">
                DETAIL PROMO: <?php echo e($promotion->name); ?>

            </h1>
            <span class="px-3 py-1 rounded text-[10px] font-heading font-bold uppercase tracking-wider border <?php echo e($promotion->is_active ? 'bg-verde/15 border-verde/40 text-verde' : 'bg-steel/40 border-gunmetal text-silver-dim'); ?>">
                <?php echo e($promotion->is_active ? 'Aktif' : 'Nonaktif'); ?>

            </span>
        </div>
    </div>

    <div class="panel border border-gunmetal/70 bg-carbon p-6 space-y-4">
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div>
                <dt class="text-silver-dim font-heading uppercase tracking-wider">Nama Kampanye</dt>
                <dd class="text-silver-bright font-bold mt-1"><?php echo e($promotion->name); ?></dd>
            </div>
            <div>
                <dt class="text-silver-dim font-heading uppercase tracking-wider">Tipe Potongan</dt>
                <dd class="text-silver-bright mt-1"><?php echo e(($promotion->type->value ?? $promotion->type) === 'fixed' ? 'Nominal Tetap' : 'Persentase'); ?></dd>
            </div>
            <div>
                <dt class="text-silver-dim font-heading uppercase tracking-wider">Nilai Potongan</dt>
                <dd class="text-giallo font-bold mt-1">
                    <?php echo e(($promotion->type->value ?? $promotion->type) === 'fixed' ? 'Rp ' . number_format($promotion->value, 0, ',', '.') : $promotion->value . '%'); ?>

                </dd>
            </div>
            <div>
                <dt class="text-silver-dim font-heading uppercase tracking-wider">Masa Berlaku</dt>
                <dd class="text-silver-bright mt-1">
                    <?php echo e($promotion->starts_at ? $promotion->starts_at->translatedFormat('d M Y') : '—'); ?> s/d <?php echo e($promotion->ends_at ? $promotion->ends_at->translatedFormat('d M Y') : '—'); ?>

                </dd>
            </div>
            <div>
                <dt class="text-silver-dim font-heading uppercase tracking-wider">Berlaku Untuk</dt>
                <dd class="text-silver-bright mt-1"><?php echo e($promotion->applicable_to ?? 'Semua Layanan'); ?></dd>
            </div>
        </dl>

        <?php if($promotion->description): ?>
            <div class="border-t border-gunmetal/60 pt-4">
                <dt class="text-silver-dim font-heading uppercase tracking-wider text-xs">Deskripsi</dt>
                <dd class="text-silver-dim text-xs mt-1 leading-relaxed whitespace-pre-line"><?php echo e($promotion->description); ?></dd>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/admin/promotions/show.blade.php ENDPATH**/ ?>