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
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('insurance_policy_number')->nullable()->after('owner_phone');
            $table->integer('year_of_manufacture')->nullable()->after('insurance_policy_number');
            $table->integer('total_seats')->nullable()->after('year_of_manufacture');
            $table->boolean('is_vip')->default(false)->after('total_seats');
            $table->json('photos')->nullable()->after('is_vip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'insurance_policy_number',
                'year_of_manufacture',
                'total_seats',
                'is_vip',
                'photos'
            ]);
        });
    }
};
