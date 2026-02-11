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
        Schema::create('cancellation_policies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // 'flexible', 'moderate', 'strict', 'custom_xyz'
            $table->string('label'); // "Flexible Policy"
            $table->text('description')->nullable(); // "Full refund up to 24 hours before departure"
            $table->json('rules'); // [{ hours: 24, refund_percentage: 100 }, { hours: 12, refund_percentage: 50 }]
            $table->boolean('is_default')->default(false); // Default policy for new trips
            $table->boolean('is_system')->default(false); // System policies can't be deleted
            $table->boolean('is_active')->default(true); // Can be disabled
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cancellation_policies');
    }
};
