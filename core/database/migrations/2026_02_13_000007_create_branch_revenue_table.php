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
        Schema::create('branch_revenue', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('trip_id');
            $table->unsignedBigInteger('booked_ticket_id');
            $table->decimal('revenue_amount', 28, 8);
            $table->enum('revenue_type', ['primary', 'shared'])->default('primary');
            $table->decimal('split_percentage', 5, 2)->default(100.00);
            $table->date('booking_date');
            $table->timestamp('created_at')->nullable();
            
            // Foreign keys
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('trip_id')->references('id')->on('trips')->onDelete('cascade');
            $table->foreign('booked_ticket_id')->references('id')->on('booked_tickets')->onDelete('cascade');
            
            // Indexes for performance
            $table->index(['branch_id', 'booking_date'], 'idx_branch_date');
            $table->index('trip_id', 'idx_trip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_revenue');
    }
};
