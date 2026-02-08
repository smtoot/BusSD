<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settlements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id');
            $table->decimal('gross_amount', 28, 8)->default(0);
            $table->decimal('commission_amount', 28, 8)->default(0);
            $table->decimal('net_amount', 28, 8)->default(0);
            $table->tinyInteger('status')->default(0)->comment('0: Pending, 1: Paid');
            $table->string('trx', 40)->unique();
            $table->string('settlement_period')->nullable();
            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('owners')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settlements');
    }
};
