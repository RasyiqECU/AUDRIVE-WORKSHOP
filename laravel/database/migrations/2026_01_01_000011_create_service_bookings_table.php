<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_vehicle_id')->constrained()->cascadeOnDelete();
            $table->string('service_type', 50);
            $table->date('preferred_date');
            $table->time('preferred_time');
            $table->string('status', 20)->default('pending');
            $table->unsignedBigInteger('estimated_cost_idr')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('customer_id');
            $table->index('preferred_date');
            $table->index('service_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_bookings');
    }
};