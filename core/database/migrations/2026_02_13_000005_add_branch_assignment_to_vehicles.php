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
            $table->unsignedBigInteger('primary_branch_id')->nullable()->after('owner_id')->comment('Home branch');
            $table->unsignedBigInteger('current_branch_id')->nullable()->after('primary_branch_id')->comment('Currently operating branch');
            $table->boolean('is_pooled')->default(false)->after('current_branch_id')->comment('Shared pool vehicle');
            
            // Foreign keys
            $table->foreign('primary_branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->foreign('current_branch_id')->references('id')->on('branches')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign(['primary_branch_id']);
            $table->dropForeign(['current_branch_id']);
            $table->dropColumn(['primary_branch_id', 'current_branch_id', 'is_pooled']);
        });
    }
};
