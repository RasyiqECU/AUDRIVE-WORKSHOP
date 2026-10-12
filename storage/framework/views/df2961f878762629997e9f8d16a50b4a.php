<?php $__env->startSection('title', 'Notifikasi'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-4xl mx-auto">
    
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Pemberitahuan</p>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                NOTIFIKASI SAYA
            </h1>
            <p class="text-xs text-silver-dim mt-1">
                Riwayat pemberitahuan booking servis, status pesanan, promo, dan pengingat
            </p>
        </div>

        <?php if($notifications->count() > 0): ?>
            <form action="<?php echo e(route('customer.notifications.read-all')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" class="px-4 py-2 bg-steel/40 hover:bg-steel border border-gunmetal text-silver-bright font-heading text-xs uppercase tracking-wider rounded transition-premium">
                    ✓ Tandai Semua Sudah Dibaca
                </button>
            </form>
        <?php endif; ?>
    </div>

    
    <div class="panel border border-gunmetal bg-carbon overflow-hidden">
        <div class="divide-y divide-gunmetal/40">
            <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="p-4 md:p-5 flex items-start justify-between gap-4 <?php echo e($notification->read_at ? 'bg-carbon' : 'bg-steel/20 border-l-2 border-l-giallo'); ?> hover:bg-steel/30 transition-premium">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full <?php echo e($notification->read_at ? 'bg-steel/40 text-silver-dim' : 'bg-giallo/20 text-giallo'); ?> flex items-center justify-center shrink-0 mt-0.5 text-sm">
                            🔔
                        </div>
                        <div>
                            <p class="text-xs text-silver-bright font-semibold leading-relaxed">
                                <?php echo e($notification->data['message'] ?? $notification->data['title'] ?? 'Notifikasi baru'); ?>

                            </p>
                            <?php if(isset($notification->data['detail'])): ?>
                                <p class="text-xs text-silver-dim mt-1">
                                    <?php echo e($notification->data['detail']); ?>

                                </p>
                            <?php endif; ?>
                            <p class="font-mono text-[10px] text-silver-dim mt-1.5 flex items-center gap-2">
                                <span><?php echo e($notification->created_at->diffForHumans()); ?></span>
                                <span>&bull;</span>
                                <span><?php echo e($notification->created_at->format('d M Y H:i')); ?></span>
                            </p>
                        </div>
                    </div>

                    <?php if(!$notification->read_at): ?>
                        <form action="<?php echo e(route('customer.notifications.read', $notification->id)); ?>" method="POST" class="shrink-0">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>
                            <button type="submit" class="px-2.5 py-1 text-[10px] font-heading uppercase tracking-wider text-giallo hover:text-arancio hover:underline">
                                Tandai Dibaca
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="p-12 text-center text-silver-dim">
                    <span class="text-4xl block mb-2 opacity-50">📬</span>
                    <p class="font-display font-bold text-silver-bright text-sm tracking-wide">Belum Ada Notifikasi</p>
                    <p class="text-xs text-silver-dim mt-1">Notifikasi booking, pesanan, dan aktivitas Anda akan muncul di sini.</p>
                </div>
            <?php endif; ?>
        </div>

        <?php if($notifications->hasPages()): ?>
            <div class="p-4 border-t border-gunmetal bg-steel/20">
                <?php echo e($notifications->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.customer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/customer/notifications/index.blade.php ENDPATH**/ ?>