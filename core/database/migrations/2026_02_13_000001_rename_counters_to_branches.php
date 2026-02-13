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
        // Rename counters table to branches
        Schema::rename('counters', 'branches');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rename branches table back to counters
        Schema::rename('branches', 'counters');
    }
};
