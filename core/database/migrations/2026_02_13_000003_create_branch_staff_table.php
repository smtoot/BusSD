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
        Schema::create('branch_staff', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('user_id')->comment('Links to counter_managers table');
            $table->enum('role', ['manager', 'supervisor', 'agent', 'driver'])->default('agent');
            $table->json('permissions')->nullable()->comment('Fine-grained permissions');
            $table->boolean('is_active')->default(true);
            $table->date('assigned_date')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('counter_managers')->onDelete('cascade');
            
            // Unique constraint
            $table->unique(['branch_id', 'user_id'], 'unique_branch_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_staff');
    }
};
