<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawal_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40)->nullable();
            $table->string('image', 255)->nullable();
            $table->decimal('min_limit', 28, 8)->default(0);
            $table->decimal('max_limit', 28, 8)->default(0);
            $table->decimal('fixed_charge', 28, 8)->default(0);
            $table->decimal('percent_charge', 5, 2)->default(0);
            $table->string('delay', 40)->nullable();
            $table->text('user_data')->nullable(); // JSON for custom fields
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('method_id')->default(0);
            $table->unsignedBigInteger('owner_id')->default(0);
            $table->decimal('amount', 28, 8)->default(0);
            $table->string('currency', 40)->nullable();
            $table->decimal('rate', 28, 8)->default(0);
            $table->decimal('charge', 28, 8)->default(0);
            $table->decimal('after_charge', 28, 8)->default(0);
            $table->decimal('final_amount', 28, 8)->default(0);
            $table->string('trx', 40)->nullable();
            $table->text('withdraw_information')->nullable();
            $table->text('admin_feedback')->nullable();
            $table->tinyInteger('status')->default(0); // 0: Pending, 1: Approved, 2: Rejected
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
        Schema::dropIfExists('withdrawal_methods');
    }
};
