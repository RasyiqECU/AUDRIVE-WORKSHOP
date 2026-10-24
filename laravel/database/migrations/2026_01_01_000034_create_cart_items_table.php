<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('spare_part_id')->constrained()->cascadeOnDelete();
            $table->integer('qty')->default(1);
            $table->unsignedBigInteger('price_idr');
            $table->timestamps();


            $table->index('cart_id');
            $table->index('spare_part_id');
            $table->unique(['cart_id', 'spare_part_id']);
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};

