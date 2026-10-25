
<header class="topbar flex items-center justify-between">
    
    <button class="lg:hidden text-giallo hover:text-arancio transition-premium">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    
    <div class="hidden lg:flex items-center gap-3">
        <div class="h-px w-8 bg-giallo"></div>
        <h2 class="font-heading text-sm uppercase tracking-[0.15em] text-silver-bright">
            <?php echo $__env->yieldContent('title', 'Admin Dashboard'); ?>
        </h2>
    </div>

    
    <?php
        $topbarUser = auth()->user();
        $topbarRole = $topbarUser->role ?? 'admin';
        $topbarRoleLabel = match($topbarRole) {
            'super-admin' => 'Super Admin',
            'manager'     => 'Manager',
            'mechanic'    => 'Mekanik',
            'sales'       => 'Sales',
            'inventory'   => 'Inventory',
            'cashier'     => 'Kasir',
            default       => ucfirst(str_replace('-', ' ', $topbarRole)),
        };

        $stat1Label = 'WO Active';
        $stat1Value = 0;
        $stat1Color = 'text-giallo';
        $stat1Link = route('admin.work-orders.index');

        $stat2Label = 'Unit Ready';
        $stat2Value = 0;
        $stat2Color = 'text-verde';
        $stat2Link = route('admin.cars.index');

        try {
            if ($topbarRole === 'mechanic') {
                $stat1Label = 'WO Saya';
                $stat1Value = \App\Models\WorkOrder::whereIn('status', ['queued', 'in_progress'])
                    ->where('assigned_mechanic_id', $topbarUser->id)
                    ->count();
                $stat1Link = route('admin.work-orders.index');

                $stat2Label = 'Antrean Bengkel';
                $stat2Value = \App\Models\WorkOrder::whereIn('status', ['queued', 'in_progress'])->count();
                $stat2Color = 'text-giallo';
                $stat2Link = route('admin.work-orders.index');
            } elseif ($topbarRole === 'sales') {
                $stat1Label = 'Unit Ready';
                $stat1Value = \App\Models\CarUnit::where('status', 'available')->count();
                $stat1Color = 'text-verde';
                $stat1Link = route('admin.cars.index');

                $stat2Label = 'Penawaran Masuk';
                $stat2Value = \App\Models\Offer::where('status', 'pending')->count();
                $stat2Color = 'text-giallo';
                $stat2Link = route('admin.offers.index');
            } elseif ($topbarRole === 'inventory') {
                $stat1Label = 'Stok Menipis';
                $stat1Value = \App\Models\SparePart::whereColumn('stock', '<=', 'low_stock_threshold')->count();
                $stat1Color = $stat1Value > 0 ? 'text-rosso' : 'text-verde';
                $stat1Link = route('admin.spare-parts.index');

                $stat2Label = 'Total Part';
                $stat2Value = \App\Models\SparePart::count();
                $stat2Color = 'text-silver-bright';
                $stat2Link = route('admin.spare-parts.index');
            } elseif ($topbarRole === 'cashier') {
                $stat1Label = 'Tagihan Pending';
                $stat1Value = \App\Models\SalesOrder::where('status', 'pending')->count();
                $stat1Color = 'text-giallo';
                $stat1Link = route('admin.pos.index');

                $stat2Label = 'Trx Hari Ini';
                $stat2Value = \App\Models\SalesOrder::where('status', 'paid')
                    ->whereDate('created_at', today())
                    ->count();
                $stat2Color = 'text-verde';
                $stat2Link = route('admin.pos.index');
            } elseif ($topbarRole === 'manager') {
                $stat1Label = 'Perlu Approval';
                $stat1Value = \App\Models\ApprovalRequest::where('status', 'pending')->count();
                $stat1Color = $stat1Value > 0 ? 'text-arancio font-bold animate-pulse' : 'text-silver-dim';
                $stat1Link = route('admin.approvals.index');

                $stat2Label = 'Total WO';
                $stat2Value = \App\Models\WorkOrder::whereIn('status', ['queued', 'in_progress'])->count();
                $stat2Color = 'text-giallo';
                $stat2Link = route('admin.work-orders.index');
            } else {
                // Super Admin
                $stat1Label = 'WO Active';
                $stat1Value = \App\Models\WorkOrder::whereIn('status', ['queued', 'in_progress'])->count();
                $stat1Color = 'text-giallo';
                $stat1Link = route('admin.work-orders.index');

                $stat2Label = 'Unit Ready';
                $stat2Value = \App\Models\CarUnit::where('status', 'available')->count();
                $stat2Color = 'text-verde';
                $stat2Link = route('admin.cars.index');
            }
        } catch (\Throwable $e) {
            // graceful fallback
        }

        $adminUnreadCount = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0;
    ?>

    <div class="flex items-center gap-3">
        
        <div class="hidden md:flex items-center gap-4 mr-4">
            <a href="<?php echo e($stat1Link); ?>" class="text-center group" title="<?php echo e($stat1Label); ?>">
                <p class="font-mono text-xs <?php echo e($stat1Color); ?> group-hover:underline"><?php echo e($stat1Value); ?></p>
                <p class="font-heading text-[8px] text-silver-dim uppercase"><?php echo e($stat1Label); ?></p>
            </a>
            <a href="<?php echo e($stat2Link); ?>" class="text-center group" title="<?php echo e($stat2Label); ?>">
                <p class="font-mono text-xs <?php echo e($stat2Color); ?> group-hover:underline"><?php echo e($stat2Value); ?></p>
                <p class="font-heading text-[8px] text-silver-dim uppercase"><?php echo e($stat2Label); ?></p>
            </a>
        </div>

        
        <a href="<?php echo e(route('admin.notifications.index')); ?>" class="relative text-silver-dim hover:text-giallo transition-premium" title="Notifikasi <?php echo e($topbarRoleLabel); ?>">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <?php if($adminUnreadCount > 0): ?>
                <span class="absolute -top-1 -right-1 min-w-3.5 h-3.5 px-1 bg-red-600 rounded-full text-[9px] flex items-center justify-center text-white font-bold leading-none animate-pulse">
                    <?php echo e($adminUnreadCount > 9 ? '9+' : $adminUnreadCount); ?>

                </span>
            <?php endif; ?>
        </a>

        
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 bg-giallo/10 border border-giallo/30 clip-hexagon flex items-center justify-center">
                <span class="font-heading text-[10px] text-giallo font-bold">
                    <?php echo e(substr($topbarUser->name ?? 'A', 0, 1)); ?>

                </span>
            </div>
            <div class="hidden md:block">
                <p class="font-heading text-xs text-silver-bright"><?php echo e($topbarUser->name ?? 'Admin'); ?></p>
                <p class="font-mono text-[9px] text-giallo uppercase tracking-wider"><?php echo e($topbarRoleLabel); ?></p>
            </div>
        </div>
    </div>
</header><?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/components/layout/admin-topbar.blade.php ENDPATH**/ ?>