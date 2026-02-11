<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trips', function (Blueprint $table) {
            // Trip Classification
            $table->enum('trip_type', ['express', 'semi_express', 'local', 'night'])->default('local')->after('status');
            $table->enum('trip_category', ['premium', 'standard', 'budget'])->default('standard')->after('trip_type');
            $table->string('bus_type')->nullable()->after('trip_category');

            // Pricing Configuration
            $table->decimal('base_price', 10, 2)->nullable()->after('bus_type');
            $table->decimal('weekend_surcharge', 5, 2)->default(0)->after('base_price');
            $table->decimal('holiday_surcharge', 5, 2)->default(0)->after('weekend_surcharge');
            $table->decimal('early_bird_discount', 5, 2)->default(0)->after('holiday_surcharge');
            $table->decimal('last_minute_surcharge', 5, 2)->default(0)->after('early_bird_discount');

            // Display & Search
            $table->integer('search_priority')->default(50)->after('last_minute_surcharge');

            // Workflow
            $table->enum('trip_status', ['draft', 'pending', 'approved', 'active'])->default('draft')->after('search_priority');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn([
                'trip_type',
                'trip_category',
                'bus_type',
                'base_price',
                'weekend_surcharge',
                'holiday_surcharge',
                'early_bird_discount',
                'last_minute_surcharge',
                'search_priority',
                'trip_status'
            ]);
        });
    }
};
