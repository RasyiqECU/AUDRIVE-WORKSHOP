<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->morphs('invoiceable');
            $table->string('number', 30)->unique();
            $table->string('status', 20)->default('draft');
            $table->timestamp('due_at')->nullable();
            $table->unsignedBigInteger('total_idr');
            $table->text('notes')->nullable();
            $table->timestamps();


            $table->index('number');
            $table->index('status');
            $table->index('due_at');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

