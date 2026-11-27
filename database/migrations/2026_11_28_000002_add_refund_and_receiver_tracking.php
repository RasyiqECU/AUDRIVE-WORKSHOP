<?php

use App\Models\SalesOrder;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('refunded_idr')->default(0);
        });

        // Isi penerima pembayaran lama; layar pembayaran tunai/EDC hanya bisa dipakai Cashier
        $cashiers = User::where('role', 'cashier')->pluck('id');
        $payments = DB::table('payments')->where('paymentable_type', SalesOrder::class)
            ->whereNull('verified_by')->where('method', '!=', 'refund')
            ->whereIn('status', ['success', 'refunded'])->get(['id', 'paymentable_id']);

        foreach ($payments as $payment) {
            $receiver = DB::table('sales_orders')->where('id', $payment->paymentable_id)->whereIn('created_by', $cashiers)->value('created_by')
                ?? DB::table('stock_movements')->where('reference_type', SalesOrder::class)->where('reference_id', $payment->paymentable_id)
                    ->whereIn('created_by', $cashiers)->value('created_by')
                ?? ($cashiers->count() === 1 ? $cashiers->first() : null);

            DB::table('payments')->where('id', $payment->id)->update(['verified_by' => $receiver]);
        }
    }

    public function down(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropColumn('refunded_idr');
        });
    }
};
