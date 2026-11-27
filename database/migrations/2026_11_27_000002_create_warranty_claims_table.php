<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->boolean('is_warranty')->default(false);
        });

        Schema::create('warranty_claims', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('source_type', 10); // service | car
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('source_work_order_item_id')->nullable()->constrained('work_order_items')->nullOnDelete();
            $table->foreignId('source_sales_order_id')->nullable()->constrained('sales_orders')->nullOnDelete();
            $table->date('reference_date');
            $table->unsignedInteger('reference_km');
            $table->unsignedInteger('current_km');
            $table->text('complaint');
            $table->string('status', 20)->default('submitted');
            $table->text('diagnosis')->nullable();
            $table->boolean('is_covered')->nullable();
            $table->foreignId('diagnosed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('repair_work_order_id')->nullable()->constrained('work_orders')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warranty_claims');

        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn('is_warranty');
        });
    }
};
