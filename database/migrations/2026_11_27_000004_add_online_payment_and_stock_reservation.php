<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spare_parts', function (Blueprint $table) {
            // Signed agar `stock - booked_stock` tidak pernah error "out of range" di MySQL
            $table->integer('booked_stock')->default(0);
        });

        Schema::table('sales_orders', function (Blueprint $table) {
            $table->foreignId('work_order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('payment_channel', 20)->nullable();
            $table->string('fulfillment_method', 20)->nullable();
            $table->text('shipping_address')->nullable();
            $table->unsignedBigInteger('shipping_fee_idr')->default(0);
            $table->boolean('stock_reserved')->default(false);
            $table->timestamp('reserved_until')->nullable();
            $table->timestamp('prepared_at')->nullable();
            $table->timestamp('handed_over_at')->nullable();

            $table->index(['status', 'reserved_until']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('purpose', 20)->default('full');
            $table->string('proof_path')->nullable();
            $table->string('sender_name', 100)->nullable();
            $table->string('destination_bank', 100)->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->string('rejection_reason')->nullable();
        });

        // Pesanan spare part lama yang sudah lunas dianggap sudah diserahkan
        DB::table('sales_orders')->where('type', 'spare_part')->where('status', 'paid')
            ->update(['handed_over_at' => DB::raw('updated_at')]);
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('verified_by');
            $table->dropColumn(['purpose', 'proof_path', 'sender_name', 'destination_bank', 'uploaded_at', 'verified_at', 'rejection_reason']);
        });

        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('work_order_id');
            $table->dropIndex(['status', 'reserved_until']);
            $table->dropColumn(['payment_channel', 'fulfillment_method', 'shipping_address', 'shipping_fee_idr', 'stock_reserved', 'reserved_until', 'prepared_at', 'handed_over_at']);
        });

        Schema::table('spare_parts', function (Blueprint $table) {
            $table->dropColumn('booked_stock');
        });
    }
};
