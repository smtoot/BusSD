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
        Schema::table('amenity_templates', function (Blueprint $table) {
            $table->enum('amenity_type', ['vehicle', 'trip'])->default('vehicle')->after('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('amenity_templates', function (Blueprint $table) {
            $table->dropColumn('amenity_type');
        });
    }
};
