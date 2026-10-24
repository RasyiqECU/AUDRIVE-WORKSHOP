<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_units', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('slug', 150)->unique();
            $table->string('brand', 50);
            $table->string('model', 50);
            $table->unsignedSmallInteger('year');
            $table->enum('transmission', ['manual', 'automatic', 'cvt'])->default('automatic');
            $table->string('color', 30)->nullable();
            $table->unsignedInteger('mileage_km')->default(0);
            $table->unsignedBigInteger('price_buy_idr')->default(0);
            $table->unsignedBigInteger('price_sell_idr');
            $table->string('status', 20)->default('draft');
            $table->text('description')->nullable();
            $table->string('stock_status', 20)->default('available');
            $table->string('warranty_status', 20)->nullable();
            $table->string('stnk_status', 20)->nullable();
            $table->string('bpkb_status', 20)->nullable();
            $table->string('location', 100)->nullable();
            $table->timestamp('featured_at')->nullable();
            $table->string('fuel_type', 20)->nullable();
            $table->string('engine_cc', 10)->nullable();
            $table->string('exterior_color', 30)->nullable();
            $table->string('interior_color', 30)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('brand');
            $table->index('year');
            $table->index('price_sell_idr');
            $table->index('featured_at');
            $table->index(['brand', 'model', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_units');
    }
};