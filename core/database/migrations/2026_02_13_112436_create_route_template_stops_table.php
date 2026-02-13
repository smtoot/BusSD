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
        Schema::create('route_template_stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_template_id')->constrained()->onDelete('cascade');
            $table->foreignId('city_id')->constrained();
            $table->integer('sequence_order');
            $table->integer('time_offset_minutes')->default(0);
            $table->integer('dwell_time_minutes')->default(5);
            $table->decimal('distance_from_previous', 10, 2)->nullable();
            $table->boolean('boarding_allowed')->default(true);
            $table->boolean('dropping_allowed')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['route_template_id', 'sequence_order']);
            $table->unique(['route_template_id', 'sequence_order'], 'template_sequence_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('route_template_stops');
    }
};
