<?php $__env->startSection('title', 'Tambah Pengguna Baru'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-3xl mx-auto">
    <div class="flex items-center justify-between">
        <a href="<?php echo e(route('admin.users.index')); ?>" class="font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider">&larr; Kembali ke Daftar Pengguna</a>
    </div>

    <div>
        <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">TAMBAH PENGGUNA BARU</h1>
        <p class="text-xs text-silver-dim mt-1">Daftarkan akun staf, teknisi, sales, manager, atau admin baru</p>
    </div>

    <div class="panel border border-gunmetal bg-carbon p-6">
        <form action="<?php echo e(route('admin.users.store')); ?>" method="POST" class="space-y-4">
            <?php echo csrf_field(); ?>

            <div>
                <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">Nama Lengkap *</label>
                <input type="text" name="name" required value="<?php echo e(old('name')); ?>" placeholder="Masukkan nama lengkap" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-danger mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">Email *</label>
                <input type="email" name="email" required value="<?php echo e(old('email')); ?>" placeholder="contoh@audrive.local" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-danger mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">No. Telepon / WhatsApp</label>
                    <input type="text" name="phone" value="<?php echo e(old('phone')); ?>" placeholder="08xxxxxxxxxx" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                    <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-danger mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">Role / Peran *</label>
                    <select name="role" required class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                        <option value="manager" <?php echo e(old('role') === 'manager' ? 'selected' : ''); ?>>Manager</option>
                        <option value="sales" <?php echo e(old('role') === 'sales' ? 'selected' : ''); ?>>Sales</option>
                        <option value="cashier" <?php echo e(old('role') === 'cashier' ? 'selected' : ''); ?>>Cashier</option>
                        <option value="inventory" <?php echo e(old('role') === 'inventory' ? 'selected' : ''); ?>>Inventory</option>
                        <option value="mechanic" <?php echo e(old('role') === 'mechanic' ? 'selected' : ''); ?>>Mechanic</option>
                        <option value="customer" <?php echo e(old('role') === 'customer' ? 'selected' : ''); ?>>Customer</option>
                        <option value="super-admin" <?php echo e(old('role') === 'super-admin' ? 'selected' : ''); ?>>Super Admin</option>
                    </select>
                    <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-danger mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">Password * (Min. 8 karakter)</label>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-danger mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">Konfirmasi Password *</label>
                    <input type="password" name="password_confirmation" required placeholder="••••••••" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gunmetal">
                <a href="<?php echo e(route('admin.users.index')); ?>" class="px-5 py-2 border border-gunmetal text-silver text-xs uppercase tracking-wider">Batal</a>
                <button type="submit" class="px-7 py-2 bg-giallo hover:bg-arancio text-nero font-heading font-bold text-xs uppercase tracking-wider transition clip-angular-button">
                    Simpan Pengguna
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/admin/users/create.blade.php ENDPATH**/ ?>