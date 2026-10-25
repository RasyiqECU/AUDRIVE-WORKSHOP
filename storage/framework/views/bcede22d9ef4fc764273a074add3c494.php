
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['car']));

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

foreach (array_filter((['car']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>


<div <?php echo e($attributes->merge(['class' => 'bg-carbon border border-gunmetal overflow-hidden dashboard-card group'])); ?>>
    
    <div class="relative aspect-[16/10] bg-steel/30 overflow-hidden">
        <?php if($car->primaryPhoto): ?>
            <img src="<?php echo e(asset('storage/' . $car->primaryPhoto->path)); ?>" alt="<?php echo e($car->full_name); ?>"
                 class="w-full h-full object-cover group-hover:scale-105 transition-premium duration-500" />
        <?php else: ?>
            <div class="w-full h-full flex items-center justify-center text-gunmetal">
                <span class="text-5xl">🏎</span>
            </div>
        <?php endif; ?>


        
        <div class="absolute top-3 left-3">
            <?php if (isset($component)) { $__componentOriginald271113ca69d52f8dbc84ae4f8468e44 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald271113ca69d52f8dbc84ae4f8468e44 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.status-pill','data' => ['status' => $car->status->value]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.status-pill'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($car->status->value)]); ?>
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


        
        <?php if($car->featured_at): ?>
            <div class="absolute top-3 right-3">
                <span class="bg-giallo text-nero font-heading text-[9px] font-bold uppercase tracking-wider px-2 py-0.5">★ Featured</span>
            </div>
        <?php endif; ?>
    </div>


    
    <div class="p-4">
        <div class="flex items-start justify-between mb-2">
            <div>
                <h3 class="font-heading text-sm font-semibold text-silver-bright group-hover:text-giallo transition-premium">
                    <?php echo e($car->full_name); ?>

                </h3>
                <p class="font-mono text-[10px] text-silver-dim"><?php echo e($car->code); ?></p>
            </div>
        </div>


        
        <div class="grid grid-cols-3 gap-2 mb-3">
            <div class="text-center bg-steel/20 py-1.5 border border-gunmetal/30">
                <p class="font-mono text-[10px] text-silver-dim">KM</p>
                <p class="font-heading text-xs text-silver-bright"><?php echo e(number_format($car->mileage_km, 0, ',', '.')); ?></p>
            </div>
            <div class="text-center bg-steel/20 py-1.5 border border-gunmetal/30">
                <p class="font-mono text-[10px] text-silver-dim">TRANS</p>
                <p class="font-heading text-xs text-silver-bright uppercase"><?php echo e($car->transmission); ?></p>
            </div>
            <div class="text-center bg-steel/20 py-1.5 border border-gunmetal/30">
                <p class="font-mono text-[10px] text-silver-dim">YEAR</p>
                <p class="font-heading text-xs text-silver-bright"><?php echo e($car->year); ?></p>
            </div>
        </div>


        
        <div class="flex items-end justify-between pt-3 border-t border-gunmetal/30">
            <?php if (isset($component)) { $__componentOriginalabe319c0c4704fdfb8485f67bb8f6386 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalabe319c0c4704fdfb8485f67bb8f6386 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.price','data' => ['amount' => $car->price_sell_idr,'size' => 'md']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.price'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['amount' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($car->price_sell_idr),'size' => 'md']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalabe319c0c4704fdfb8485f67bb8f6386)): ?>
<?php $attributes = $__attributesOriginalabe319c0c4704fdfb8485f67bb8f6386; ?>
<?php unset($__attributesOriginalabe319c0c4704fdfb8485f67bb8f6386); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalabe319c0c4704fdfb8485f67bb8f6386)): ?>
<?php $component = $__componentOriginalabe319c0c4704fdfb8485f67bb8f6386; ?>
<?php unset($__componentOriginalabe319c0c4704fdfb8485f67bb8f6386); ?>
<?php endif; ?>
            <a href="<?php echo e(route('marketplace.show', $car->slug)); ?>" class="font-heading text-[10px] text-giallo hover:text-arancio uppercase tracking-wider transition-premium">
                Detail →
            </a>
        </div>
    </div>
</div>



<?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/components/marketplace/car-card.blade.php ENDPATH**/ ?>