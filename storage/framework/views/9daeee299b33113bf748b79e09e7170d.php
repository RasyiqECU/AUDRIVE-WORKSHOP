<?php $__env->startSection('title', 'Detail Pengguna & Hak Akses — ' . $user->name); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-5xl mx-auto">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <a href="<?php echo e(route('admin.users.index')); ?>" class="font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider flex items-center gap-1.5 transition-premium">
            <span>&larr;</span> Kembali ke Daftar Pengguna
        </a>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-3.5 py-1.5 bg-steel/60 hover:bg-steel text-silver-bright border border-gunmetal font-heading text-xs uppercase tracking-wider rounded transition-premium flex items-center gap-1.5">
                <span>🖨️</span> Cetak Berkas
            </button>
            <a href="<?php echo e(route('admin.users.edit', $user)); ?>" class="px-4 py-1.5 bg-giallo hover:bg-arancio text-nero font-heading font-bold text-xs uppercase tracking-wider rounded transition-premium flex items-center gap-1.5 shadow-md shadow-giallo/10">
                <span>✏️</span> Edit Data Pengguna
            </a>
        </div>
    </div>

    
    <div class="panel border border-gunmetal bg-carbon p-6 space-y-6 shadow-xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gunmetal pb-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-steel border border-gunmetal clip-hexagon flex items-center justify-center font-display font-bold text-3xl text-giallo shadow-inner">
                    <?php echo e(substr($user->name, 0, 1)); ?>

                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="font-display text-2xl font-bold text-silver-bright tracking-wide"><?php echo e($user->name); ?></h1>
                        <span class="px-2.5 py-0.5 text-[10px] font-heading font-bold uppercase tracking-wider rounded border <?php echo e($roleInfo['badge_class'] ?? 'bg-steel border-gunmetal text-silver-bright'); ?>">
                            <?php echo e($roleInfo['icon'] ?? '👤'); ?> <?php echo e($roleInfo['name'] ?? $user->role); ?>

                        </span>
                    </div>
                    <p class="font-mono text-xs text-silver-dim mt-1">
                        <?php echo e($user->email); ?> &bull; <?php echo e($user->phone ?: 'Tidak ada nomor telepon'); ?>

                    </p>
                </div>
            </div>
            <div class="flex sm:flex-col items-center sm:items-end justify-between gap-1 text-xs">
                <span class="text-silver-dim text-[11px] font-heading uppercase tracking-wider">Status Akun:</span>
                <?php if($user->is_active): ?>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-verde/15 border border-verde/40 text-verde font-heading text-xs font-bold uppercase tracking-wider rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-verde animate-pulse"></span> Aktif
                    </span>
                <?php else: ?>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-danger/15 border border-danger/40 text-danger font-heading text-xs font-bold uppercase tracking-wider rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-danger"></span> Nonaktif
                    </span>
                <?php endif; ?>
            </div>
        </div>

        
        <dl class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs font-mono bg-nero/50 p-4 border border-gunmetal/50 rounded">
            <div>
                <dt class="text-silver-dim text-[11px] font-heading uppercase tracking-wider">ID Akun</dt>
                <dd class="text-silver-bright font-bold mt-1">#USR-<?php echo e(str_pad((string)$user->id, 5, '0', STR_PAD_LEFT)); ?></dd>
            </div>
            <div>
                <dt class="text-silver-dim text-[11px] font-heading uppercase tracking-wider">Role Kunci</dt>
                <dd class="text-giallo font-bold uppercase mt-1"><?php echo e($user->role); ?></dd>
            </div>
            <div>
                <dt class="text-silver-dim text-[11px] font-heading uppercase tracking-wider">Email Terverifikasi</dt>
                <dd class="mt-1 <?php echo e($user->email_verified_at ? 'text-verde font-semibold' : 'text-silver-dim italic'); ?>">
                    <?php echo e($user->email_verified_at ? $user->email_verified_at->format('d M Y H:i') : 'Belum Diverifikasi'); ?>

                </dd>
            </div>
            <div>
                <dt class="text-silver-dim text-[11px] font-heading uppercase tracking-wider">Terdaftar Sejak</dt>
                <dd class="mt-1 text-silver-bright"><?php echo e($user->created_at->format('d M Y H:i')); ?></dd>
            </div>
        </dl>
    </div>

    
    <div class="panel border border-gunmetal bg-carbon p-6 space-y-6 shadow-xl">
        
        <div class="border-b border-gunmetal pb-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-giallo font-mono text-xs tracking-widest uppercase">Otorisasi & Keamanan</span>
                        <span class="text-silver-dim text-xs">/</span>
                        <span class="text-silver-dim text-xs font-mono">Prinsip Least Privilege</span>
                    </div>
                    <h2 class="font-display text-xl md:text-2xl font-bold text-silver-bright tracking-wide mt-1 flex items-center gap-2">
                        <span><?php echo e($roleInfo['icon'] ?? '🛡️'); ?></span> KETERANGAN HAK AKSES ROLE: <?php echo e(strtoupper($roleInfo['name'] ?? $user->role)); ?>

                    </h2>
                </div>
                <div class="sm:text-right">
                    <span class="text-[11px] font-heading uppercase tracking-wider text-silver-dim block">Kategori Wewenang:</span>
                    <span class="text-xs font-mono font-bold text-giallo"><?php echo e($roleInfo['category'] ?? 'Pengguna Sistem'); ?></span>
                </div>
            </div>
            <p class="text-sm text-silver-dim mt-2 leading-relaxed bg-steel/20 p-3 rounded border border-gunmetal/60">
                <strong class="text-silver-bright">Deskripsi Tanggung Jawab:</strong> <?php echo e($roleInfo['summary'] ?? '-'); ?>

            </p>
        </div>

        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div class="p-5 bg-nero border border-verde/30 rounded shadow-md space-y-3">
                <div class="flex items-center gap-2 border-b border-verde/20 pb-2">
                    <span class="w-6 h-6 rounded-full bg-verde/20 text-verde flex items-center justify-center text-xs font-bold">✓</span>
                    <h3 class="font-heading text-xs uppercase tracking-wider text-verde font-bold">
                        Tindakan yang Diizinkan (Dapat Melakukan)
                    </h3>
                </div>
                <ul class="space-y-2 text-xs text-silver-bright font-sans">
                    <?php $__empty_1 = true; $__currentLoopData = $roleInfo['allowed_actions'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li class="flex items-start gap-2">
                            <span class="text-verde text-sm shrink-0 leading-none mt-0.5">●</span>
                            <span class="leading-relaxed"><?php echo e($action); ?></span>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="text-silver-dim italic">Tidak ada keterangan wewenang khusus.</li>
                    <?php endif; ?>
                </ul>
            </div>

            
            <div class="p-5 bg-nero border border-danger/30 rounded shadow-md space-y-3">
                <div class="flex items-center gap-2 border-b border-danger/20 pb-2">
                    <span class="w-6 h-6 rounded-full bg-danger/20 text-danger flex items-center justify-center text-xs font-bold">✕</span>
                    <h3 class="font-heading text-xs uppercase tracking-wider text-danger font-bold">
                        Batasan Ketat Sistem (Tidak Dapat Melakukan)
                    </h3>
                </div>
                <ul class="space-y-2 text-xs text-silver-dim font-sans">
                    <?php $__empty_1 = true; $__currentLoopData = $roleInfo['restricted_actions'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $restricted): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li class="flex items-start gap-2">
                            <span class="text-danger text-sm shrink-0 leading-none mt-0.5">■</span>
                            <span class="leading-relaxed text-silver-dim"><?php echo e($restricted); ?></span>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="text-silver-dim italic">Role ini tidak memiliki batasan khusus di luar kebijakan sistem.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        
        <div class="space-y-3 pt-2">
            <div class="flex items-center justify-between">
                <h3 class="font-heading text-xs uppercase tracking-wider text-silver-bright font-bold flex items-center gap-2">
                    <span>📑</span> Daftar Menu & Modul yang Terbuka untuk Role Ini
                </h3>
                <span class="text-[11px] font-mono text-silver-dim">
                    Total: <?php echo e(count($roleInfo['menus'] ?? [])); ?> Modul Aktif
                </span>
            </div>

            <div class="overflow-x-auto border border-gunmetal rounded bg-nero">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-steel/40 border-b border-gunmetal text-[10px] font-heading uppercase tracking-wider text-silver-dim">
                            <th class="py-2.5 px-3.5">Modul / Menu</th>
                            <th class="py-2.5 px-3.5">Cakupan Rute</th>
                            <th class="py-2.5 px-3.5">Tingkat Izin</th>
                            <th class="py-2.5 px-3.5">Keterangan Akses Operasional</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gunmetal/30 text-xs font-sans">
                        <?php $__empty_1 = true; $__currentLoopData = $roleInfo['menus'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-steel/20 transition-premium">
                                <td class="py-3 px-3.5 font-bold text-silver-bright whitespace-nowrap">
                                    <span class="mr-1.5"><?php echo e($menu['icon'] ?? '📁'); ?></span>
                                    <?php echo e($menu['name']); ?>

                                </td>
                                <td class="py-3 px-3.5 font-mono text-[11px] text-giallo whitespace-nowrap">
                                    <?php echo e($menu['route_hint']); ?>

                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <span class="px-2 py-0.5 text-[10px] font-heading font-semibold uppercase rounded bg-steel/60 border border-gunmetal text-silver-bright">
                                        <?php echo e($menu['access_level']); ?>

                                    </span>
                                </td>
                                <td class="py-3 px-3.5 text-silver-dim text-xs leading-relaxed">
                                    <?php echo e($menu['description']); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="py-6 text-center text-silver-dim italic text-xs">
                                    Role ini tidak memiliki menu akses di panel dashboard admin.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <p class="text-[11px] text-silver-dim italic font-mono pt-1">
                * Catatan: Hak akses di atas secara ketat dilindungi oleh RoleMiddleware per-rute dan Authorization Policies. Menu dan tombol di luar izin ini secara otomatis disembunyikan dari antarmuka pengguna (Least Privilege).
            </p>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/admin/users/show.blade.php ENDPATH**/ ?>