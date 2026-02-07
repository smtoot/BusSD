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
        Schema::table('general_settings', function (Blueprint $table) {
            $table->decimal('b2c_commission', 5, 2)->default(0)->after('cur_sym');
        });

        Schema::table('owners', function (Blueprint $table) {
            $table->decimal('b2c_commission', 5, 2)->nullable()->after('balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn('b2c_commission');
        });

        Schema::table('owners', function (Blueprint $table) {
            $table->dropColumn('b2c_commission');
        });
    }
};
