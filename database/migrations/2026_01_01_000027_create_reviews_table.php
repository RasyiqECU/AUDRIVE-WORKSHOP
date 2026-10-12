<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('reviewer_type');
            $table->unsignedBigInteger('reviewer_id');
            $table->string('reviewable_type');
            $table->unsignedBigInteger('reviewable_id');
            $table->unsignedTinyInteger('rating')->default(5);
            $table->text('comment')->nullable();
            $table->string('status', 20)->default('pending');
            $table->timestamps();


            $table->index(['reviewer_type', 'reviewer_id']);
            $table->index(['reviewable_type', 'reviewable_id']);
            $table->index('rating');
            $table->index('status');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};

