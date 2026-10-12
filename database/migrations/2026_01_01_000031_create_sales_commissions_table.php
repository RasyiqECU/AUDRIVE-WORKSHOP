<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sales_order_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('amount_idr');
            $table->string('status', 20)->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();


            $table->index('user_id');
            $table->index('sales_order_id');
            $table->index('status');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('sales_commissions');
    }
};

