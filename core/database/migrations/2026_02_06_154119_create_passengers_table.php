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
        Schema::create('passengers', function (Blueprint $col) {
            $col->id();
            $col->string('firstname', 40)->nullable();
            $col->string('lastname', 40)->nullable();
            $col->string('username', 40)->unique();
            $col->string('email')->unique();
            $col->string('dial_code', 40)->nullable();
            $col->string('mobile', 40)->unique();
            $col->string('password');
            $col->string('image')->nullable();
            $col->string('phone_otp', 10)->nullable();
            $col->datetime('otp_expires_at')->nullable();
            $col->string('ver_code', 255)->nullable();
            $col->timestamp('ver_code_send_at')->nullable();
            $col->tinyInteger('status')->default(1)->comment('1: Active, 0: Banned');
            $col->tinyInteger('ev')->default(0)->comment('Email Verified');
            $col->tinyInteger('sv')->default(0)->comment('SMS Verified');
            $col->tinyInteger('profile_complete')->default(0);
            $col->rememberToken();
            $col->softDeletes();
            $col->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('passengers');
    }
};
