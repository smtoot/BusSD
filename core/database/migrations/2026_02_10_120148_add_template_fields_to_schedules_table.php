<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
            $table->unsignedBigInteger('route_id')->default(0)->after('owner_id');
            $table->unsignedBigInteger('fleet_type_id')->default(0)->after('route_id');
            
            // Duration
            $table->integer('duration_hours')->default(0)->after('ends_at');
            $table->integer('duration_minutes')->default(0)->after('duration_hours');
            
            // Recurrence
            $table->string('recurrence_type')->default('daily')->after('duration_minutes'); // daily, weekly
            $table->json('recurrence_days')->nullable()->after('recurrence_type'); // [0,1,2,3,4,5,6] (0=Sunday)
            
            // Validity
            $table->date('starts_on')->nullable()->after('recurrence_days');
            $table->date('ends_on')->nullable()->after('starts_on');
            $table->boolean('never_ends')->default(true)->after('ends_on');
            
            // Pricing & Inventory
            $table->decimal('base_price', 10, 2)->nullable()->after('never_ends');
            $table->string('inventory_allocation')->default('all_seats')->after('base_price');
            $table->integer('inventory_count')->nullable()->after('inventory_allocation');
            
            // Policies
            $table->string('cancellation_policy')->default('flexible')->after('inventory_count');
            
            // Relationships (optional but good practice)
            $table->unsignedBigInteger('starting_point')->default(0)->after('route_id');
            $table->unsignedBigInteger('destination_point')->default(0)->after('starting_point');
            $table->unsignedBigInteger('vehicle_id')->default(0)->after('fleet_type_id'); // Default vehicle
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn([
                'name',
                'route_id',
                'starting_point',
                'destination_point',
                'fleet_type_id',
                'vehicle_id',
                'duration_hours',
                'duration_minutes',
                'recurrence_type',
                'recurrence_days',
                'starts_on',
                'ends_on',
                'never_ends',
                'base_price',
                'inventory_allocation',
                'inventory_count',
                'cancellation_policy'
            ]);
        });
    }
};
