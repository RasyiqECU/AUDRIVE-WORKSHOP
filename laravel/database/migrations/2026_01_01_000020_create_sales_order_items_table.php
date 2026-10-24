<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->constrained()->cascadeOnDelete();
            $table->string('item_type', 20);
            $table->foreignId('car_unit_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('spare_part_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description');
            $table->unsignedInteger('qty')->default(1);
            $table->unsignedBigInteger('unit_price_idr');
            $table->unsignedBigInteger('subtotal_idr');
            $table->timestamps();


            $table->index('sales_order_id');
            $table->index('item_type');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('sales_order_items');
    }
};

