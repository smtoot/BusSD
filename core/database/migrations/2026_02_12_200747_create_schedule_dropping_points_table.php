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
        Schema::create('schedule_dropping_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules')->onDelete('cascade');
            $table->foreignId('dropping_point_id')->constrained('dropping_points')->onDelete('cascade');
            $table->integer('time_offset_minutes')->default(0); // Minutes from schedule start time
            $table->integer('sort_order')->default(0); // Display order in timeline
            $table->text('notes')->nullable(); // Template notes for this stop
            $table->timestamps();
            
            // Prevent duplicate point assignments in same schedule
            $table->unique(['schedule_id', 'dropping_point_id']);
            
            // Index for efficient schedule lookups
            $table->index('schedule_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_dropping_points');
    }
};
