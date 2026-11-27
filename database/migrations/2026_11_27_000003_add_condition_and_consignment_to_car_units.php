<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('car_units', function (Blueprint $table) {
            // Unit lama dianggap bekas milik dealer; admin bisa mengubahnya
            $table->string('condition', 10)->default('used');
            $table->string('ownership', 15)->default('dealer');
            $table->foreignId('seller_customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->text('condition_defects')->nullable();
            $table->text('condition_additions')->nullable();
            $table->string('seller_warranty_info')->nullable();
            $table->boolean('seller_warranty_verified')->default(false);
            $table->unsignedBigInteger('asking_price_idr')->nullable();
            $table->unsignedBigInteger('min_price_idr')->nullable();
            $table->unsignedBigInteger('commission_idr')->nullable();
            $table->unsignedBigInteger('seller_payout_idr')->nullable();
            $table->timestamp('seller_paid_at')->nullable();
            $table->foreignId('seller_paid_by')->nullable()->constrained('users')->nullOnDelete();

            $table->index('condition');
            $table->index('ownership');
        });
    }

    public function down(): void
    {
        Schema::table('car_units', function (Blueprint $table) {
            $table->dropConstrainedForeignId('seller_customer_id');
            $table->dropConstrainedForeignId('seller_paid_by');
            $table->dropIndex(['condition']);
            $table->dropIndex(['ownership']);
            $table->dropColumn([
                'condition', 'ownership', 'condition_defects', 'condition_additions', 'seller_warranty_info',
                'seller_warranty_verified', 'asking_price_idr', 'min_price_idr', 'commission_idr',
                'seller_payout_idr', 'seller_paid_at',
            ]);
        });
    }
};
