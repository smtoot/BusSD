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
        Schema::create('amenity_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // 'wifi', 'ac', 'usb_charging', etc.
            $table->string('label'); // Localized label (can be translated)
            $table->string('icon')->default('fa-circle'); // Font Awesome icon class
            $table->string('category')->default('other'); // connectivity, comfort, entertainment, facilities, safety
            $table->boolean('is_active')->default(true); // Can be disabled
            $table->boolean('is_system')->default(false); // System amenities can't be deleted
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amenity_templates');
    }
};
