<?php $__env->startSection('title', 'Booking Servis Kendaraan — AUDrive'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <div class="mb-6">
        <a href="<?php echo e(route('workshop.index')); ?>" class="text-xs text-giallo hover:underline uppercase tracking-wider font-heading">
            &larr; Kembali ke Workshop
        </a>
        <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright mt-2">FORMULIR BOOKING SERVIS</h1>
        <p class="text-xs text-silver-dim mt-1">Pilih kendaraan, paket servis, dan tentukan jadwal kedatangan Anda.</p>
    </div>

    <?php if(session('error')): ?>
        <div class="mb-6 p-4 rounded bg-rosso/10 border border-rosso/30 text-rosso text-sm">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <div class="bg-nero-dark border border-white/10 rounded-lg p-6 md:p-8 shadow-xl">
        <form action="<?php echo e(route('workshop.booking.store')); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>

            
            <div>
                <label for="customer_vehicle_id" class="block text-xs font-heading font-semibold uppercase tracking-wider text-silver-bright mb-2">
                    Pilih Kendaraan Anda <span class="text-rosso">*</span>
                </label>
                <?php if($vehicles->isEmpty()): ?>
                    <div class="p-4 bg-nero border border-white/10 rounded text-xs text-silver-dim">
                        Anda belum mendaftarkan kendaraan di akun ini.
                        <a href="<?php echo e(route('customer.vehicles.create')); ?>" class="text-giallo hover:underline font-semibold ml-1">
                            + Tambah Kendaraan Baru
                        </a>
                    </div>
                <?php else: ?>
                    <select name="customer_vehicle_id" id="customer_vehicle_id" required class="w-full bg-nero border border-white/15 rounded px-3 py-2.5 text-sm text-silver-bright focus:border-giallo focus:outline-none">
                        <option value="">-- Pilih Kendaraan --</option>
                        <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($vehicle->id); ?>" <?php echo e(old('customer_vehicle_id') == $vehicle->id ? 'selected' : ''); ?>>
                                <?php echo e($vehicle->license_plate); ?> — <?php echo e($vehicle->brand); ?> <?php echo e($vehicle->model); ?> (<?php echo e($vehicle->year); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                <?php endif; ?>
                <?php $__errorArgs = ['customer_vehicle_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-rosso text-xs mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div>
                <label for="service_type" class="block text-xs font-heading font-semibold uppercase tracking-wider text-silver-bright mb-2">
                    Jenis Servis <span class="text-rosso">*</span>
                </label>
                <select name="service_type" id="service_type" required class="w-full bg-nero border border-white/15 rounded px-3 py-2.5 text-sm text-silver-bright focus:border-giallo focus:outline-none">
                    <option value="">-- Pilih Layanan Servis --</option>
                    <option value="regular_service" <?php echo e(old('service_type') === 'regular_service' ? 'selected' : ''); ?>>Servis Berkala (Tune-up & Ganti Oli)</option>
                    <option value="heavy_repair" <?php echo e(old('service_type') === 'heavy_repair' ? 'selected' : ''); ?>>Perbaikan Mesin & Transmisi</option>
                    <option value="body_repair" <?php echo e(old('service_type') === 'body_repair' ? 'selected' : ''); ?>>Body Repair & Pengecatan Oven</option>
                    <option value="ac_service" <?php echo e(old('service_type') === 'ac_service' ? 'selected' : ''); ?>>Servis AC & Sistem Elektrikal</option>
                    <option value="inspection" <?php echo e(old('service_type') === 'inspection' ? 'selected' : ''); ?>>Inspeksi Menyeluruh (150+ Titik)</option>
                </select>
                <?php $__errorArgs = ['service_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-rosso text-xs mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="preferred_date" class="block text-xs font-heading font-semibold uppercase tracking-wider text-silver-bright mb-2">
                        Tanggal Kedatangan <span class="text-rosso">*</span>
                    </label>
                    <input type="date" name="preferred_date" id="preferred_date" min="<?php echo e(date('Y-m-d')); ?>" value="<?php echo e(old('preferred_date', date('Y-m-d'))); ?>" required class="w-full bg-nero border border-white/15 rounded px-3 py-2.5 text-sm text-silver-bright focus:border-giallo focus:outline-none">
                    <?php $__errorArgs = ['preferred_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-rosso text-xs mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label for="preferred_time" class="block text-xs font-heading font-semibold uppercase tracking-wider text-silver-bright mb-2">
                        Jam Kedatangan <span class="text-rosso">*</span>
                    </label>
                    <select name="preferred_time" id="preferred_time" required class="w-full bg-nero border border-white/15 rounded px-3 py-2.5 text-sm text-silver-bright focus:border-giallo focus:outline-none">
                        <option value="08:00" <?php echo e(old('preferred_time') === '08:00' ? 'selected' : ''); ?>>08:00 WIB</option>
                        <option value="09:00" <?php echo e(old('preferred_time') === '09:00' ? 'selected' : ''); ?>>09:00 WIB</option>
                        <option value="10:00" <?php echo e(old('preferred_time') === '10:00' ? 'selected' : ''); ?>>10:00 WIB</option>
                        <option value="11:00" <?php echo e(old('preferred_time') === '11:00' ? 'selected' : ''); ?>>11:00 WIB</option>
                        <option value="13:00" <?php echo e(old('preferred_time') === '13:00' ? 'selected' : ''); ?>>13:00 WIB</option>
                        <option value="14:00" <?php echo e(old('preferred_time') === '14:00' ? 'selected' : ''); ?>>14:00 WIB</option>
                        <option value="15:00" <?php echo e(old('preferred_time') === '15:00' ? 'selected' : ''); ?>>15:00 WIB</option>
                        <option value="16:00" <?php echo e(old('preferred_time') === '16:00' ? 'selected' : ''); ?>>16:00 WIB</option>
                    </select>
                    <?php $__errorArgs = ['preferred_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-rosso text-xs mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            
            <div>
                <label for="notes" class="block text-xs font-heading font-semibold uppercase tracking-wider text-silver-bright mb-2">
                    Keluhan atau Catatan Tambahan (Opsional)
                </label>
                <textarea name="notes" id="notes" rows="4" maxlength="500" placeholder="Jelaskan kendala atau gejala yang dirasakan pada mobil..." class="w-full bg-nero border border-white/15 rounded px-3 py-2 text-sm text-silver-bright focus:border-giallo focus:outline-none"><?php echo e(old('notes')); ?></textarea>
                <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-rosso text-xs mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div class="pt-4 border-t border-white/10 flex items-center justify-end gap-3">
                <a href="<?php echo e(route('workshop.index')); ?>" class="px-5 py-2.5 border border-white/20 rounded text-xs text-silver-bright hover:border-white/40 uppercase tracking-wider font-heading">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-giallo hover:bg-giallo/90 text-nero font-heading font-semibold text-xs uppercase tracking-wider rounded transition-all shadow-md shadow-giallo/20">
                    Konfirmasi Booking Servis
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make(auth()->check() && auth()->user()->role === 'customer' ? 'layouts.customer' : (auth()->check() ? 'layouts.app' : 'layouts.public'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/workshop/create.blade.php ENDPATH**/ ?>