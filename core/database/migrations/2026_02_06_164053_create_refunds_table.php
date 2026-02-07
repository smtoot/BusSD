<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booked_ticket_id')->default(0);
            $table->unsignedBigInteger('passenger_id')->default(0);
            $table->decimal('amount', 28, 8)->default(0);
            $table->string('trx', 40)->nullable();
            $table->text('admin_feedback')->nullable();
            $table->tinyInteger('status')->default(0); // 0: Pending, 1: Approved, 2: Rejected
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
