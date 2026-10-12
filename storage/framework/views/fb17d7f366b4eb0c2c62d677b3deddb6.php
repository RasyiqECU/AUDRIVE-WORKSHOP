
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'type' => 'info', // success, warning, danger, info
    'message' => null,
    'dismissible' => true,
]));

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

foreach (array_filter(([
    'type' => 'info', // success, warning, danger, info
    'message' => null,
    'dismissible' => true,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>


<?php
    $types = [
        'success' => ['border' => 'border-verde/30', 'bg' => 'bg-verde/5', 'text' => 'text-verde', 'icon' => '✓'],
        'warning' => ['border' => 'border-arancio/30', 'bg' => 'bg-arancio/5', 'text' => 'text-arancio', 'icon' => '⚠'],
        'danger'  => ['border' => 'border-danger/30', 'bg' => 'bg-danger/5', 'text' => 'text-danger', 'icon' => '✕'],
        'info'    => ['border' => 'border-giallo/30', 'bg' => 'bg-giallo/5', 'text' => 'text-giallo', 'icon' => 'ℹ'],
    ];
    $style = $types[$type] ?? $types['info'];
?>


<div x-data="{ show: true }" x-show="show" x-transition
     <?php echo e($attributes->merge(['class' => "border {$style['border']} {$style['bg']} p-4 flex items-start gap-3"])); ?>>
    <span class="text-lg shrink-0 <?php echo e($style['text']); ?>"><?php echo e($style['icon']); ?></span>
    <div class="flex-1 text-sm <?php echo e($style['text']); ?>">
        <?php if($message): ?>
            <?php echo e($message); ?>

        <?php else: ?>
            <?php echo e($slot); ?>

        <?php endif; ?>
    </div>
    <?php if($dismissible): ?>
        <button @click="show = false" class="shrink-0 opacity-60 hover:opacity-100 transition-premium <?php echo e($style['text']); ?>">
            ✕
        </button>
    <?php endif; ?>
</div>



<?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/components/ui/alert.blade.php ENDPATH**/ ?>