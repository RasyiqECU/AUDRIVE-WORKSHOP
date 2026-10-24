<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_unit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inspector_id')->constrained('users')->restrictOnDelete();
            $table->json('checklist');
            $table->text('notes')->nullable();
            $table->foreignId('ai_scan_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('result', ['pass', 'fail', 'conditional'])->default('conditional');
            $table->timestamp('inspected_at')->nullable();
            $table->timestamps();

            $table->index('car_unit_id');
            $table->index('result');
            $table->index('inspected_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_inspections');
    }
};