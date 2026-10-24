
<header class="topbar flex items-center justify-between">
    
    <button class="lg:hidden text-giallo hover:text-arancio transition-premium">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    
    <div class="hidden lg:flex items-center gap-3">
        <div class="h-px w-8 bg-giallo"></div>
        <h2 class="font-heading text-sm uppercase tracking-[0.15em] text-silver-bright">
            <?php echo $__env->yieldContent('title', 'Dashboard'); ?>
        </h2>
    </div>

    
    <div class="flex items-center gap-3">
        
        <?php
            $custPoints = auth()->check() ? (auth()->user()->customer?->loyalty_points ?? 0) : 0;
        ?>
        <a href="<?php echo e(route('customer.loyalty.index')); ?>" class="hidden md:flex items-center gap-2 px-3 py-1 bg-steel/30 border border-gunmetal/50 hover:border-giallo/50 transition-premium" title="Poin Reward Saya">
            <span class="text-giallo text-sm">⭐</span>
            <span class="font-mono text-xs text-giallo font-bold"><?php echo e(number_format($custPoints)); ?></span>
            <span class="font-heading text-[9px] text-silver-dim uppercase">Points</span>
        </a>

        
        <?php
            $custUnreadCount = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0;
        ?>
        <a href="<?php echo e(route('customer.notifications.index')); ?>" class="relative text-silver-dim hover:text-giallo transition-premium" title="Notifikasi Saya">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <?php if($custUnreadCount > 0): ?>
                <span class="absolute -top-1 -right-1 min-w-[14px] h-3.5 px-1 bg-red-600 rounded-full text-[8px] flex items-center justify-center text-white font-bold leading-none animate-pulse">
                    <?php echo e($custUnreadCount > 9 ? '9+' : $custUnreadCount); ?>

                </span>
            <?php endif; ?>
        </a>

        
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 bg-steel border border-gunmetal clip-hexagon flex items-center justify-center">
                <span class="font-heading text-[10px] text-giallo font-bold">
                    <?php echo e(substr(auth()->user()->name ?? 'C', 0, 1)); ?>

                </span>
            </div>
            <span class="hidden md:inline font-heading text-xs text-silver-bright">
                <?php echo e(auth()->user()->name ?? 'Customer'); ?>

            </span>
        </div>
    </div>
</header><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/components/layout/customer-topbar.blade.php ENDPATH**/ ?>