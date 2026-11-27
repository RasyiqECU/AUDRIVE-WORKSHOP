<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            $table->timestamp('checked_in_at')->nullable();
            $table->foreignId('checked_in_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('odometer_km')->nullable();
            $table->text('body_condition')->nullable();
            $table->text('belongings')->nullable();
        });

        Schema::table('work_orders', function (Blueprint $table) {
            $table->timestamp('estimated_completion_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
        });

        Schema::table('work_order_items', function (Blueprint $table) {
            // Item lama dianggap sudah disetujui agar total & stok tidak berubah
            $table->string('approval_status', 20)->default('approved');
            $table->string('part_source', 20)->nullable();
            $table->timestamp('inventory_checked_at')->nullable();
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->string('decision_note')->nullable();
            $table->index('approval_status');
        });

        // Work order lama berstatus "delivered" sudah lunas dan sudah diserahkan
        DB::table('work_orders')->where('status', 'delivered')->update([
            'paid_at' => DB::raw('COALESCE(completed_at, updated_at)'),
            'delivered_at' => DB::raw('COALESCE(completed_at, updated_at)'),
        ]);
    }

    public function down(): void
    {
        Schema::table('work_order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('decided_by');
            $table->dropIndex(['approval_status']);
            $table->dropColumn(['approval_status', 'part_source', 'inventory_checked_at', 'decided_at', 'decision_note']);
        });

        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn(['estimated_completion_at', 'paid_at', 'delivered_at']);
        });

        Schema::table('service_bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('checked_in_by');
            $table->dropColumn(['checked_in_at', 'odometer_km', 'body_condition', 'belongings']);
        });
    }
};
