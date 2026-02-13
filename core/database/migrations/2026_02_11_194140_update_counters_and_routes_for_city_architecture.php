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
        Schema::table('counters', function (Blueprint $table) {
            $table->unsignedBigInteger('city_id')->after('owner_id')->nullable();
            $table->dropColumn('city');
        });

        Schema::table('routes', function (Blueprint $table) {
            $table->unsignedBigInteger('starting_city_id')->after('owner_id')->nullable();
            $table->unsignedBigInteger('destination_city_id')->after('starting_city_id')->nullable();
            $table->dropColumn(['starting_point', 'destination_point']);
        });
    }

    public function down(): void
    {
        Schema::table('counters', function (Blueprint $table) {
            $table->string('city')->nullable();
            $table->dropColumn('city_id');
        });

        Schema::table('routes', function (Blueprint $table) {
            $table->integer('starting_point')->nullable();
            $table->integer('destination_point')->nullable();
            $table->dropColumn(['starting_city_id', 'destination_city_id']);
        });
    }
};
