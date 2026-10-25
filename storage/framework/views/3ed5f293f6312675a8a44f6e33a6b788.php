
<div class="fixed top-0 left-0 h-full w-72 bg-carbon border-r border-gunmetal z-50 hidden lg:block">
    
    <div class="p-6 border-b border-gunmetal">
        <div class="flex items-center gap-3">
            <img src="<?php echo e(\App\Services\SettingService::getLogo()); ?>" alt="<?php echo e(\App\Services\SettingService::getName()); ?> Logo" class="w-10 h-10 object-contain drop-shadow-[0_0_8px_rgba(245,196,0,0.3)]" />
            <div>
                <h1 class="font-display text-giallo text-lg tracking-wider font-bold"><?php echo e(\App\Services\SettingService::getName()); ?></h1>
                <p class="font-mono text-[10px] text-silver-dim tracking-widest uppercase"><?php echo e(\App\Services\SettingService::getTagline()); ?></p>
            </div>
        </div>
    </div>

    
    <nav class="p-4 space-y-1 overflow-y-auto h-[calc(100%-180px)]">
        <p class="font-heading text-[10px] uppercase tracking-[0.2em] text-silver-dim px-3 mb-3">Menu Utama</p>

        <a href="<?php echo e(route('home')); ?>" class="sidebar-item <?php echo e(request()->routeIs('home') ? 'active' : ''); ?>">
            <span>⬡</span>
            <span class="font-heading text-sm font-medium tracking-wide">Dashboard</span>
        </a>

        <a href="<?php echo e(route('marketplace.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('marketplace.*') ? 'active' : ''); ?>">
            <span>🏎</span>
            <span class="font-heading text-sm font-medium tracking-wide">Marketplace</span>
        </a>

        <a href="<?php echo e(route('workshop.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('workshop.*') ? 'active' : ''); ?>">
            <span>🔧</span>
            <span class="font-heading text-sm font-medium tracking-wide">Bengkel</span>
        </a>

        <a href="<?php echo e(route('parts.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('parts.*') ? 'active' : ''); ?>">
            <span>⚙️</span>
            <span class="font-heading text-sm font-medium tracking-wide">Spare Part</span>
        </a>

        <p class="font-heading text-[10px] uppercase tracking-[0.2em] text-silver-dim px-3 mt-6 mb-3">Akun</p>

        <?php if(auth()->guard()->check()): ?>
            <?php if(auth()->user()->role === 'customer'): ?>
                <a href="<?php echo e(route('customer.dashboard')); ?>" class="sidebar-item <?php echo e(request()->routeIs('customer.*') ? 'active' : ''); ?>">
                    <span>👤</span>
                    <span class="font-heading text-sm font-medium tracking-wide">Portal Saya</span>
                </a>
            <?php elseif(auth()->user()->role === 'mechanic'): ?>
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="sidebar-item <?php echo e(request()->routeIs('admin.*') ? 'active' : ''); ?>">
                    <span>🔧</span>
                    <span class="font-heading text-sm font-medium tracking-wide">Panel Bengkel</span>
                </a>
            <?php else: ?>
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="sidebar-item <?php echo e(request()->routeIs('admin.*') ? 'active' : ''); ?>">
                    <span>⚡</span>
                    <span class="font-heading text-sm font-medium tracking-wide">Admin Panel</span>
                </a>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="sidebar-item w-full">
                    <span>⏻</span>
                    <span class="font-heading text-sm font-medium tracking-wide">Logout</span>
                </button>
            </form>
        <?php else: ?>
            <a href="<?php echo e(route('login')); ?>" class="sidebar-item">
                <span>→</span>
                <span class="font-heading text-sm font-medium tracking-wide">Login</span>
            </a>
        <?php endif; ?>
    </nav>

    
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gunmetal">
        <div class="bg-steel/30 rounded-sm p-3 border border-gunmetal/50">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-2 h-2 rounded-full bg-verde animate-pulse-glow"></div>
                <span class="font-mono text-[10px] text-verde uppercase tracking-wider">System Active</span>
            </div>
            <p class="font-mono text-[10px] text-silver-dim">v<?php echo e(config('app.version', '1.0.0')); ?></p>
        </div>
    </div>
</div><?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/components/layout/sidebar.blade.php ENDPATH**/ ?>