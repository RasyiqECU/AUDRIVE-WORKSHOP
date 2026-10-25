<?php $__env->startSection('title', 'Manajemen Pengguna'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">
                MANAJEMEN PENGGUNA
            </h1>
            <p class="text-sm text-silver-dim mt-1">
                Kelola akun staf, teknisi, kasir, admin, dan pelanggan sistem AUDrive
            </p>
        </div>
        <a href="<?php echo e(route('admin.users.create')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-giallo hover:bg-arancio text-nero font-heading font-bold text-xs uppercase tracking-wider transition-premium shadow-lg shadow-giallo/20 clip-angular-button shrink-0">
            <span>+</span> Tambah Pengguna
        </a>
    </div>

    
    <div class="panel border border-gunmetal bg-carbon/90 p-4">
        <form method="GET" action="<?php echo e(route('admin.users.index')); ?>" class="flex flex-wrap items-center gap-3">
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari nama atau email..." class="bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none min-w-[220px]">
            
            <select name="role" class="bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                <option value="">Semua Role</option>
                <option value="super-admin" <?php echo e(request('role') === 'super-admin' ? 'selected' : ''); ?>>Super Admin</option>
                <option value="manager" <?php echo e(request('role') === 'manager' ? 'selected' : ''); ?>>Manager</option>
                <option value="sales" <?php echo e(request('role') === 'sales' ? 'selected' : ''); ?>>Sales</option>
                <option value="cashier" <?php echo e(request('role') === 'cashier' ? 'selected' : ''); ?>>Cashier</option>
                <option value="inventory" <?php echo e(request('role') === 'inventory' ? 'selected' : ''); ?>>Inventory</option>
                <option value="mechanic" <?php echo e(request('role') === 'mechanic' ? 'selected' : ''); ?>>Mechanic / Teknisi</option>
                <option value="customer" <?php echo e(request('role') === 'customer' ? 'selected' : ''); ?>>Customer</option>
            </select>

            <select name="status" class="bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                <option value="">Semua Status</option>
                <option value="active" <?php echo e(request('status') === 'active' ? 'selected' : ''); ?>>Aktif</option>
                <option value="inactive" <?php echo e(request('status') === 'inactive' ? 'selected' : ''); ?>>Nonaktif</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-steel hover:bg-steel/80 border border-gunmetal text-silver-bright font-heading text-xs uppercase tracking-wider transition-premium">
                Filter
            </button>
        </form>
    </div>

    
    <div class="panel border border-gunmetal bg-carbon overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-steel/40 border-b border-gunmetal text-[11px] font-heading uppercase tracking-wider text-silver-dim">
                        <th class="py-3 px-4">Pengguna</th>
                        <th class="py-3 px-4">Kontak</th>
                        <th class="py-3 px-4">Role</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Bergabung</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gunmetal/40 text-xs">
                    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-steel/20 transition-premium">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-steel border border-gunmetal clip-hexagon flex items-center justify-center font-bold text-giallo">
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
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 text-[10px] font-heading font-semibold uppercase tracking-wider rounded border <?php echo e($user->role === 'super-admin' ? 'bg-giallo/20 border-giallo text-giallo' : ($user->role === 'customer' ? 'bg-steel border-gunmetal text-silver-dim' : 'bg-verde/20 border-verde text-verde')); ?>">
                                    <?php echo e($user->role); ?>

                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <?php if($user->is_active): ?>
                                    <span class="text-verde font-bold text-xs">● Aktif</span>
                                <?php else: ?>
                                    <span class="text-danger font-bold text-xs">● Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-silver-dim text-[10px]">
                                <?php echo e($user->created_at->format('d M Y')); ?>

                            </td>
                            <td class="py-3.5 px-4 text-right space-x-2">
                                <a href="<?php echo e(route('admin.users.show', $user)); ?>" class="px-2.5 py-1 bg-steel/50 hover:bg-giallo hover:text-nero border border-gunmetal text-silver-bright font-heading text-[10px] uppercase rounded transition-premium">
                                    Detail
                                </a>
                                <a href="<?php echo e(route('admin.users.edit', $user)); ?>" class="px-2.5 py-1 bg-steel/30 hover:bg-steel border border-gunmetal text-silver-dim hover:text-silver-bright font-heading text-[10px] uppercase rounded transition-premium">
                                    Edit
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
            <div class="p-4 border-t border-gunmetal bg-steel/20">
                <?php echo e($users->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/admin/users/index.blade.php ENDPATH**/ ?>