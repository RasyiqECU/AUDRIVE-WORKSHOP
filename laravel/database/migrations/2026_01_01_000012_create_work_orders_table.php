<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->foreignId('service_booking_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_vehicle_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('booking');
            $table->foreignId('assigned_mechanic_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('total_jasa_idr')->default(0);
            $table->unsignedBigInteger('total_part_idr')->default(0);
            $table->unsignedBigInteger('total_idr')->default(0);
            $table->timestamps();

            $table->index('code');
            $table->index('status');
            $table->index('assigned_mechanic_id');
            $table->index('customer_vehicle_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};