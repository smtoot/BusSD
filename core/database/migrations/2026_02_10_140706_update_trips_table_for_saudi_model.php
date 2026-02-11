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
        Schema::table('trips', function (Blueprint $table) {
            // Add specific datetime fields (critical for single instances)
            $table->dateTime('departure_datetime')->nullable()->after('schedule_id');
            $table->dateTime('arrival_datetime')->nullable()->after('departure_datetime');
            
            // Add cancellation policy (can inherit from schedule or override)
            $table->unsignedBigInteger('cancellation_policy_id')->nullable()->after('arrival_datetime');
            $table->foreign('cancellation_policy_id')->references('id')->on('cancellation_policies')->onDelete('set null');
            
            // Add amenities (JSON array of amenity IDs or keys)
            $table->json('amenities')->nullable()->after('cancellation_policy_id');
            
            // Add inventory management
            $table->string('inventory_allocation')->default('full')->after('amenities'); // full, partial, reserved, custom
            $table->integer('inventory_count')->nullable()->after('inventory_allocation'); // For partial/reserved
            
            // Add seat price (trip-specific pricing)
            $table->decimal('seat_price', 10, 2)->nullable()->after('inventory_count');
            
            // Remove day_off (conceptually wrong - trips don't recur!)
            $table->dropColumn('day_off');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            // Restore day_off
            $table->json('day_off')->nullable();
            
            // Drop new columns
            $table->dropForeign(['cancellation_policy_id']);
            $table->dropColumn([
                'departure_datetime',
                'arrival_datetime',
                'cancellation_policy_id',
                'amenities',
                'inventory_allocation',
                'inventory_count',
                'seat_price',
            ]);
        });
    }
};
