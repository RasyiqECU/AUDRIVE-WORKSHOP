<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 20);
            $table->string('status', 20)->default('pending');
            $table->unsignedBigInteger('subtotal_idr')->default(0);
            $table->unsignedBigInteger('discount_idr')->default(0);
            $table->unsignedBigInteger('tax_idr')->default(0);
            $table->unsignedBigInteger('total_idr')->default(0);
            $table->unsignedBigInteger('paid_idr')->default(0);
            $table->string('payment_status', 20)->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();


            $table->index('code');
            $table->index('type');
            $table->index('status');
            $table->index('payment_status');
            $table->index('customer_id');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('sales_orders');
    }
};

