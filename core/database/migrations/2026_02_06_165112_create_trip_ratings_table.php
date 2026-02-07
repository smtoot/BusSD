<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booked_ticket_id')->default(0);
            $table->unsignedBigInteger('passenger_id')->default(0);
            $table->unsignedBigInteger('trip_id')->default(0);
            $table->tinyInteger('rating')->default(0); // 1-5 stars
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_ratings');
    }
};
