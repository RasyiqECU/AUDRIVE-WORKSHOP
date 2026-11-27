<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            // "waiting_part_confirmation" 25 karakter
            $table->string('status', 30)->default('booking')->change();
        });

        Schema::table('work_order_items', function (Blueprint $table) {
            // Jasa pasang yang ditawarkan bila customer membawa part sendiri
            $table->unsignedBigInteger('install_fee_idr')->nullable();
        });
    }

    public function down(): void
    {
        // Lebar kolom status sengaja tidak dikembalikan agar data tidak terpotong
        Schema::table('work_order_items', function (Blueprint $table) {
            $table->dropColumn('install_fee_idr');
        });
    }
};
