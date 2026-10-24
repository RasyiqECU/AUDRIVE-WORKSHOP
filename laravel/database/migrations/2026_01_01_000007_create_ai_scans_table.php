<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('uploaded_by')->constrained('users')->restrictOnDelete();
            $table->string('audio_path');
            $table->string('status', 20)->default('pending');
            $table->string('predicted_class')->nullable();
            $table->decimal('confidence', 5, 4)->nullable();
            $table->json('recommendations')->nullable();
            $table->string('spectrogram_path')->nullable();
            $table->text('technician_review')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('customer_id');
            $table->index('predicted_class');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_scans');
    }
};