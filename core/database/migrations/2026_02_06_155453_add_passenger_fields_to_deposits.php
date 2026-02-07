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
        Schema::table('deposits', function (Blueprint $table) {
            $table->unsignedBigInteger('passenger_id')->nullable()->after('owner_id');
            $table->unsignedBigInteger('booked_ticket_id')->nullable()->after('sold_package_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropColumn(['passenger_id', 'booked_ticket_id']);
        });
    }
};
