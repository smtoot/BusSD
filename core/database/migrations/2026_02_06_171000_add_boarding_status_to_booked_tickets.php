<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booked_tickets', function (Blueprint $table) {
            $table->boolean('is_boarded')->default(0)->after('status');
            $table->timestamp('boarded_at')->nullable()->after('is_boarded');
        });
    }

    public function down(): void
    {
        Schema::table('booked_tickets', function (Blueprint $table) {
            $table->dropColumn('is_boarded');
            $table->dropColumn('boarded_at');
        });
    }
};
