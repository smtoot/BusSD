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
        $tables = ['co_owners', 'counter_managers', 'supervisors', 'drivers'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->text('permissions')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['co_owners', 'counter_managers', 'supervisors', 'drivers'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('permissions');
            });
        }
    }
};
