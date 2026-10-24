<?php $__env->startSection('title', 'Review AI Scan #' . $aiScan->id); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">

    
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">
                Review AI Scan <span class="text-giallo">#<?php echo e($aiScan->id); ?></span>
            </h1>
            <p class="text-sm text-silver-dim mt-2">
                <?php echo e($aiScan->created_at?->format('d M Y, H:i') ?? '-'); ?>

                <?php if($aiScan->is_confirmed): ?>
                    • <span class="text-verde font-heading text-xs uppercase">✅ Sudah Dikonfirmasi Mekanik</span>
                <?php else: ?>
                    • <span class="text-silver-dim font-heading text-xs uppercase">💬 Belum Ada Review Mekanik</span>
                <?php endif; ?>
            </p>
        </div>
        <a href="<?php echo e(route('admin.ai-scans.index')); ?>" class="px-4 py-2 border border-gunmetal text-silver-dim font-heading text-xs uppercase tracking-wider hover:border-silver-dim transition-premium">
            ← Kembali
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="border border-verde/50 bg-verde/10 px-4 py-3 text-verde text-sm font-body">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>


    
    <div class="grid md:grid-cols-2 gap-4">
        <div class="panel border border-gunmetal p-5">
            <h2 class="font-heading text-xs uppercase tracking-wider text-silver-bright mb-3">👤 Customer</h2>
            <?php if($aiScan->customer): ?>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="font-body text-xs text-silver-dim">Nama:</span>
                    <span class="font-heading text-xs text-silver-bright"><?php echo e($aiScan->customer->user?->name ?? '-'); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="font-body text-xs text-silver-dim">Email:</span>
                    <span class="font-heading text-xs text-silver-bright"><?php echo e($aiScan->customer->user?->email ?? '-'); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="font-body text-xs text-silver-dim">ID:</span>
                    <span class="font-mono text-xs text-silver-bright"><?php echo e($aiScan->customer->code ?? '-'); ?></span>
                </div>
            </div>
            <?php else: ?>
                <p class="text-xs text-silver-dim italic">Data customer tidak ditemukan.</p>
            <?php endif; ?>
        </div>

        <?php if($aiScan->vehicle): ?>
        <div class="panel border border-gunmetal p-5">
            <h2 class="font-heading text-xs uppercase tracking-wider text-silver-bright mb-3">🚗 Kendaraan</h2>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="font-body text-xs text-silver-dim">Unit:</span>
                    <span class="font-heading text-xs text-silver-bright"><?php echo e($aiScan->vehicle->brand); ?> <?php echo e($aiScan->vehicle->model); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="font-body text-xs text-silver-dim">Plat:</span>
                    <span class="font-mono text-xs text-silver-bright"><?php echo e($aiScan->vehicle->plate_number); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="font-body text-xs text-silver-dim">Tahun:</span>
                    <span class="font-heading text-xs text-silver-bright"><?php echo e($aiScan->vehicle->year); ?></span>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>


    
    <div class="panel border border-gunmetal p-5">
        <h2 class="font-heading text-xs uppercase tracking-wider text-silver-bright mb-3">🎵 Audio Rekaman — Dengarkan Sebelum Review</h2>
        <audio controls class="w-full">
            <source src="<?php echo e(asset('storage/' . $aiScan->audio_path)); ?>" type="audio/mpeg">
            Browser tidak mendukung pemutar audio.
        </audio>
        <?php if($aiScan->quality_warning): ?>
            <p class="text-xs text-giallo mt-2">⚠️ <?php echo e($aiScan->quality_warning); ?></p>
        <?php endif; ?>
        <?php if($aiScan->quality_score): ?>
            <p class="text-xs text-silver-dim mt-1">Skor kualitas audio: <?php echo e(round((float)$aiScan->quality_score * 100)); ?>%</p>
        <?php endif; ?>
    </div>


    
    
    
    <div class="grid lg:grid-cols-2 gap-4">

        
        <div class="panel border border-giallo/30 overflow-hidden">
            <div class="bg-giallo/10 px-5 py-3 border-b border-giallo/30 flex items-center gap-2">
                <span class="text-giallo text-lg">🤖</span>
                <div>
                    <h2 class="font-heading text-xs uppercase tracking-wider text-giallo">Hasil Diagnosa AI (Otomatis)</h2>
                    <p class="text-[10px] text-silver-dim mt-0.5">Prediksi awal — memerlukan verifikasi mekanik</p>
                </div>
            </div>

            <div class="p-5 space-y-4">
                
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="px-2 py-0.5 text-[9px] font-heading uppercase tracking-wider border <?php echo e($aiScan->getStatusColor()); ?> bg-steel/30">
                        <?php echo e($aiScan->getStatusLabel()); ?>

                    </span>
                    <?php if($aiScan->urgency_level): ?>
                    <span class="px-2 py-0.5 text-[9px] font-heading uppercase tracking-wider border
                        <?php echo e(match($aiScan->urgency_level) {
                            'safe'      => 'text-verde border-verde bg-verde/10',
                            'monitor'   => 'text-giallo border-giallo bg-giallo/10',
                            'urgent'    => 'text-arancio border-arancio bg-arancio/10',
                            'emergency' => 'text-danger border-danger bg-danger/10',
                            default     => 'text-silver-dim border-silver-dim'
                        }); ?>">
                        <?php echo e(match($aiScan->urgency_level) { 'safe'=>'✅ Aman', 'monitor'=>'⚠️ Pantau', 'urgent'=>'🔶 Segera', 'emergency'=>'🚨 Darurat', default=>'?' }); ?>

                    </span>
                    <?php endif; ?>
                </div>

                
                <?php if($aiScan->predicted_class): ?>
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1">Diagnosa Terdeteksi</p>
                    <p class="font-display text-lg text-silver-bright">
                        <?php echo e($aiScan->class_label ?? str_replace('_', ' ', ucfirst($aiScan->predicted_class))); ?>

                    </p>
                </div>
                <?php endif; ?>

                
                <?php if($aiScan->confidence): ?>
                <div class="flex items-center gap-3">
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim">Kepercayaan AI:</p>
                    <div class="flex-1 bg-steel/30 h-2 rounded-full overflow-hidden">
                        <div class="h-full rounded-full <?php echo e($aiScan->confidence >= 0.8 ? 'bg-verde' : ($aiScan->confidence >= 0.6 ? 'bg-giallo' : 'bg-danger')); ?>"
                             style="width: <?php echo e(round($aiScan->confidence * 100)); ?>%"></div>
                    </div>
                    <span class="font-mono text-xs <?php echo e($aiScan->confidence >= 0.8 ? 'text-verde' : ($aiScan->confidence >= 0.6 ? 'text-giallo' : 'text-danger')); ?>">
                        <?php echo e(round($aiScan->confidence * 100)); ?>%
                    </span>
                </div>
                <?php endif; ?>

                
                <?php if($aiScan->wave_analysis): ?>
                <div class="bg-steel/20 border border-gunmetal/50 p-3">
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-2">🔊 Analisis Gelombang Suara</p>
                    <p class="font-body text-xs text-silver-bright leading-relaxed">
                        <?php echo e($aiScan->wave_analysis['pattern_description'] ?? '-'); ?>

                    </p>
                    <?php if(!empty($aiScan->wave_analysis['anomaly_details'])): ?>
                        <p class="font-mono text-[10px] text-silver-dim mt-2"><?php echo e($aiScan->wave_analysis['anomaly_details']); ?></p>
                    <?php endif; ?>
                    <?php if(!empty($aiScan->wave_analysis['dominant_frequency_hz'])): ?>
                        <p class="font-mono text-[10px] text-silver-dim mt-1">
                            Frekuensi dominan: <?php echo e($aiScan->wave_analysis['dominant_frequency_hz']); ?> Hz
                        </p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                
                <?php if($aiScan->mechanic_ai_notes): ?>
                <div class="bg-giallo/5 border border-giallo/20 p-3">
                    <p class="font-heading text-[10px] uppercase tracking-wider text-giallo mb-2">📋 Catatan Teknis AI (untuk Mekanik)</p>
                    <p class="font-body text-xs text-silver-dim leading-relaxed"><?php echo e($aiScan->mechanic_ai_notes); ?></p>
                </div>
                <?php endif; ?>

                
                
                <?php if($aiScan->spectrogram_path): ?>
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-2">Spektogram Mel</p>
                    <img src="<?php echo e(asset('storage/' . $aiScan->spectrogram_path)); ?>"
                         alt="Mel-Spectrogram" class="w-full border border-gunmetal">
                </div>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="panel border <?php echo e($aiScan->is_confirmed ? 'border-verde/40' : 'border-gunmetal'); ?> overflow-hidden">
            <div class="bg-<?php echo e($aiScan->is_confirmed ? 'verde' : 'steel'); ?>/10 px-5 py-3 border-b border-<?php echo e($aiScan->is_confirmed ? 'verde/40' : 'gunmetal'); ?> flex items-center gap-2">
                <span class="text-lg"><?php echo e($aiScan->is_confirmed ? '🔧' : '⏳'); ?></span>
                <div>
                    <h2 class="font-heading text-xs uppercase tracking-wider <?php echo e($aiScan->is_confirmed ? 'text-verde' : 'text-silver-bright'); ?>">
                        Hasil Diagnosa Manual
                    </h2>
                    <p class="text-[10px] text-silver-dim mt-0.5">
                        <?php echo e($aiScan->is_confirmed ? 'Dikonfirmasi oleh: ' . ($aiScan->reviewer?->name ?? 'Mekanik') : 'Belum direview oleh mekanik'); ?>

                    </p>
                </div>
            </div>

            <?php if($aiScan->is_confirmed): ?>
            
            <div class="p-5 space-y-4">
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1">Keputusan Mekanik</p>
                    <p class="font-display text-lg text-verde">
                        <?php echo e($aiScan->mechanic_decision
                            ? str_replace('_', ' ', ucfirst($aiScan->mechanic_decision))
                            : '✅ Setuju dengan hasil AI'); ?>

                    </p>
                </div>
                <?php if($aiScan->final_urgency): ?>
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1">Urgensi Final</p>
                    <span class="px-2 py-0.5 text-[9px] font-heading uppercase tracking-wider border <?php echo e($aiScan->getUrgencyColor()); ?>">
                        <?php echo e($aiScan->getUrgencyIcon()); ?> <?php echo e($aiScan->getUrgencyLabel()); ?>

                    </span>
                </div>
                <?php endif; ?>
                <?php if($aiScan->technician_review): ?>
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1">Review Mekanik</p>
                    <p class="font-body text-xs text-silver-bright leading-relaxed"><?php echo e($aiScan->technician_review); ?></p>
                </div>
                <?php endif; ?>
                <?php if($aiScan->mechanic_final_notes): ?>
                <div>
                    <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1">Catatan Final</p>
                    <p class="font-body text-xs text-silver-bright leading-relaxed"><?php echo e($aiScan->mechanic_final_notes); ?></p>
                </div>
                <?php endif; ?>
                <div class="text-right">
                    <p class="font-mono text-[10px] text-silver-dim">
                        Direview: <?php echo e($aiScan->reviewed_at?->format('d M Y, H:i') ?? '-'); ?>

                    </p>
                </div>
            </div>

            <?php else: ?>
            
            <form action="<?php echo e(route('admin.ai-scans.review', ['aiScan' => $aiScan->id])); ?>" method="POST" class="p-5 space-y-4">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PATCH'); ?>

                <div class="bg-steel/20 border border-gunmetal/30 p-3 text-xs text-silver-dim">
                    ℹ️ Hasil AI sudah langsung diterima customer. Review Anda bersifat <strong>opsional</strong> — gunakan untuk mengoreksi atau mengkonfirmasi diagnosa AI berdasarkan keahlian Anda.
                </div>

                
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">Tindakan</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-2 cursor-pointer border border-gunmetal p-3 hover:border-verde/50 transition-premium has-[:checked]:border-verde">
                            <input type="radio" name="action" value="approve" checked class="accent-verde">
                            <div>
                                <p class="font-heading text-xs text-verde">✅ Setujui Hasil AI</p>
                                <p class="font-body text-[10px] text-silver-dim">Konfirmasi prediksi AI sudah benar</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer border border-gunmetal p-3 hover:border-arancio/50 transition-premium has-[:checked]:border-arancio">
                            <input type="radio" name="action" value="override" class="accent-arancio">
                            <div>
                                <p class="font-heading text-xs text-arancio">✏️ Koreksi Hasil AI</p>
                                <p class="font-body text-[10px] text-silver-dim">Ganti dengan diagnosa manual</p>
                            </div>
                        </label>
                    </div>
                </div>

                
                <div id="override-fields" class="space-y-3 hidden">
                    <div>
                        <label class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">Koreksi Diagnosa</label>
                        <select name="mechanic_decision" class="w-full bg-nero border border-gunmetal px-4 py-2.5 text-silver-bright text-sm focus:border-arancio focus:outline-none">
                            <option value="">— Pilih diagnosa yang benar —</option>
                            <option value="normal">Mesin Normal</option>
                            <option value="timing_chain_belt">Kerusakan Timing Chain / Belt</option>
                            <option value="valve_lifter">Kerusakan Katup / Lifter</option>
                            <option value="rod_knock">Rod Knock / Bantalan Mesin</option>
                            <option value="combustion_misfire">Masalah Pembakaran / Misfire</option>
                            <option value="exhaust_leak">Kebocoran Knalpot</option>
                            <option value="bearing_alternator">Bearing Alternator / Pulley</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">Urgensi Hasil Koreksi</label>
                        <select name="final_urgency" class="w-full bg-nero border border-gunmetal px-4 py-2.5 text-silver-bright text-sm focus:border-arancio focus:outline-none">
                            <option value="safe">✅ Aman</option>
                            <option value="monitor">⚠️ Perlu Dipantau</option>
                            <option value="urgent" selected>🔶 Perlu Segera ke Bengkel</option>
                            <option value="emergency">🚨 Darurat</option>
                        </select>
                    </div>
                </div>

                
                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">
                        Review / Temuan Mekanik <span class="text-danger">*</span>
                    </label>
                    <textarea name="technician_review" rows="4" required
                        class="w-full bg-nero border border-gunmetal px-4 py-2.5 text-silver-bright text-sm focus:border-giallo focus:outline-none resize-none"
                        placeholder="Deskripsikan temuan saat mendengarkan audio dan pemeriksaan visual..."><?php echo e(old('technician_review', $aiScan->technician_review)); ?></textarea>
                    <?php $__errorArgs = ['technician_review'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-xs text-danger mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block font-heading text-xs uppercase tracking-wider text-silver-dim mb-2">Catatan Tambahan (Opsional)</label>
                    <textarea name="mechanic_final_notes" rows="2"
                        class="w-full bg-nero border border-gunmetal px-4 py-2.5 text-silver-bright text-sm focus:border-giallo focus:outline-none resize-none"
                        placeholder="Rekomendasi tindakan atau informasi tambahan..."></textarea>
                </div>

                <button type="submit" class="w-full px-5 py-3 bg-giallo text-nero font-heading font-bold text-xs uppercase tracking-wider hover:bg-arancio transition-premium">
                    Simpan & Konfirmasi Review
                </button>
            </form>
            <?php endif; ?>
        </div>
    </div>


    
    <?php if($aiScan->recommendations && count($aiScan->recommendations) > 0): ?>
    <div class="panel border border-gunmetal overflow-hidden">
        <div class="bg-steel/30 px-5 py-3 border-b border-gunmetal">
            <h2 class="font-heading text-xs uppercase tracking-wider text-silver-bright">📋 Rekomendasi yang Diterima Customer</h2>
        </div>
        <div class="p-5 space-y-2">
            <?php $__currentLoopData = $aiScan->recommendations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <p class="font-body text-xs text-silver-dim"><?php echo e($rec); ?></p>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

</div>

<script>
    // Toggle override fields
    document.querySelectorAll('input[name="action"]').forEach(radio => {
        radio.addEventListener('change', () => {
            const overrideFields = document.getElementById('override-fields');
            if (overrideFields) {
                overrideFields.classList.toggle('hidden', radio.value !== 'override' || !radio.checked);
            }
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/admin/ai-scans/show.blade.php ENDPATH**/ ?>