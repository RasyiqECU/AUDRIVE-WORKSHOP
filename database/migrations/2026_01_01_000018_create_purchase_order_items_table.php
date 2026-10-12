<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('spare_part_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('qty');
            $table->unsignedBigInteger('cost_idr');
            $table->unsignedBigInteger('subtotal_idr');
            $table->unsignedInteger('received_qty')->default(0);
            $table->timestamps();


            $table->index('purchase_order_id');
            $table->index('spare_part_id');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};

