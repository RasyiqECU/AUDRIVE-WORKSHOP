<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_scans', function (Blueprint $table) {
            // ── Langkah 3: Hasil Diagnostik AI ──────────────────────────────
            $table->string('class_label')->nullable()->after('predicted_class');
            $table->string('urgency_level', 20)->nullable()->after('confidence');  // safe|monitor|urgent|emergency
            $table->text('wave_analysis')->nullable()->after('urgency_level');     // JSON: deskripsi gelombang
            $table->string('quality_score', 10)->nullable()->after('wave_analysis');
            $table->text('quality_warning')->nullable()->after('quality_score');
            $table->text('ai_diagnostic_notes')->nullable()->after('quality_warning'); // catatan AI untuk customer
            $table->text('mechanic_ai_notes')->nullable()->after('ai_diagnostic_notes'); // catatan teknis untuk mekanik

            // ── Langkah 4: Manual Review (Dual Diagnosis) ────────────────────
            $table->string('mechanic_decision')->nullable()->after('technician_review');  // kelas hasil koreksi mekanik
            $table->text('mechanic_final_notes')->nullable()->after('mechanic_decision'); // catatan final mekanik
            $table->string('admin_decision')->nullable()->after('mechanic_final_notes');  // keputusan super admin
            $table->text('admin_notes')->nullable()->after('admin_decision');
            $table->string('final_diagnosis')->nullable()->after('admin_notes');          // diagnosa final yang ditampilkan
            $table->string('final_urgency', 20)->nullable()->after('final_diagnosis');    // urgensi final
            $table->boolean('is_confirmed')->default(false)->after('final_urgency');     // sudah dikonfirmasi manual
        });
    }

    public function down(): void
    {
        Schema::table('ai_scans', function (Blueprint $table) {
            $table->dropColumn([
                'class_label', 'urgency_level', 'wave_analysis',
                'quality_score', 'quality_warning', 'ai_diagnostic_notes', 'mechanic_ai_notes',
                'mechanic_decision', 'mechanic_final_notes',
                'admin_decision', 'admin_notes',
                'final_diagnosis', 'final_urgency', 'is_confirmed',
            ]);
        });
    }
};
