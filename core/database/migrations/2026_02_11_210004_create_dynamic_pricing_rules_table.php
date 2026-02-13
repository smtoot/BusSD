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
        Schema::create('dynamic_pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->nullable()->constrained('owners')->onDelete('cascade');
            $table->foreignId('route_id')->nullable()->constrained('routes')->onDelete('cascade');
            $table->foreignId('fleet_type_id')->nullable()->constrained('fleet_types')->onDelete('set null');
            $table->string('name');
            $table->enum('rule_type', ['surge', 'early_bird', 'last_minute', 'weekend', 'holiday', 'custom']);
            $table->enum('operator', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('value', 8, 2); // Percentage or fixed amount
            $table->integer('min_hours_before_departure')->nullable(); // For early bird/last minute
            $table->integer('max_hours_before_departure')->nullable(); // For early bird/last minute
            $table->json('applicable_days')->nullable(); // Days of week [0-6]
            $table->json('applicable_dates')->nullable(); // Specific dates
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('min_seats_available')->nullable();
            $table->integer('max_seats_available')->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->integer('priority')->default(0); // Higher priority rules applied first
            $table->timestamps();

            $table->index(['owner_id', 'is_active']);
            $table->index(['route_id', 'is_active']);
            $table->index(['rule_type', 'is_active']);
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dynamic_pricing_rules');
    }
};
