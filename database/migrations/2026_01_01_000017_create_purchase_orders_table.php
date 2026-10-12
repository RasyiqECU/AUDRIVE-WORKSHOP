<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('supplier_name', 150);
            $table->string('status', 25)->default('draft');
            $table->unsignedBigInteger('total_idr')->default(0);
            $table->timestamp('ordered_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();


            $table->index('code');
            $table->index('status');
            $table->index('supplier_name');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};

