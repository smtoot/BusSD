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
        Schema::create('branch_operating_hours', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->tinyInteger('day_of_week')->comment('0=Sunday, 6=Saturday');
            $table->time('opens_at');
            $table->time('closes_at');
            $table->boolean('is_24_hours')->default(false);
            $table->boolean('is_closed')->default(false);
            $table->timestamps();
            
            // Foreign key
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            
            // Unique constraint
            $table->unique(['branch_id', 'day_of_week'], 'unique_branch_day');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_operating_hours');
    }
};
