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
        Schema::create('seat_pricing_modifiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained()->onDelete('cascade');
            $table->foreignId('trip_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('fleet_type_id')->nullable()->constrained()->onDelete('cascade');
            
            // Modifier Details
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->enum('modifier_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('modifier_value', 10, 2);
            
            // Seat Selection Criteria
            $table->enum('applies_to', ['category', 'position', 'specific_seats', 'all'])->default('all');
            $table->string('seat_category', 50)->nullable(); // 'premium', 'economy', 'vip'
            $table->json('seat_positions')->nullable(); // ['1A', '1B', '1C']
            $table->integer('row_range_start')->nullable(); // Start row number
            $table->integer('row_range_end')->nullable(); // End row number
            $table->string('seat_type', 20)->nullable(); // 'window', 'aisle', 'middle'
            
            // Status & Priority
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0); // Higher = applied first
            
            $table->timestamps();
            
            // Indexes
            $table->index(['owner_id', 'is_active']);
            $table->index('trip_id');
            $table->index('fleet_type_id');
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seat_pricing_modifiers');
    }
};
