<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mechanic_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('shift_start');
            $table->time('shift_end');
            $table->boolean('is_available')->default(true);
            $table->timestamps();


            $table->index('user_id');
            $table->index('date');
            $table->index('is_available');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('mechanic_schedules');
    }
};

