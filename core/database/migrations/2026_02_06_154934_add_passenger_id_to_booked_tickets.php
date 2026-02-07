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
        Schema::table('booked_tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('passenger_id')->nullable()->after('owner_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booked_tickets', function (Blueprint $table) {
            $table->dropColumn('passenger_id');
        });
    }
};
