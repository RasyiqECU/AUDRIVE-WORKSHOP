<?php $__env->startSection('title', 'Edit Pengguna — ' . $user->name); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-3xl mx-auto">
    <div class="flex items-center justify-between">
        <a href="<?php echo e(route('admin.users.index')); ?>" class="font-heading text-xs text-giallo hover:text-arancio uppercase tracking-wider">&larr; Kembali ke Daftar Pengguna</a>
    </div>

    <div>
        <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">EDIT PENGGUNA</h1>
        <p class="text-xs text-silver-dim mt-1">Perbarui data profil & hak akses <?php echo e($user->name); ?></p>
    </div>

    <div class="panel border border-gunmetal bg-carbon p-6">
        <form action="<?php echo e(route('admin.users.update', $user)); ?>" method="POST" class="space-y-4">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div>
                <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">Nama Lengkap *</label>
                <input type="text" name="name" required value="<?php echo e(old('name', $user->name)); ?>" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
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
                <input type="email" name="email" required value="<?php echo e(old('email', $user->email)); ?>" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-danger mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">No. Telepon / WhatsApp</label>
                <input type="text" name="phone" value="<?php echo e(old('phone', $user->phone)); ?>" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                <?php $__errorArgs = ['phone'];
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
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">Role / Peran *</label>
                    <select name="role" required class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                        <option value="super-admin" <?php echo e(old('role', $user->role) === 'super-admin' ? 'selected' : ''); ?>>Super Admin</option>
                        <option value="manager" <?php echo e(old('role', $user->role) === 'manager' ? 'selected' : ''); ?>>Manager</option>
                        <option value="sales" <?php echo e(old('role', $user->role) === 'sales' ? 'selected' : ''); ?>>Sales</option>
                        <option value="cashier" <?php echo e(old('role', $user->role) === 'cashier' ? 'selected' : ''); ?>>Cashier</option>
                        <option value="inventory" <?php echo e(old('role', $user->role) === 'inventory' ? 'selected' : ''); ?>>Inventory</option>
                        <option value="mechanic" <?php echo e(old('role', $user->role) === 'mechanic' ? 'selected' : ''); ?>>Mechanic</option>
                        <option value="customer" <?php echo e(old('role', $user->role) === 'customer' ? 'selected' : ''); ?>>Customer</option>
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
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">Status Akun</label>
                    <select name="is_active" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                        <option value="1" <?php echo e(old('is_active', $user->is_active) ? 'selected' : ''); ?>>Aktif</option>
                        <option value="0" <?php echo e(!old('is_active', $user->is_active) ? 'selected' : ''); ?>>Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">Password Baru (Kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" placeholder="••••••••" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
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
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-bright mb-1">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" placeholder="••••••••" class="w-full bg-nero border border-gunmetal px-3 py-2 text-xs text-silver-bright focus:border-giallo focus:outline-none">
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gunmetal">
                <a href="<?php echo e(route('admin.users.index')); ?>" class="px-5 py-2 border border-gunmetal text-silver text-xs uppercase tracking-wider">Batal</a>
                <button type="submit" class="px-7 py-2 bg-giallo hover:bg-arancio text-nero font-heading font-bold text-xs uppercase tracking-wider transition clip-angular-button">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/admin/users/edit.blade.php ENDPATH**/ ?>