<?php $__env->startSection('title', 'Notifikasi Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-5xl mx-auto">
    
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="font-heading text-xs uppercase tracking-[0.2em] text-giallo">Pusat Informasi Sistem</p>
            <h1 class="font-display text-2xl md:text-3xl text-silver-bright font-bold tracking-wide mt-1">
                NOTIFIKASI MASUK (ADMIN)
            </h1>
            <p class="text-xs text-silver-dim mt-1">
                Pantau pesanan baru, booking servis masuk, persetujuan (approval), dan aktivitas sistem AUDrive secara real-time.
            </p>
        </div>
        <?php if($unreadCount > 0): ?>
            <form action="<?php echo e(route('admin.notifications.read-all')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright font-heading text-xs uppercase tracking-wider rounded transition-premium">
                    ✓ Tandai Semua Dibaca (<?php echo e($unreadCount); ?>)
                </button>
            </form>
        <?php endif; ?>
    </div>

    
    <div class="space-y-3">
        <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $data = $notification->data ?? [];
                $isUnread = is_null($notification->read_at);
            ?>
            <div class="panel border <?php echo e($isUnread ? 'border-giallo/50 bg-carbon/90 shadow-lg' : 'border-gunmetal/60 bg-carbon/50'); ?> p-5 transition-all flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 <?php echo e($isUnread ? 'bg-giallo/20 text-giallo' : 'bg-steel text-silver-dim'); ?>">
                        <?php if(($data['type'] ?? '') === 'service_booking'): ?>
                            🔧
                        <?php elseif(($data['type'] ?? '') === 'order' || ($data['type'] ?? '') === 'spare_part'): ?>
                            📦
                        <?php elseif(($data['type'] ?? '') === 'approval'): ?>
                            ⚖️
                        <?php elseif(($data['type'] ?? '') === 'offer'): ?>
                            🏎️
                        <?php else: ?>
                            🔔
                        <?php endif; ?>
                    </div>
                    <div class="space-y-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h4 class="font-heading text-sm text-silver-bright font-bold">
                                <?php echo e($data['title'] ?? $data['subject'] ?? 'Notifikasi Sistem AUDrive'); ?>

                            </h4>
                            <?php if($isUnread): ?>
                                <span class="px-2 py-0.5 text-[9px] font-heading uppercase tracking-wider bg-giallo text-carbon font-bold rounded">
                                    Baru
                                </span>
                            <?php endif; ?>
                        </div>
                        <p class="text-xs text-silver-muted leading-relaxed">
                            <?php echo e($data['message'] ?? $data['description'] ?? 'Terdapat aktivitas baru pada sistem.'); ?>

                        </p>
                        <p class="text-[10px] font-mono text-silver-dim pt-1">
                            <?php echo e($notification->created_at->diffForHumans()); ?> (<?php echo e($notification->created_at->format('d M Y, H:i')); ?>)
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 shrink-0 self-end md:self-center">
                    <?php if(isset($data['action_url'])): ?>
                        <a href="<?php echo e($data['action_url']); ?>" class="px-3 py-1.5 bg-giallo hover:bg-giallo-bright text-carbon text-xs font-heading uppercase font-bold rounded clip-angular-button transition-premium">
                            Lihat Detail →
                        </a>
                    <?php endif; ?>
                    <?php if($isUnread): ?>
                        <form action="<?php echo e(route('admin.notifications.read', $notification->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>
                            <button type="submit" title="Tandai telah dibaca" class="p-2 text-silver-dim hover:text-silver-bright text-xs">
                                ✓
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="panel border border-gunmetal/70 bg-carbon p-12 text-center text-silver-dim">
                <span class="text-4xl block mb-3 opacity-60">🔔</span>
                <p class="font-heading text-sm text-silver-bright uppercase tracking-wider">Belum Ada Notifikasi Masuk</p>
                <p class="text-xs mt-1">Notifikasi aktivitas booking, pesanan, dan persetujuan akan tampil di sini.</p>
            </div>
        <?php endif; ?>

        
        <?php if($notifications->hasPages()): ?>
            <div class="pt-4">
                <?php echo e($notifications->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/admin/notifications/index.blade.php ENDPATH**/ ?>