<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('plate_number', 15)->nullable();
            $table->string('vin', 17)->nullable()->unique();
            $table->string('brand', 50);
            $table->string('model', 50);
            $table->unsignedSmallInteger('year');
            $table->string('color', 30)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['brand', 'model']);
            $table->index('plate_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_vehicles');
    }
};