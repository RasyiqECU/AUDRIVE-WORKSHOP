<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk #SO-<?php echo e($salesOrder->id); ?> - AUDrive</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 80mm;
            margin: 0 auto;
            padding: 10px;
            color: #000;
            font-size: 12px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .divider { border-top: 1px dashed #000; margin: 8px 0; }
        .flex { display: flex; justify-content: space-between; }
        @media print {
            body { width: 100%; margin: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="text-center">
        <h2 style="margin:0; font-size:16px;">AUDRIVE MOTORS</h2>
        <p style="margin:2px 0; font-size:10px;">Workshop & Luxury Auto Solutions</p>
        <p style="margin:2px 0; font-size:10px;"><?php echo e($salesOrder->created_at->format('d/m/Y H:i')); ?> | #<?php echo e($salesOrder->code); ?></p>
        <?php if($salesOrder->customer): ?>
            <p style="margin:2px 0; font-size:10px;">Customer: <?php echo e($salesOrder->customer->user?->name); ?></p>
        <?php endif; ?>
    </div>

    <div class="divider"></div>

    <?php $__currentLoopData = $salesOrder->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="flex">
            <span><?php echo e($item->description ?: ($item->sparePart?->name ?? 'Item')); ?></span>
            <span>Rp <?php echo e(number_format($item->subtotal_idr, 0, ',', '.')); ?></span>
        </div>
        <div style="font-size:10px; color:#555;"><?php echo e($item->qty); ?> x Rp <?php echo e(number_format($item->unit_price_idr, 0, ',', '.')); ?></div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <div class="divider"></div>

    <?php if($salesOrder->discount_idr > 0): ?>
        <div class="flex" style="font-size:11px;">
            <span>Diskon:</span>
            <span>- Rp <?php echo e(number_format($salesOrder->discount_idr, 0, ',', '.')); ?></span>
        </div>
    <?php endif; ?>
    <?php if($salesOrder->tax_idr > 0): ?>
        <div class="flex" style="font-size:11px;">
            <span>PPN (11%):</span>
            <span>Rp <?php echo e(number_format($salesOrder->tax_idr, 0, ',', '.')); ?></span>
        </div>
    <?php endif; ?>

    <div class="flex" style="font-weight:bold; font-size:14px; margin-top:4px;">
        <span>TOTAL:</span>
        <span>Rp <?php echo e(number_format($salesOrder->total_idr, 0, ',', '.')); ?></span>
    </div>
    <div class="flex" style="font-size:11px; margin-top:4px;">
        <span>METODE:</span>
        <span style="text-transform:uppercase;"><?php echo e($salesOrder->payments?->first()?->method ?? 'CASH'); ?></span>
    </div>

    <div class="divider"></div>

    <div class="text-center" style="font-size:10px; margin-top:10px;">
        Terima Kasih Atas Kepercayaan Anda!
    </div>
</body>
</html>
<?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/admin/pos/print-receipt.blade.php ENDPATH**/ ?>