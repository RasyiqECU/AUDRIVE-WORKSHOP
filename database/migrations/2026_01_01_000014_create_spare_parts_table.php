<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spare_parts', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 30)->unique();
            $table->string('name', 150);
            $table->string('slug', 170)->unique();
            $table->foreignId('category_id')->constrained('spare_part_categories')->restrictOnDelete();
            $table->string('brand', 50)->nullable();
            $table->unsignedBigInteger('price_idr');
            $table->integer('stock')->default(0);
            $table->integer('low_stock_threshold')->default(5);
            $table->string('status', 20)->default('active');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->decimal('weight_kg', 8, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();


            $table->index('sku');
            $table->index('status');
            $table->index('category_id');
            $table->index('brand');
            $table->index('stock');
        });

        Schema::table('work_order_items', function (Blueprint $table) {
            $table->foreign('spare_part_id')->references('id')->on('spare_parts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('work_order_items', function (Blueprint $table) {
            $table->dropForeign(['spare_part_id']);
        });

        Schema::dropIfExists('spare_parts');
    }
};

