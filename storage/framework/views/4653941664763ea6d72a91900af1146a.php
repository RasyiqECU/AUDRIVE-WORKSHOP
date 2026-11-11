<?php $__env->startSection('title', 'Edit Profil Klien - ' . ($customer->user->name ?? 'Customer')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-4xl">
    
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <a href="<?php echo e(route('admin.customers.index')); ?>" class="text-giallo font-mono text-sm tracking-widest uppercase hover:underline">Database Pelanggan</a>
                <span class="text-silver-dim text-xs">/</span>
                <a href="<?php echo e(route('admin.customers.show', $customer)); ?>" class="text-silver-bright font-mono text-xs hover:underline"><?php echo e($customer->code); ?></a>
                <span class="text-silver-dim text-xs">/</span>
                <span class="text-silver-dim text-xs font-mono">Edit</span>
            </div>
            <h1 class="font-display text-2xl font-bold text-silver-bright tracking-wide mt-1">
                EDIT DATA PELANGGAN
            </h1>
            <p class="text-xs text-silver-dim mt-0.5 font-mono">
                Kode Pelanggan: <span class="text-giallo font-bold"><?php echo e($customer->code); ?></span>
            </p>
        </div>
        <a href="<?php echo e(route('admin.customers.show', $customer)); ?>" class="px-4 py-2 bg-steel/40 hover:bg-steel text-silver-bright border border-gunmetal font-heading text-xs uppercase tracking-wider rounded transition-premium">
            Kembali
        </a>
    </div>

    
    <form action="<?php echo e(route('admin.customers.update', $customer)); ?>" method="POST" class="panel border border-gunmetal bg-carbon p-6 space-y-6">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div>
                <label class="block text-xs font-heading uppercase tracking-wider text-silver-bright mb-2">
                    Nama Lengkap Klien <span class="text-danger">*</span>
                </label>
                <input type="text" name="name" value="<?php echo e(old('name', $customer->user->name ?? '')); ?>" required class="w-full bg-steel/30 border border-gunmetal rounded p-2.5 text-xs text-silver-bright focus:border-giallo focus:outline-none transition-premium" />
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-danger text-[10px] mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div>
                <label class="block text-xs font-heading uppercase tracking-wider text-silver-bright mb-2">
                    Nomor Telepon / WhatsApp
                </label>
                <input type="text" name="phone" value="<?php echo e(old('phone', $customer->user->phone ?? '')); ?>" placeholder="08xxxxxxxxxx" class="w-full bg-steel/30 border border-gunmetal rounded p-2.5 text-xs text-silver-bright font-mono focus:border-giallo focus:outline-none transition-premium" />
                <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-danger text-[10px] mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div>
                <label class="block text-xs font-heading uppercase tracking-wider text-silver-bright mb-2">
                    Kota Domisili
                </label>
                <input type="text" name="city" value="<?php echo e(old('city', $customer->city)); ?>" placeholder="Contoh: Jakarta Selatan" class="w-full bg-steel/30 border border-gunmetal rounded p-2.5 text-xs text-silver-bright focus:border-giallo focus:outline-none transition-premium" />
                <?php $__errorArgs = ['city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-danger text-[10px] mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div>
                <label class="block text-xs font-heading uppercase tracking-wider text-silver-bright mb-2">
                    Provinsi
                </label>
                <input type="text" name="province" value="<?php echo e(old('province', $customer->province)); ?>" placeholder="Contoh: DKI Jakarta" class="w-full bg-steel/30 border border-gunmetal rounded p-2.5 text-xs text-silver-bright focus:border-giallo focus:outline-none transition-premium" />
                <?php $__errorArgs = ['province'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-danger text-[10px] mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div>
                <label class="block text-xs font-heading uppercase tracking-wider text-silver-bright mb-2">
                    Loyalty Points VIP
                </label>
                <input type="number" name="loyalty_points" value="<?php echo e(old('loyalty_points', $customer->loyalty_points)); ?>" min="0" class="w-full bg-steel/30 border border-gunmetal rounded p-2.5 text-xs text-giallo font-mono font-bold focus:border-giallo focus:outline-none transition-premium" />
                <?php $__errorArgs = ['loyalty_points'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-danger text-[10px] mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div>
                <label class="block text-xs font-heading uppercase tracking-wider text-silver-dim mb-2">
                    Email Akun (Terkunci)
                </label>
                <input type="email" value="<?php echo e($customer->user->email ?? '-'); ?>" disabled class="w-full bg-steel/10 border border-gunmetal/40 rounded p-2.5 text-xs text-silver-dim cursor-not-allowed" />
            </div>
        </div>

        
        <div>
            <label class="block text-xs font-heading uppercase tracking-wider text-silver-bright mb-2">
                Alamat Domisili Lengkap
            </label>
            <textarea name="address" rows="3" class="w-full bg-steel/30 border border-gunmetal rounded p-2.5 text-xs text-silver-bright focus:border-giallo focus:outline-none transition-premium"><?php echo e(old('address', $customer->address)); ?></textarea>
            <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="text-danger text-[10px] mt-1"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="pt-4 border-t border-gunmetal flex items-center justify-end gap-3">
            <a href="<?php echo e(route('admin.customers.show', $customer)); ?>" class="px-5 py-2.5 bg-steel/30 hover:bg-steel/60 text-silver-dim hover:text-silver-bright border border-gunmetal font-heading text-xs uppercase tracking-wider rounded transition-premium">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-giallo hover:bg-arancio text-carbon font-heading text-xs font-bold uppercase tracking-wider rounded transition-premium">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\resources\views/admin/customers/edit.blade.php ENDPATH**/ ?>