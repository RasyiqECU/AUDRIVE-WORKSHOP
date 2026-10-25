<?php $__env->startSection('title', '403 - Akses Ditolak'); ?>


<?php $__env->startSection('content'); ?>
<div class="min-h-[60vh] flex items-center justify-center">
    <div class="text-center max-w-md">
        <div class="mb-8">
            <span class="font-display text-8xl font-bold text-danger">403</span>
        </div>
        <h1 class="font-display text-2xl text-silver-bright mb-4">Akses Ditolak</h1>
        <p class="font-body text-sm text-silver-dim mb-8">
            Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="<?php echo e(route('home')); ?>" class="px-6 py-3 bg-giallo text-nero font-heading font-bold text-xs uppercase tracking-wider hover:bg-arancio transition-premium clip-angular-button">
                Kembali ke Beranda
            </a>
            <?php if(auth()->guard()->check()): ?>
                <?php if(auth()->user()->role === 'customer'): ?>
                    <a href="<?php echo e(route('customer.dashboard')); ?>" class="px-6 py-3 border border-giallo text-giallo font-heading font-bold text-xs uppercase tracking-wider hover:bg-giallo hover:text-nero transition-premium clip-angular-button">
                        Dashboard Saya
                    </a>
                <?php else: ?>
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="px-6 py-3 border border-giallo text-giallo font-heading font-bold text-xs uppercase tracking-wider hover:bg-giallo hover:text-nero transition-premium clip-angular-button">
                        Dashboard Admin
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/errors/403.blade.php ENDPATH**/ ?>