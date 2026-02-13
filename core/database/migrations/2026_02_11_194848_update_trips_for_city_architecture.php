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
            $table->unsignedBigInteger('starting_city_id')->after('schedule_id')->default(0);
            $table->unsignedBigInteger('destination_city_id')->after('starting_city_id')->default(0);
        });

        // Drop old columns if they exist
        Schema::table('trips', function (Blueprint $table) {
            if (Schema::hasColumn('trips', 'starting_point')) {
                $table->dropColumn('starting_point');
            }
            if (Schema::hasColumn('trips', 'destination_point')) {
                $table->dropColumn('destination_point');
            }
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
            $table->unsignedBigInteger('starting_point')->after('schedule_id')->default(0);
            $table->unsignedBigInteger('destination_point')->after('starting_point')->default(0);
            $table->dropColumn(['starting_city_id', 'destination_city_id']);
        });
    }
};
