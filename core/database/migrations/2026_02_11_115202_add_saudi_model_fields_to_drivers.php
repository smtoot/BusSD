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
        Schema::table('drivers', function (Blueprint $table) {
            $table->string('nationality', 40)->default('Sudan')->after('mobile');
            $table->string('id_type', 40)->nullable()->after('nationality');
            $table->string('id_number', 40)->nullable()->after('id_type');
            $table->string('license_number', 40)->nullable()->after('id_number');
            $table->date('license_expiry_date')->nullable()->after('license_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn(['nationality', 'id_type', 'id_number', 'license_number', 'license_expiry_date']);
        });
    }
};
