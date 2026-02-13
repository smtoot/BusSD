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
        Schema::table('trips', function (Blueprint $table) {
            $table->unsignedBigInteger('owning_branch_id')->nullable()->after('owner_id')->comment('Branch that created trip');
            $table->unsignedBigInteger('origin_branch_id')->nullable()->after('owning_branch_id')->comment('Starting location branch');
            $table->unsignedBigInteger('destination_branch_id')->nullable()->after('origin_branch_id')->comment('Ending location branch');
            $table->enum('revenue_split_model', ['origin_100', 'destination_100', 'split_50_50', 'custom'])->default('origin_100')->after('destination_branch_id');
            $table->json('revenue_split_custom')->nullable()->after('revenue_split_model')->comment('Custom split if revenue_split_model=custom');
            
            // Foreign keys
            $table->foreign('owning_branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->foreign('origin_branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->foreign('destination_branch_id')->references('id')->on('branches')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropForeign(['owning_branch_id']);
            $table->dropForeign(['origin_branch_id']);
            $table->dropForeign(['destination_branch_id']);
            $table->dropColumn(['owning_branch_id', 'origin_branch_id', 'destination_branch_id', 'revenue_split_model', 'revenue_split_custom']);
        });
    }
};
