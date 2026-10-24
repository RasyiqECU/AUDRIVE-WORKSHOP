<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->string('type', 15);
            $table->unsignedBigInteger('value');
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->string('applicable_to', 30)->nullable();
            $table->timestamps();


            $table->index('slug');
            $table->index('is_active');
            $table->index(['starts_at', 'ends_at']);
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};

