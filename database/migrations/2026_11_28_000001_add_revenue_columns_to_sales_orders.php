<?php

use App\Models\SalesOrder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            // Tanggal lunas: dasar periode semua laporan omzet
            $table->timestamp('paid_at')->nullable()->index();
            // Omzet milik AUDrive (titip jual: komisi + biaya servis, bukan harga mobil)
            $table->unsignedBigInteger('revenue_idr')->default(0);
            // Sales yang menangani; kosong = penjualan mandiri / online
            $table->foreignId('sales_id')->nullable()->constrained('users')->nullOnDelete();
        });

        SalesOrder::whereIn('status', ['paid', 'completed'])->eachById(function (SalesOrder $order) {
            $paidAt = DB::table('payments')->where('paymentable_type', SalesOrder::class)
                ->where('paymentable_id', $order->id)->max('paid_at');

            DB::table('sales_orders')->where('id', $order->id)->update([
                'paid_at' => $paidAt ?? $order->created_at,
                'revenue_idr' => $order->calculateRevenue(),
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sales_id');
            $table->dropColumn(['paid_at', 'revenue_idr']);
        });
    }
};
