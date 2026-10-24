


<?php $__env->startSection('title', 'Riwayat AI Scan'); ?>


<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">
                Riwayat AI Scan
            </h1>
            <p class="text-sm text-silver-dim mt-2">
                Daftar semua analisis suara mesin yang telah Anda lakukan
            </p>
        </div>
        <a href="<?php echo e(route('customer.ai-scan.create')); ?>" class="px-4 py-2 bg-giallo text-nero font-heading font-bold text-xs uppercase tracking-wider hover:bg-arancio transition-premium clip-angular-button">
            + Scan Baru
        </a>
    </div>


    
    <?php if($scans->count() > 0): ?>
        <div class="panel border border-gunmetal overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gunmetal bg-steel/20">
                            <th class="text-left px-4 py-3 font-heading text-[10px] uppercase tracking-wider text-silver-dim">Tanggal</th>
                            <th class="text-left px-4 py-3 font-heading text-[10px] uppercase tracking-wider text-silver-dim">Kendaraan</th>
                            <th class="text-left px-4 py-3 font-heading text-[10px] uppercase tracking-wider text-silver-dim">Prediksi</th>
                            <th class="text-center px-4 py-3 font-heading text-[10px] uppercase tracking-wider text-silver-dim">Confidence</th>
                            <th class="text-center px-4 py-3 font-heading text-[10px] uppercase tracking-wider text-silver-dim">Status</th>
                            <th class="text-center px-4 py-3 font-heading text-[10px] uppercase tracking-wider text-silver-dim">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gunmetal/30">
                        <?php $__currentLoopData = $scans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-steel/20 transition-premium">
                                <td class="px-4 py-3">
                                    <p class="font-mono text-xs text-silver-bright">
                                        <?php echo e($scan->created_at->format('d M Y')); ?>

                                    </p>
                                    <p class="font-mono text-[10px] text-silver-dim">
                                        <?php echo e($scan->created_at->format('H:i')); ?>

                                    </p>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if($scan->vehicle): ?>
                                        <p class="font-heading text-xs text-silver-bright">
                                            <?php echo e($scan->vehicle->brand); ?> <?php echo e($scan->vehicle->model); ?>

                                        </p>
                                        <p class="font-mono text-[10px] text-silver-dim">
                                            <?php echo e($scan->vehicle->plate_number); ?>

                                        </p>
                                    <?php else: ?>
                                        <p class="font-heading text-xs text-silver-dim">-</p>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if($scan->predicted_class): ?>
                                        <p class="font-heading text-xs text-silver-bright">
                                            <?php echo e(str_replace('_', ' ', ucfirst($scan->predicted_class))); ?>

                                        </p>
                                    <?php else: ?>
                                        <p class="font-heading text-xs text-silver-dim">-</p>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <?php if($scan->confidence): ?>
                                        <span class="font-mono text-xs <?php echo e($scan->confidence >= 0.8 ? 'text-verde' : ($scan->confidence >= 0.6 ? 'text-giallo' : 'text-danger')); ?>">
                                            <?php echo e(round($scan->confidence * 100)); ?>%
                                        </span>
                                    <?php else: ?>
                                        <span class="font-mono text-xs text-silver-dim">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-0.5 text-[9px] font-heading uppercase tracking-wider border <?php echo e($scan->getStatusColor()); ?> bg-steel/30">
                                        <?php echo e($scan->getStatusLabel()); ?>

                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="<?php echo e(route('customer.ai-scan.show', $scan)); ?>" class="font-heading text-[10px] text-giallo hover:text-arancio uppercase tracking-wider transition-premium">
                                        Detail →
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>


        
        <?php echo e($scans->links()); ?>

    <?php else: ?>
        <div class="panel border border-gunmetal p-12 text-center">
            <span class="text-5xl text-gunmetal block mb-4">🔊</span>
            <h3 class="font-heading text-sm font-semibold text-silver-bright uppercase tracking-wider mb-2">
                Belum Ada Scan
            </h3>
            <p class="font-body text-xs text-silver-dim mb-4">
                Anda belum melakukan analisis suara mesin
            </p>
            <a href="<?php echo e(route('customer.ai-scan.create')); ?>" class="inline-block px-5 py-2 bg-giallo text-nero font-heading font-bold text-xs uppercase tracking-wider hover:bg-arancio transition-premium clip-angular-button">
                Mulai Scan Pertama
            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('layouts.customer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/customer/ai-scan/index.blade.php ENDPATH**/ ?>