<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('type', 15);
            $table->unsignedBigInteger('value');
            $table->unsignedBigInteger('min_purchase_idr')->default(0);
            $table->unsignedInteger('max_usage')->default(1);
            $table->unsignedInteger('used_count')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();


            $table->index('code');
            $table->index('is_active');
            $table->index('expires_at');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};

