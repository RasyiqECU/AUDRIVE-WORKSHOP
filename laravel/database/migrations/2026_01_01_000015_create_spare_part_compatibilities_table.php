<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spare_part_compatibilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spare_part_id')->constrained()->cascadeOnDelete();
            $table->string('vehicle_brand', 50);
            $table->string('vehicle_model', 50);
            $table->unsignedSmallInteger('year_from');
            $table->unsignedSmallInteger('year_to');
            $table->timestamps();


            $table->index('spare_part_id');
            $table->index(['vehicle_brand', 'vehicle_model']);
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('spare_part_compatibilities');
    }
};

