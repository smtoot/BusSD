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
        Schema::create('route_dropping_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained('routes')->onDelete('cascade');
            $table->foreignId('dropping_point_id')->constrained('dropping_points')->onDelete('cascade');
            $table->integer('dropoff_time_offset')->default(0); // Minutes from route start
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['route_id', 'dropping_point_id']);
            $table->index(['route_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('route_dropping_points');
    }
};
