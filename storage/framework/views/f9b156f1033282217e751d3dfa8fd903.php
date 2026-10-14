
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['part']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['part']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div <?php echo e($attributes->merge(['class' => 'bg-carbon border border-gunmetal overflow-hidden dashboard-card group flex flex-col justify-between hover:border-giallo/50 transition-premium rounded shadow-lg'])); ?>>
    <div>
        
        <a href="<?php echo e(route('parts.show', $part->slug)); ?>" class="block relative aspect-square bg-steel/30 overflow-hidden">
            <?php if($part->image): ?>
                <img src="<?php echo e(asset('storage/' . $part->image)); ?>" alt="<?php echo e($part->name); ?>"
                     class="w-full h-full object-cover group-hover:scale-105 transition-premium duration-500" />
            <?php else: ?>
                <div class="w-full h-full flex flex-col items-center justify-center text-gunmetal group-hover:text-giallo transition-premium">
                    <span class="text-4xl mb-1">⚙️</span>
                    <span class="font-mono text-[9px] text-silver-dim uppercase">AUDrive OEM</span>
                </div>
            <?php endif; ?>

            <?php if($part->isLowStock()): ?>
                <div class="absolute top-2 right-2">
                    <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['type' => 'warning']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'warning']); ?>Stok Terbatas <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                </div>
            <?php elseif($part->stock <= 0): ?>
                <div class="absolute top-2 right-2">
                    <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['type' => 'danger']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'danger']); ?>Habis <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if($part->category): ?>
                <div class="absolute bottom-2 left-2">
                    <span class="bg-nero/80 backdrop-blur-sm border border-gunmetal/60 px-2 py-0.5 text-[9px] font-mono text-silver uppercase rounded">
                        <?php echo e($part->category->name); ?>

                    </span>
                </div>
            <?php endif; ?>
        </a>

        
        <div class="p-4">
            <div class="flex items-center justify-between gap-2 mb-1">
                <p class="font-mono text-[10px] text-giallo/80 font-semibold"><?php echo e($part->sku); ?></p>
                <?php if($part->brand): ?>
                    <span class="font-heading text-[10px] uppercase text-silver-dim bg-steel/30 px-1.5 py-0.5 rounded border border-gunmetal/30"><?php echo e($part->brand); ?></span>
                <?php endif; ?>
            </div>

            <h3 class="font-heading text-sm font-semibold text-silver-bright group-hover:text-giallo transition-premium mb-2 line-clamp-2 min-h-[40px]">
                <a href="<?php echo e(route('parts.show', $part->slug)); ?>">
                    <?php echo e($part->name); ?>

                </a>
            </h3>

            <?php if($part->description): ?>
                <p class="font-body text-xs text-silver-dim line-clamp-2 mb-3">
                    <?php echo e(Str::limit($part->description, 70)); ?>

                </p>
            <?php endif; ?>
        </div>
    </div>

    <div class="p-4 pt-0">
        <div class="flex items-end justify-between pt-3 border-t border-gunmetal/30">
            <div>
                <span class="block font-mono text-[9px] text-silver-dim uppercase">Harga</span>
                <span class="font-mono text-sm font-bold text-silver-bright">
                    Rp <?php echo e(number_format($part->price_idr, 0, ',', '.')); ?>

                </span>
            </div>
            <div class="text-right">
                <span class="font-mono text-[10px] block <?php echo e($part->stock > 0 ? 'text-verde' : 'text-danger'); ?>">
                    <?php echo e($part->stock > 0 ? 'Stok: ' . $part->stock : 'Stok Kosong'); ?>

                </span>
                <a href="<?php echo e(route('parts.show', $part->slug)); ?>" class="inline-block mt-1 font-heading text-[10px] text-giallo hover:text-arancio uppercase tracking-wider font-semibold">
                    Detail →
                </a>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/components/parts/part-card.blade.php ENDPATH**/ ?>