<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('code', 20)->unique();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('ktp_number', 20)->nullable();
            $table->integer('loyalty_points')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('city');
            $table->index('province');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};