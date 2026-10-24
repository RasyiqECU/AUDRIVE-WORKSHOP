
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['status' => '']));

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

foreach (array_filter((['status' => '']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>


<?php
    $statuses = [
        'available' => ['label' => 'Tersedia', 'class' => 'border-verde/40 text-verde bg-verde/5'],
        'reserved' => ['label' => 'Dipesan', 'class' => 'border-arancio/40 text-arancio bg-arancio/5'],
        'sold' => ['label' => 'Terjual', 'class' => 'border-giallo/40 text-giallo bg-giallo/5'],
        'draft' => ['label' => 'Draft', 'class' => 'border-silver/20 text-silver-dim bg-steel/30'],
        'pending' => ['label' => 'Menunggu', 'class' => 'border-giallo/40 text-giallo bg-giallo/5'],
        'in_progress' => ['label' => 'Diproses', 'class' => 'border-arancio/40 text-arancio bg-arancio/5'],
        'quality_control' => ['label' => 'Quality Control', 'class' => 'border-arancio/40 text-arancio bg-arancio/5'],
        'completed' => ['label' => 'Selesai', 'class' => 'border-verde/40 text-verde bg-verde/5'],
        'delivered' => ['label' => 'Diserahkan', 'class' => 'border-verde/40 text-verde bg-verde/5'],
        'queued' => ['label' => 'Antrean', 'class' => 'border-silver/40 text-silver-bright bg-steel/30'],
        'cancelled' => ['label' => 'Dibatalkan', 'class' => 'border-danger/40 text-danger bg-danger/5'],
        'active' => ['label' => 'Aktif', 'class' => 'border-verde/40 text-verde bg-verde/5'],
        'inactive' => ['label' => 'Nonaktif', 'class' => 'border-silver/20 text-silver-dim bg-steel/30'],
    ];
    $style = $statuses[$status] ?? ['label' => ucfirst($status), 'class' => 'border-silver/20 text-silver-dim bg-steel/30'];
?>


<span <?php echo e($attributes->merge(['class' => "inline-flex items-center gap-1.5 px-3 py-1 border font-heading font-semibold text-[10px] uppercase tracking-wider {$style['class']}"])); ?>

      style="clip-path: polygon(0 0, calc(100% - 6px) 0, 100% 6px, 100% 100%, 6px 100%, 0 calc(100% - 6px));">
    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
    <?php echo e($style['label']); ?>

</span>



<?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/components/ui/status-pill.blade.php ENDPATH**/ ?>