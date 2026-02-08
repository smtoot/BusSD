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
        if (!Schema::hasColumn('admin_notifications', 'passenger_id')) {
            Schema::table('admin_notifications', function (Blueprint $table) {
                $table->integer('passenger_id')->default(0)->after('owner_id');
            });
        }

        if (!Schema::hasColumn('notification_logs', 'passenger_id')) {
            Schema::table('notification_logs', function (Blueprint $table) {
                $table->integer('passenger_id')->default(0)->after('owner_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_notifications', function (Blueprint $table) {
            $table->dropColumn('passenger_id');
        });

        Schema::table('notification_logs', function (Blueprint $table) {
            $table->dropColumn('passenger_id');
        });
    }
};
