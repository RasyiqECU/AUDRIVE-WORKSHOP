<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('subject', 200);
            $table->string('status', 20)->default('open');
            $table->string('priority', 10)->default('medium');
            $table->text('description');
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();


            $table->index('code');
            $table->index('status');
            $table->index('priority');
            $table->index('customer_id');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};

