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
        Schema::create('trip_boarding_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->onDelete('cascade');
            $table->foreignId('boarding_point_id')->constrained('boarding_points')->onDelete('cascade');
            $table->time('scheduled_time'); // Scheduled pickup time
            $table->time('actual_time')->nullable(); // Actual pickup time (for trip tracking)
            $table->integer('passenger_count')->default(0); // Number of passengers boarding here
            $table->integer('sort_order')->default(0); // Display order in trip timeline
            $table->text('notes')->nullable(); // Any special notes for this stop
            $table->timestamps();
            
            // Prevent duplicate assignments
            $table->unique(['trip_id', 'boarding_point_id']);
            
            // Index for querying by trip
            $table->index('trip_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_boarding_points');
    }
};
