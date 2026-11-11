<?php $__env->startSection('title', 'Manajemen Pengguna & Matriks Hak Akses'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ showRoleGuide: false, activeRole: 'super-admin' }">
    
    <div class="no-print flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">
                MANAJEMEN PENGGUNA
            </h1>
            <p class="text-sm text-silver-dim mt-1">
                Kelola akun staf, teknisi, kasir, admin, dan hak akses berdasarkan prinsip <span class="text-giallo font-mono font-semibold">Least Privilege</span>
            </p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <button type="button" @click="showRoleGuide = true" class="px-3.5 py-2 bg-steel/80 hover:bg-steel text-giallo border border-giallo/40 hover:border-giallo font-heading text-xs uppercase tracking-wider rounded transition-premium flex items-center gap-1.5 shadow-sm">
                <span>📋</span> Panduan Hak Akses
            </button>
            <button onclick="window.print()" class="px-3.5 py-2 bg-steel/60 hover:bg-steel text-silver-bright border border-gunmetal font-heading text-xs uppercase tracking-wider rounded transition-premium flex items-center gap-1.5 shadow-sm">
                <span>🖨️</span> Cetak Berkas
            </button>
            <a href="<?php echo e(route('admin.users.create')); ?>" class="inline-flex items-center gap-1.5 px-4 py-2 bg-giallo hover:bg-arancio text-nero font-heading font-bold text-xs uppercase tracking-wider transition-premium shadow-lg shadow-giallo/20 clip-angular-button shrink-0">
                <span>+</span> Tambah Pengguna
            </a>
        </div>
    </div>

    
    <div class="hidden print:block border-b-2 border-black pb-4 mb-6">
        <table class="w-full text-black">
            <tr>
                <td style="width: 60%; vertical-align: top;">
                    <h2 class="text-2xl font-bold uppercase tracking-wider">PT AUDRIVE SUPERCAR INDONESIA</h2>
                    <p class="text-xs font-semibold uppercase text-gray-600">Database Pengguna & Hak Akses Sistem Portal Internal</p>
                    <p class="text-[10px] text-gray-600">Graha AUDrive Kav. 88, SCBD, Jakarta Selatan &bull; Telp: +62 21 8088 9900</p>
                </td>
                <td style="width: 40%; text-align: right; vertical-align: top;">
                    <h3 class="text-lg font-bold uppercase">LAPORAN DATA PENGGUNA</h3>
                    <p class="text-xs font-mono">Dokumen: USR-RPT-<?php echo e(date('Ymd-His')); ?></p>
                    <p class="text-[10px] text-gray-500">Dicetak: <?php echo e(now()->format('d/m/Y H:i')); ?> WIB oleh <?php echo e(auth()->user()->name); ?></p>
                </td>
            </tr>
        </table>
    </div>

    
    <div class="no-print panel border border-gunmetal bg-carbon/90 p-4">
        <form method="GET" action="<?php echo e(route('admin.users.index')); ?>" class="flex flex-wrap items-center gap-3">
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari nama atau email..." class="bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none min-w-[220px]">
            
            <select name="role" class="bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                <option value="">Semua Role</option>
                <option value="super-admin" <?php echo e(request('role') === 'super-admin' ? 'selected' : ''); ?>>👑 Super Admin</option>
                <option value="owner" <?php echo e(request('role') === 'owner' ? 'selected' : ''); ?>>👔 Business Owner</option>
                <option value="manager" <?php echo e(request('role') === 'manager' ? 'selected' : ''); ?>>🧭 Operational Manager</option>
                <option value="sales" <?php echo e(request('role') === 'sales' ? 'selected' : ''); ?>>🏎 Sales Executive</option>
                <option value="cashier" <?php echo e(request('role') === 'cashier' ? 'selected' : ''); ?>>💳 Kasir</option>
                <option value="inventory" <?php echo e(request('role') === 'inventory' ? 'selected' : ''); ?>>📦 Gudang / Inventory</option>
                <option value="mechanic" <?php echo e(request('role') === 'mechanic' ? 'selected' : ''); ?>>🔧 Mekanik / Teknisi</option>
                <option value="customer" <?php echo e(request('role') === 'customer' ? 'selected' : ''); ?>>👤 Customer</option>
            </select>

            <select name="status" class="bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                <option value="">Semua Status</option>
                <option value="active" <?php echo e(request('status') === 'active' ? 'selected' : ''); ?>>Aktif</option>
                <option value="inactive" <?php echo e(request('status') === 'inactive' ? 'selected' : ''); ?>>Nonaktif</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright font-heading text-xs uppercase tracking-wider transition-premium">
                Filter
            </button>
            <?php if(request()->hasAny(['search', 'role', 'status'])): ?>
                <a href="<?php echo e(route('admin.users.index')); ?>" class="text-xs text-silver-dim hover:text-giallo underline font-mono">Reset Filter</a>
            <?php endif; ?>
        </form>
    </div>

    
    <div class="panel border border-gunmetal bg-carbon overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-steel/40 border-b border-gunmetal text-[11px] font-heading uppercase tracking-wider text-silver-dim">
                        <th class="py-3 px-4">Pengguna</th>
                        <th class="py-3 px-4">Kontak</th>
                        <th class="py-3 px-4">Role Sistem</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Bergabung</th>
                        <th class="py-3 px-4 text-right no-print">Aksi & Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/40 text-xs font-sans">
                    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $userRoleData = $roleMatrix[$user->role] ?? null;
                            $badgeClass = $userRoleData['badge_class'] ?? 'bg-steel border-gunmetal text-silver-bright';
                            $roleIcon = $userRoleData['icon'] ?? '👤';
                        ?>
                        <tr class="hover:bg-steel/20 transition-premium">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-steel border border-gunmetal clip-hexagon flex items-center justify-center font-bold text-giallo no-print shrink-0">
                                        <?php echo e(substr($user->name, 0, 1)); ?>

                                    </div>
                                    <div>
                                        <p class="font-bold text-silver-bright"><?php echo e($user->name); ?></p>
                                        <p class="font-mono text-[10px] text-silver-dim"><?php echo e($user->email); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-silver-dim">
                                <?php echo e($user->phone ?: '-'); ?>

                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 text-[10px] font-heading font-bold uppercase tracking-wider rounded border <?php echo e($badgeClass); ?>">
                                    <?php echo e($roleIcon); ?> <?php echo e($userRoleData['name'] ?? $user->role); ?>

                                </span>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <?php if($user->is_active): ?>
                                    <span class="inline-flex items-center gap-1 text-verde font-bold text-xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-verde"></span> Aktif
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 text-danger font-bold text-xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-danger"></span> Nonaktif
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-silver-dim text-[10px] whitespace-nowrap">
                                <?php echo e($user->created_at->format('d M Y')); ?>

                            </td>
                            <td class="py-3.5 px-4 text-right space-x-1.5 no-print whitespace-nowrap">
                                <a href="<?php echo e(route('admin.users.show', $user)); ?>" class="px-2.5 py-1.5 bg-steel/60 hover:bg-giallo hover:text-nero border border-gunmetal text-silver-bright font-heading text-[10px] uppercase font-bold rounded transition-premium inline-flex items-center gap-1 shadow-sm" title="Lihat detail dan matriks hak akses pengguna ini">
                                    <span>🔍</span> Detail & Hak Akses
                                </a>
                                <a href="<?php echo e(route('admin.users.edit', $user)); ?>" class="px-2.5 py-1.5 bg-steel/30 hover:bg-steel border border-gunmetal text-silver-dim hover:text-silver-bright font-heading text-[10px] uppercase rounded transition-premium inline-flex items-center gap-1">
                                    <span>✏️</span> Edit
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="py-8 text-center text-silver-dim font-heading uppercase text-xs tracking-wider">
                                Tidak ada data pengguna yang cocok
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($users->hasPages()): ?>
            <div class="p-4 border-t border-gunmetal bg-steel/20 no-print">
                <?php echo e($users->links()); ?>

            </div>
        <?php endif; ?>
    </div>

    
    <div class="hidden print:block pt-8">
        <table class="w-full text-center text-black text-xs">
            <tr>
                <td style="width: 50%;">
                    <p class="text-[10px] font-semibold text-gray-600 mb-14">Dicetak & Diperiksa Oleh (Admin HR/IT):</p>
                    <p class="font-bold underline"><?php echo e(auth()->user()->name); ?></p>
                    <p class="text-[9px] text-gray-500">Administrator Sistem</p>
                </td>
                <td style="width: 50%;">
                    <p class="text-[10px] font-semibold text-gray-600 mb-14">Disetujui Oleh (Direktur/General Manager):</p>
                    <p class="font-bold underline">Management AUDrive</p>
                    <p class="text-[9px] text-gray-500">Pimpinan Operasional</p>
                </td>
            </tr>
        </table>
    </div>

    
    <div
        x-show="showRoleGuide"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-nero/80 backdrop-blur-sm"
        @keydown.escape.window="showRoleGuide = false"
    >
        <div
            x-show="showRoleGuide"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            @click.away="showRoleGuide = false"
            class="panel border border-gunmetal bg-carbon w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col shadow-2xl rounded-sm"
        >
            
            <div class="bg-steel/30 px-6 py-4 border-b border-gunmetal flex items-center justify-between shrink-0">
                <div class="flex items-center gap-2">
                    <span class="text-xl">📋</span>
                    <div>
                        <h3 class="font-display text-lg font-bold text-silver-bright tracking-wide">
                            PANDUAN MATRIKS HAK AKSES SISTEM (LEAST PRIVILEGE)
                        </h3>
                        <p class="text-[11px] text-silver-dim">
                            Keterangan menu dan wewenang yang diizinkan untuk setiap peran pengguna di portal AUDrive
                        </p>
                    </div>
                </div>
                <button
                    type="button"
                    @click="showRoleGuide = false"
                    class="text-silver-dim hover:text-giallo p-1 text-lg font-mono focus:outline-none"
                    aria-label="Tutup"
                >
                    ✕
                </button>
            </div>

            
            <div class="px-6 pt-3 pb-2 border-b border-gunmetal bg-nero/50 flex items-center gap-1.5 overflow-x-auto shrink-0">
                <?php $__currentLoopData = $roleMatrix; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rKey => $rData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button
                        type="button"
                        @click="activeRole = '<?php echo e($rKey); ?>'"
                        :class="activeRole === '<?php echo e($rKey); ?>' ? 'bg-giallo text-nero font-bold border-giallo' : 'bg-steel/30 text-silver-dim hover:text-silver-bright border-gunmetal'"
                        class="px-3 py-1.5 border font-heading text-xs uppercase tracking-wider rounded transition-premium shrink-0 flex items-center gap-1.5 shadow-sm"
                    >
                        <span><?php echo e($rData['icon']); ?></span>
                        <span><?php echo e($rData['name']); ?></span>
                    </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            
            <div class="p-6 overflow-y-auto space-y-6 text-xs flex-1">
                <?php $__currentLoopData = $roleMatrix; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rKey => $rData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div x-show="activeRole === '<?php echo e($rKey); ?>'" class="space-y-6">
                        
                        <div class="bg-steel/20 border border-gunmetal p-4 rounded flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl"><?php echo e($rData['icon']); ?></span>
                                    <div>
                                        <h4 class="font-display text-base font-bold text-silver-bright"><?php echo e($rData['name']); ?></h4>
                                        <p class="text-[11px] font-mono text-giallo uppercase tracking-wider"><?php echo e($rData['category']); ?></p>
                                    </div>
                                </div>
                                <p class="text-silver-dim mt-2 text-xs leading-relaxed">
                                    <?php echo e($rData['summary']); ?>

                                </p>
                            </div>
                        </div>

                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            
                            <div class="bg-nero border border-verde/30 p-4 rounded space-y-2">
                                <h5 class="font-heading text-xs uppercase tracking-wider text-verde font-bold flex items-center gap-1.5 border-b border-verde/20 pb-1.5">
                                    <span>✓</span> Tindakan yang Diizinkan (Dapat Melakukan)
                                </h5>
                                <ul class="space-y-1.5 text-xs text-silver-bright font-sans">
                                    <?php $__currentLoopData = $rData['allowed_actions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $act): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="flex items-start gap-1.5">
                                            <span class="text-verde shrink-0">●</span>
                                            <span><?php echo e($act); ?></span>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>

                            
                            <div class="bg-nero border border-danger/30 p-4 rounded space-y-2">
                                <h5 class="font-heading text-xs uppercase tracking-wider text-danger font-bold flex items-center gap-1.5 border-b border-danger/20 pb-1.5">
                                    <span>✕</span> Batasan Ketat (Tidak Dapat Melakukan)
                                </h5>
                                <ul class="space-y-1.5 text-xs text-silver-dim font-sans">
                                    <?php $__currentLoopData = $rData['restricted_actions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $res): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="flex items-start gap-1.5">
                                            <span class="text-danger shrink-0">■</span>
                                            <span><?php echo e($res); ?></span>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        </div>

                        
                        <div class="space-y-2">
                            <h5 class="font-heading text-xs uppercase tracking-wider text-silver-bright font-bold flex items-center gap-1.5">
                                <span>📁</span> Menu & Modul yang Terbuka (<?php echo e(count($rData['menus'])); ?> Modul)
                            </h5>
                            <div class="overflow-x-auto border border-gunmetal rounded bg-nero">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead>
                                        <tr class="bg-steel/40 border-b border-gunmetal text-[10px] font-heading uppercase tracking-wider text-silver-dim">
                                            <th class="py-2 px-3">Menu / Modul</th>
                                            <th class="py-2 px-3">Tingkat Izin</th>
                                            <th class="py-2 px-3">Keterangan Akses</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gunmetal/30">
                                        <?php $__currentLoopData = $rData['menus']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr class="hover:bg-steel/10">
                                                <td class="py-2.5 px-3 font-semibold text-silver-bright whitespace-nowrap">
                                                    <?php echo e($m['icon']); ?> <?php echo e($m['name']); ?>

                                                </td>
                                                <td class="py-2.5 px-3 whitespace-nowrap">
                                                    <span class="px-2 py-0.5 text-[9px] font-heading font-semibold uppercase rounded bg-steel/60 border border-gunmetal text-silver-bright">
                                                        <?php echo e($m['access_level']); ?>

                                                    </span>
                                                </td>
                                                <td class="py-2.5 px-3 text-silver-dim text-[11px] leading-relaxed">
                                                    <?php echo e($m['description']); ?>

                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            
            <div class="bg-steel/30 px-6 py-3 border-t border-gunmetal flex items-center justify-between shrink-0">
                <span class="text-[11px] text-silver-dim font-mono italic">
                    * Hak akses ditegakkan otomatis oleh RoleMiddleware & Authorization Policies.
                </span>
                <button
                    type="button"
                    @click="showRoleGuide = false"
                    class="px-4 py-1.5 bg-steel hover:bg-steel/80 text-silver-bright border border-gunmetal font-heading text-xs uppercase tracking-wider rounded transition-premium"
                >
                    Tutup Panduan
                </button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/admin/users/index.blade.php ENDPATH**/ ?>