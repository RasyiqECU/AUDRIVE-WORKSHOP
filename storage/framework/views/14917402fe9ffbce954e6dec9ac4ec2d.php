<?php $__env->startSection('title', 'Hasil AI Scan #' . $aiScan->id); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">

    
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-silver-bright tracking-wide">
                Hasil Analisis Suara Mesin
            </h1>
            <p class="text-sm text-silver-dim mt-2">
                Scan ID: #<?php echo e($aiScan->id); ?> • <?php echo e($aiScan->created_at?->format('d M Y, H:i') ?? '-'); ?>

            </p>
        </div>
        <a href="<?php echo e(route('customer.ai-scan.create')); ?>" class="px-4 py-2 bg-giallo text-nero font-heading font-bold text-xs uppercase tracking-wider hover:bg-arancio transition-premium clip-angular-button">
            Scan Baru
        </a>
    </div>

    <?php
        $scanStatus     = $aiScan->status->value ?? (string) $aiScan->status;
        $hasAiResult    = !empty($aiScan->predicted_class);
        $hasMechReview  = $aiScan->is_confirmed && ($aiScan->mechanic_decision || $aiScan->technician_review);
        $urgency        = $aiScan->getEffectiveUrgency();

        // Tentukan urgency dari mekanik jika ada, else dari AI
        $urgencyConfig  = [
            'safe'      => ['bg'=>'bg-verde/10',   'border'=>'border-verde/40',   'text'=>'text-verde',   'icon'=>'✅', 'label'=>'Aman — Tidak Perlu ke Bengkel'],
            'monitor'   => ['bg'=>'bg-giallo/10',  'border'=>'border-giallo/40',  'text'=>'text-giallo',  'icon'=>'⚠️', 'label'=>'Perlu Dipantau'],
            'urgent'    => ['bg'=>'bg-arancio/10', 'border'=>'border-arancio/40', 'text'=>'text-arancio', 'icon'=>'🔶', 'label'=>'Perlu Segera ke Bengkel'],
            'emergency' => ['bg'=>'bg-danger/10',  'border'=>'border-danger/40',  'text'=>'text-danger',  'icon'=>'🚨', 'label'=>'DARURAT — Hentikan Kendaraan'],
        ][$urgency] ?? ['bg'=>'bg-steel/20','border'=>'border-gunmetal','text'=>'text-silver-dim','icon'=>'❓','label'=>'Tidak Diketahui'];
    ?>


    
    <?php if(in_array($scanStatus, ['pending', 'processing'])): ?>
        <div class="bg-giallo/5 border border-giallo/30 p-4 flex items-center gap-3">
            <div class="w-4 h-4 border-2 border-giallo border-t-transparent rounded-full animate-spin shrink-0"></div>
            <div>
                <p class="font-heading text-sm text-giallo">Sedang Menganalisis Audio...</p>
                <p class="text-xs text-silver-dim">AI sedang memproses rekaman suara mesin Anda.</p>
            </div>
        </div>
        <script>setTimeout(() => location.reload(), 5000);</script>

    <?php elseif($scanStatus === 'failed' && !$hasMechReview): ?>
        <div class="bg-danger/5 border border-danger/30 p-4 flex items-center gap-3">
            <span class="text-danger text-2xl shrink-0">⚠️</span>
            <div>
                <p class="font-heading text-sm text-danger">Analisis AI Gagal</p>
                <?php if($aiScan->quality_warning): ?>
                    <p class="text-xs text-silver-dim mt-1"><?php echo e($aiScan->quality_warning); ?></p>
                <?php else: ?>
                    <p class="text-xs text-silver-dim">Terjadi kesalahan saat memproses audio. Silakan coba unggah ulang.</p>
                <?php endif; ?>
                <a href="<?php echo e(route('customer.ai-scan.create')); ?>" class="inline-block mt-2 text-xs text-giallo underline">
                    Coba Lagi →
                </a>
            </div>
        </div>
    <?php endif; ?>


    
    <?php if($aiScan->vehicle): ?>
    <div class="panel border border-gunmetal p-4 flex items-center gap-4">
        <span class="text-2xl">🚗</span>
        <div>
            <p class="font-heading text-sm text-silver-bright"><?php echo e($aiScan->vehicle->brand); ?> <?php echo e($aiScan->vehicle->model); ?></p>
            <p class="font-mono text-xs text-silver-dim"><?php echo e($aiScan->vehicle->plate_number); ?> • <?php echo e($aiScan->vehicle->year); ?></p>
        </div>
    </div>
    <?php endif; ?>


    
    <?php if($hasAiResult || $hasMechReview): ?>

        
        <?php if($hasMechReview || $hasAiResult): ?>
        <div class="<?php echo e($urgencyConfig['bg']); ?> <?php echo e($urgencyConfig['border']); ?> border p-4 flex items-center gap-3">
            <span class="text-2xl shrink-0"><?php echo e($urgencyConfig['icon']); ?></span>
            <div>
                <p class="font-heading text-sm <?php echo e($urgencyConfig['text']); ?> uppercase tracking-wider">
                    <?php echo e($urgencyConfig['label']); ?>

                    <?php if($hasMechReview): ?>
                        <span class="text-[10px] text-silver-dim normal-case tracking-normal ml-2">(dikonfirmasi mekanik)</span>
                    <?php endif; ?>
                </p>
                <p class="text-xs text-silver-dim mt-0.5"><?php echo e($aiScan->getUrgencyAction()); ?></p>
            </div>
        </div>
        <?php endif; ?>


        
        <?php
            // 2 kolom jika ada keduanya, 1 kolom jika hanya 1
            $gridClass = ($hasAiResult && $hasMechReview) ? 'lg:grid-cols-2' : 'grid-cols-1';
        ?>
        <div class="grid <?php echo e($gridClass); ?> gap-4">

            
            <?php if($hasAiResult): ?>
            <div class="panel border border-giallo/30 overflow-hidden">
                <div class="bg-giallo/10 px-5 py-3 border-b border-giallo/30 flex items-center gap-2">
                    <span class="text-xl">🤖</span>
                    <div>
                        <h2 class="font-heading text-xs uppercase tracking-wider text-giallo">Diagnosa AI (Otomatis)</h2>
                        <p class="text-[10px] text-silver-dim">Hasil analisis gelombang suara oleh sistem AI</p>
                    </div>
                </div>
                <div class="p-5 space-y-5">

                    
                    <div class="flex items-center gap-5">
                        <div class="flex-1">
                            <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1">Terdeteksi</p>
                            <p class="font-display text-xl text-silver-bright">
                                <?php echo e($aiScan->class_label ?? str_replace('_', ' ', ucfirst($aiScan->predicted_class))); ?>

                            </p>
                        </div>
                        <?php if($aiScan->confidence): ?>
                        <div class="text-center shrink-0">
                            <div class="relative w-16 h-16">
                                <svg class="w-16 h-16 transform -rotate-90" viewBox="0 0 36 36">
                                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                          fill="none" stroke="#24272E" stroke-width="3"/>
                                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                          fill="none" stroke-width="3" stroke-linecap="round"
                                          class="<?php echo e($aiScan->confidence >= 0.8 ? 'text-verde' : ($aiScan->confidence >= 0.6 ? 'text-giallo' : 'text-danger')); ?>"
                                          stroke="currentColor"
                                          stroke-dasharray="<?php echo e($aiScan->confidence * 100); ?>, 100"/>
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="font-mono text-xs font-bold <?php echo e($aiScan->confidence >= 0.8 ? 'text-verde' : ($aiScan->confidence >= 0.6 ? 'text-giallo' : 'text-danger')); ?>">
                                        <?php echo e(round($aiScan->confidence * 100)); ?>%
                                    </span>
                                </div>
                            </div>
                            <p class="font-heading text-[9px] uppercase text-silver-dim mt-1">Kepercayaan</p>
                        </div>
                        <?php endif; ?>
                    </div>

                    
                    <?php if($aiScan->wave_analysis): ?>
                    <div class="bg-steel/20 border border-gunmetal/50 p-3">
                        <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-2">🔊 Analisis Gelombang Suara</p>
                        <p class="font-body text-xs text-silver-bright leading-relaxed">
                            <?php echo e($aiScan->wave_analysis['pattern_description'] ?? ''); ?>

                        </p>
                        <?php if(!empty($aiScan->wave_analysis['dominant_frequency_hz'])): ?>
                        <p class="font-mono text-[10px] text-silver-dim mt-2">
                            Frekuensi dominan: <?php echo e($aiScan->wave_analysis['dominant_frequency_hz']); ?> Hz
                        </p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    
                    <?php if($aiScan->recommendations && count($aiScan->recommendations) > 0): ?>
                    <div>
                        <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-2">💡 Rekomendasi AI</p>
                        <div class="space-y-2">
                            <?php $__currentLoopData = $aiScan->recommendations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-start gap-2 bg-steel/20 p-2.5">
                                <span class="text-giallo text-xs shrink-0">›</span>
                                <span class="font-body text-xs text-silver-bright"><?php echo e($rec); ?></span>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    
                    <?php if($aiScan->spectrogram_path): ?>
                    <div>
                        <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-2">Spektogram Mel</p>
                        <img src="<?php echo e(asset('storage/' . $aiScan->spectrogram_path)); ?>"
                             alt="Mel-Spectrogram" class="w-full border border-gunmetal">
                    </div>
                    <?php endif; ?>

                    <?php if($aiScan->confidence && $aiScan->confidence < 0.6): ?>
                    <p class="text-[10px] text-silver-dim/70 italic">
                        * Kepercayaan AI di bawah 60%. Lihat konfirmasi mekanik jika tersedia.
                    </p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>


            
            <?php if($hasMechReview): ?>
            <div class="panel border border-verde/40 overflow-hidden">
                <div class="bg-verde/10 px-5 py-3 border-b border-verde/40 flex items-center gap-2">
                    <span class="text-xl">🔧</span>
                    <div>
                        <h2 class="font-heading text-xs uppercase tracking-wider text-verde">Diagnosa Mekanik</h2>
                        <p class="text-[10px] text-silver-dim">
                            Oleh: <?php echo e($aiScan->reviewer?->name ?? 'Mekanik AUDrive'); ?>

                            • <?php echo e($aiScan->reviewed_at?->format('d M Y') ?? '-'); ?>

                        </p>
                    </div>
                </div>

                <div class="p-5 space-y-4">
                    
                    <div>
                        <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1">Hasil Diagnosa Manual</p>
                        <?php
                            $mechClass = $aiScan->mechanic_decision;
                            $mechLabels = [
                                'normal'             => 'Mesin Normal',
                                'timing_chain_belt'  => 'Kerusakan Timing Chain / Belt',
                                'valve_lifter'       => 'Kerusakan Katup / Lifter',
                                'rod_knock'          => 'Rod Knock / Bantalan Mesin',
                                'combustion_misfire' => 'Masalah Pembakaran / Misfire',
                                'exhaust_leak'       => 'Kebocoran Knalpot',
                                'bearing_alternator' => 'Bearing Alternator / Pulley',
                            ];
                            $mechLabel = $mechLabels[$mechClass] ?? str_replace('_', ' ', ucfirst($mechClass ?? ''));
                        ?>

                        <?php if($mechClass): ?>
                            
                            <?php if($hasAiResult && $mechClass !== $aiScan->predicted_class): ?>
                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                <span class="text-[10px] text-silver-dim/60 line-through">
                                    <?php echo e($aiScan->class_label ?? str_replace('_', ' ', ucfirst($aiScan->predicted_class))); ?>

                                </span>
                                <span class="text-silver-dim text-xs">→</span>
                            </div>
                            <p class="font-display text-xl text-verde"><?php echo e($mechLabel); ?></p>
                            <p class="text-[10px] text-arancio mt-1">✏️ Mekanik mengoreksi hasil AI</p>
                            <?php else: ?>
                            <p class="font-display text-xl text-verde"><?php echo e($mechLabel ?: '—'); ?></p>
                            <?php if($hasAiResult): ?>
                                <p class="text-[10px] text-verde mt-1">✅ Mekanik mengkonfirmasi hasil AI</p>
                            <?php endif; ?>
                            <?php endif; ?>
                        <?php else: ?>
                            
                            <?php if($hasAiResult): ?>
                                <p class="font-display text-xl text-verde">
                                    <?php echo e($aiScan->class_label ?? str_replace('_', ' ', ucfirst($aiScan->predicted_class))); ?>

                                </p>
                                <p class="text-[10px] text-verde mt-1">✅ Mekanik mengkonfirmasi hasil AI</p>
                            <?php else: ?>
                                <p class="font-display text-xl text-verde">Review Tersedia</p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    
                    <?php if($aiScan->final_urgency): ?>
                    <div>
                        <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1">Urgensi (Dikonfirmasi Mekanik)</p>
                        <span class="px-2 py-1 text-[9px] font-heading uppercase tracking-wider border <?php echo e($aiScan->getUrgencyColor()); ?>">
                            <?php echo e($aiScan->getUrgencyIcon()); ?> <?php echo e($aiScan->getUrgencyLabel()); ?>

                        </span>
                    </div>
                    <?php endif; ?>

                    
                    <?php if($aiScan->technician_review): ?>
                    <div class="bg-verde/5 border border-verde/20 p-3">
                        <p class="font-heading text-[10px] uppercase tracking-wider text-verde mb-2">📝 Catatan Mekanik</p>
                        <p class="font-body text-xs text-silver-bright leading-relaxed"><?php echo e($aiScan->technician_review); ?></p>
                    </div>
                    <?php endif; ?>

                    
                    <?php if($aiScan->mechanic_final_notes): ?>
                    <div>
                        <p class="font-heading text-[10px] uppercase tracking-wider text-silver-dim mb-1">Rekomendasi Tambahan</p>
                        <p class="font-body text-xs text-silver-dim leading-relaxed"><?php echo e($aiScan->mechanic_final_notes); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>


        
        <?php if(!$hasAiResult && $hasMechReview): ?>
        <div class="bg-steel/20 border border-gunmetal/50 p-4 flex items-start gap-3">
            <span class="text-silver-dim text-lg shrink-0">ℹ️</span>
            <p class="text-xs text-silver-dim">
                Hasil diagnosa AI belum tersedia untuk rekaman ini. Diagnosa di atas merupakan hasil pemeriksaan manual oleh mekanik kami.
            </p>
        </div>
        <?php endif; ?>

        
        <?php if($hasAiResult && !$hasMechReview): ?>
        <div class="bg-steel/20 border border-gunmetal/50 p-4 flex items-start gap-3">
            <span class="text-lg shrink-0">ℹ️</span>
            <p class="text-xs text-silver-dim">
                Hasil di atas merupakan analisis otomatis dari AI. Tim mekanik kami dapat memberikan konfirmasi tambahan — hasil review mekanik akan muncul di sini secara otomatis jika tersedia.
            </p>
        </div>
        <?php endif; ?>


        
        <?php if(in_array($urgency, ['urgent', 'emergency'])): ?>
        <div class="panel border border-arancio/40 p-5 text-center bg-arancio/5">
            <p class="font-heading text-sm text-arancio mb-1"><?php echo e($urgencyConfig['icon']); ?> Tindakan Segera Diperlukan</p>
            <p class="text-xs text-silver-dim mb-4"><?php echo e($urgencyConfig['label']); ?></p>
            <a href="<?php echo e(route('workshop.booking.create')); ?>" class="inline-block px-6 py-2.5 bg-arancio text-nero font-heading font-bold text-xs uppercase tracking-wider hover:bg-danger transition-premium clip-angular-button">
                Booking Servis Sekarang
            </a>
        </div>
        <?php elseif($urgency === 'monitor'): ?>
        <div class="panel border border-gunmetal p-5 text-center">
            <p class="font-heading text-sm text-silver-bright mb-3">Butuh pemeriksaan lebih lanjut?</p>
            <a href="<?php echo e(route('workshop.booking.create')); ?>" class="inline-block px-6 py-2.5 bg-giallo text-nero font-heading font-bold text-xs uppercase tracking-wider hover:bg-arancio transition-premium clip-angular-button">
                Booking Servis
            </a>
        </div>
        <?php endif; ?>

    <?php endif; ?>


    
    <div class="panel border border-gunmetal p-5">
        <h2 class="font-heading text-xs uppercase tracking-wider text-silver-bright mb-3">🎵 Audio Rekaman Anda</h2>
        <audio controls class="w-full">
            <source src="<?php echo e(asset('storage/' . $aiScan->audio_path)); ?>" type="audio/mpeg">
            Browser Anda tidak mendukung pemutar audio.
        </audio>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.customer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pongo\Documents\audrive\laravel\resources\views/customer/ai-scan/show.blade.php ENDPATH**/ ?>