<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['service', 'part', 'other']);
            $table->string('description');
            $table->foreignId('spare_part_id')->nullable();
            $table->unsignedInteger('qty')->default(1);
            $table->unsignedBigInteger('unit_price_idr');
            $table->unsignedBigInteger('subtotal_idr');
            $table->timestamps();

            $table->index('work_order_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_order_items');
    }
};