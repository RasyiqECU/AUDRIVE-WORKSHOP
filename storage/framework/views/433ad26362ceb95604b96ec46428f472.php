<?php $__env->startSection('title', 'Lacak Pengerjaan Servis #' . $workOrder->code . ' — AUDrive'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="mb-6">
        <a href="<?php echo e(route('workshop.booking.index')); ?>" class="text-xs text-giallo hover:underline uppercase tracking-wider font-heading">
            &larr; Kembali ke Daftar Booking
        </a>
        <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright mt-2">
            PELACAKAN SERVIS: <?php echo e($workOrder->code); ?>

        </h1>
        <p class="text-xs text-silver-dim mt-1">Status pengerjaan realtime unit kendaraan Anda di bengkel AUDrive.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            
            <div class="bg-nero-dark border border-white/10 rounded-lg p-6">
                <div class="flex items-center justify-between mb-6 pb-3 border-b border-white/5">
                    <span class="text-xs uppercase tracking-wider text-silver-dim font-heading">Status Terkini</span>
                    <span class="px-3 py-1 text-xs font-semibold rounded uppercase tracking-wider bg-giallo/20 text-giallo border border-giallo/40">
                        <?php echo e($workOrder->status->value ?? $workOrder->status); ?>

                    </span>
                </div>

                
                <?php
                    $statuses = ['booking', 'inspection', 'in_progress', 'waiting_parts', 'quality_control', 'completed'];
                    $currentIdx = array_search($workOrder->status->value ?? $workOrder->status, $statuses);
                    if ($currentIdx === false) $currentIdx = 1;
                ?>
                <div class="space-y-4">
                    <?php $__currentLoopData = [
                        ['id' => 'booking', 'title' => 'Booking Diterima', 'desc' => 'Kendaraan terdaftar dalam antrean pengerjaan.'],
                        ['id' => 'inspection', 'title' => 'Inspeksi Awal', 'desc' => 'Pengecekan teknis oleh tim kepala mekanik.'],
                        ['id' => 'in_progress', 'title' => 'Dalam Pengerjaan', 'desc' => 'Mekanik sedang melakukan tindakan servis/perbaikan.'],
                        ['id' => 'quality_control', 'title' => 'Quality Control & Test Drive', 'desc' => 'Pengujian performa akhir sebelum serah terima.'],
                        ['id' => 'completed', 'title' => 'Selesai & Siap Diambil', 'desc' => 'Unit bersih dan siap diserahkan kepada pemilik.'],
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $isDone = $idx <= $currentIdx;
                            $isActive = $idx === $currentIdx;
                        ?>
                        <div class="flex items-start gap-4">
                            <div class="flex flex-col items-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold <?php echo e($isActive ? 'bg-giallo text-nero ring-4 ring-giallo/20' : ($isDone ? 'bg-giallo/40 text-giallo' : 'bg-nero border border-white/20 text-silver-dim')); ?>">
                                    <?php echo e($idx + 1); ?>

                                </div>
                                <?php if(!$loop->last): ?>
                                    <div class="w-0.5 h-10 <?php echo e($isDone ? 'bg-giallo/40' : 'bg-white/10'); ?>"></div>
                                <?php endif; ?>
                            </div>
                            <div class="pt-1">
                                <h4 class="font-heading text-sm font-semibold <?php echo e($isActive ? 'text-giallo' : ($isDone ? 'text-silver-bright' : 'text-silver-dim')); ?>">
                                    <?php echo e($step['title']); ?>

                                </h4>
                                <p class="text-xs text-silver-dim mt-0.5 leading-relaxed"><?php echo e($step['desc']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            
            <?php if($workOrder->items && $workOrder->items->isNotEmpty()): ?>
                <div class="bg-nero-dark border border-white/10 rounded-lg p-6">
                    <h3 class="font-heading text-sm font-bold text-silver-bright uppercase tracking-wider mb-4 border-b border-white/5 pb-2">
                        Rincian Pekerjaan & Suku Cadang
                    </h3>
                    <div class="divide-y divide-white/5">
                        <?php $__currentLoopData = $workOrder->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="py-2.5 flex justify-between items-center text-xs">
                                <div>
                                    <span class="text-silver-bright font-medium"><?php echo e($item->item_name); ?></span>
                                    <span class="text-silver-dim block text-[11px]"><?php echo e($item->item_type); ?> (x<?php echo e($item->quantity); ?>)</span>
                                </div>
                                <span class="text-silver-bright font-mono">
                                    Rp <?php echo e(number_format($item->subtotal_idr ?? ($item->price_idr * $item->quantity), 0, ',', '.')); ?>

                                </span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        
        <div class="space-y-6">
            <div class="bg-nero-dark border border-white/10 rounded-lg p-6">
                <h3 class="font-heading text-sm font-bold text-silver-bright uppercase tracking-wider mb-4 border-b border-white/5 pb-2">
                    Unit Kendaraan
                </h3>
                <div class="text-xs space-y-2">
                    <div>
                        <span class="text-silver-dim block">Nomor Polisi</span>
                        <span class="text-silver-bright font-bold font-mono text-sm"><?php echo e($workOrder->vehicle?->license_plate ?? 'N/A'); ?></span>
                    </div>
                    <div>
                        <span class="text-silver-dim block">Model Kendaraan</span>
                        <span class="text-silver-bright font-semibold"><?php echo e($workOrder->vehicle?->brand); ?> <?php echo e($workOrder->vehicle?->model); ?></span>
                    </div>
                    <?php if($workOrder->mechanic): ?>
                        <div>
                            <span class="text-silver-dim block">Mekanik Bertugas</span>
                            <span class="text-silver-bright"><?php echo e($workOrder->mechanic->name); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make(auth()->check() && auth()->user()->role === 'customer' ? 'layouts.customer' : (auth()->check() ? 'layouts.app' : 'layouts.public'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/workshop/tracking.blade.php ENDPATH**/ ?>