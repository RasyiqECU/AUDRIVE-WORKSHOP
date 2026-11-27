<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('car_inspections', function (Blueprint $table) {
            $table->unsignedBigInteger('estimated_repair_idr')->nullable();
        });

        Schema::table('car_units', function (Blueprint $table) {
            $table->foreignId('price_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('price_approved_at')->nullable();
            $table->unsignedBigInteger('repair_cost_idr')->default(0);
        });

        Schema::table('sales_orders', function (Blueprint $table) {
            // Booking fee yang mengunci unit mobil; 0 = bukan pesanan dengan booking fee
            $table->unsignedBigInteger('deposit_idr')->default(0);
        });

        // Penjualan mobil lama yang sudah lunas dianggap sudah diserahkan
        DB::table('sales_orders')->where('type', 'car_sales')->where('status', 'paid')
            ->update(['handed_over_at' => DB::raw('updated_at')]);
    }

    public function down(): void
    {
        Schema::table('sales_orders', fn (Blueprint $table) => $table->dropColumn('deposit_idr'));

        Schema::table('car_units', function (Blueprint $table) {
            $table->dropConstrainedForeignId('price_approved_by');
            $table->dropColumn(['price_approved_at', 'repair_cost_idr']);
        });

        Schema::table('car_inspections', fn (Blueprint $table) => $table->dropColumn('estimated_repair_idr'));
    }
};
