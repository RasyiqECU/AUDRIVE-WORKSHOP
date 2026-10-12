
<div class="fixed top-0 left-0 h-full w-72 bg-carbon border-r border-gunmetal z-50 hidden lg:block">
    
    <div class="p-5 border-b border-gunmetal">
        <a href="<?php echo e(route('customer.dashboard')); ?>" class="flex items-center gap-3 group">
            <img src="<?php echo e(asset('images/AUDrive-logo.png')); ?>" alt="AUDrive Logo" class="w-9 h-9 object-contain group-hover:scale-105 transition-transform duration-300 drop-shadow-[0_0_8px_rgba(245,196,0,0.3)]" />
            <div>
                <h1 class="font-display text-giallo text-sm tracking-wider font-bold">AUDRIVE</h1>
                <p class="font-mono text-[9px] text-silver-dim tracking-widest uppercase">Customer Portal</p>
            </div>
        </a>
    </div>

    
    <nav class="p-3 space-y-0.5 overflow-y-auto h-[calc(100%-160px)]">
        <p class="font-heading text-[9px] uppercase tracking-[0.2em] text-silver-dim px-3 mb-2 mt-1">Portal</p>

        <a href="<?php echo e(route('customer.dashboard')); ?>" class="sidebar-item <?php echo e(request()->routeIs('customer.dashboard') ? 'active' : ''); ?>">
            <span class="text-sm">⬡</span>
            <span class="font-heading text-xs font-medium tracking-wide">Dashboard</span>
        </a>

        <a href="<?php echo e(route('customer.vehicles.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('customer.vehicles.*') ? 'active' : ''); ?>">
            <span class="text-sm">🚗</span>
            <span class="font-heading text-xs font-medium tracking-wide">Kendaraan Saya</span>
        </a>

        <a href="<?php echo e(route('customer.reminders.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('customer.reminders.*') ? 'active' : ''); ?>">
            <span class="text-sm">🔔</span>
            <span class="font-heading text-xs font-medium tracking-wide">Reminder</span>
        </a>

        <a href="<?php echo e(route('customer.loyalty.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('customer.loyalty.*') ? 'active' : ''); ?>">
            <span class="text-sm">⭐</span>
            <span class="font-heading text-xs font-medium tracking-wide">Loyalty Point</span>
        </a>

        <p class="font-heading text-[9px] uppercase tracking-[0.2em] text-silver-dim px-3 mb-2 mt-4">Layanan</p>

        <a href="<?php echo e(route('customer.ai-scan.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('customer.ai-scan.*') ? 'active' : ''); ?>">
            <span class="text-sm">🔊</span>
            <span class="font-heading text-xs font-medium tracking-wide">AI Sound Scan</span>
        </a>

        <a href="<?php echo e(route('workshop.booking.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('workshop.*') ? 'active' : ''); ?>">
            <span class="text-sm">🔧</span>
            <span class="font-heading text-xs font-medium tracking-wide">Servis & Work Order</span>
        </a>

        <a href="<?php echo e(route('marketplace.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('marketplace.*') ? 'active' : ''); ?>">
            <span class="text-sm">🏎</span>
            <span class="font-heading text-xs font-medium tracking-wide">Marketplace</span>
        </a>

        <a href="<?php echo e(route('parts.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('parts.*') ? 'active' : ''); ?>">
            <span class="text-sm">⚙️</span>
            <span class="font-heading text-xs font-medium tracking-wide">Spare Part</span>
        </a>

        <p class="font-heading text-[9px] uppercase tracking-[0.2em] text-silver-dim px-3 mb-2 mt-4">Bantuan</p>

        <a href="<?php echo e(route('customer.tickets.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('customer.tickets.*') ? 'active' : ''); ?>">
            <span class="text-sm">🎫</span>
            <span class="font-heading text-xs font-medium tracking-wide">Tiket Support</span>
        </a>

        <a href="<?php echo e(route('customer.vouchers.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('customer.vouchers.*') ? 'active' : ''); ?>">
            <span class="text-sm">🎁</span>
            <span class="font-heading text-xs font-medium tracking-wide">Voucher & Promo</span>
        </a>

        <a href="<?php echo e(route('customer.notifications.index')); ?>" class="sidebar-item <?php echo e(request()->routeIs('customer.notifications.*') ? 'active' : ''); ?>">
            <span class="text-sm">📬</span>
            <span class="font-heading text-xs font-medium tracking-wide">Notifikasi</span>
        </a>
    </nav>

    
    <div class="absolute bottom-0 left-0 right-0 p-3 border-t border-gunmetal">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 bg-steel border border-gunmetal clip-hexagon flex items-center justify-center">
                    <span class="font-heading text-[9px] text-giallo font-bold">
                        <?php echo e(substr(auth()->user()->name ?? 'C', 0, 1)); ?>

                    </span>
                </div>
                <span class="font-heading text-[10px] text-silver-dim"><?php echo e(auth()->user()->name ?? 'Customer'); ?></span>
            </div>
            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="font-heading text-[10px] text-silver-dim hover:text-danger uppercase tracking-wider transition-premium">
                    Logout
                </button>
            </form>
        </div>
    </div>
</div><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/components/layout/customer-sidebar.blade.php ENDPATH**/ ?>