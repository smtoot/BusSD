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
        Schema::table('branches', function (Blueprint $table) {
            // Branch identification
            $table->string('code', 20)->unique()->nullable()->after('name');
            $table->enum('type', ['headquarters', 'branch', 'sub_branch'])->default('branch')->after('code');
            
            // Autonomy settings
            $table->enum('autonomy_level', ['controlled', 'semi_autonomous', 'autonomous'])->default('controlled')->after('type');
            $table->boolean('can_set_routes')->default(false)->after('autonomy_level');
            $table->boolean('can_adjust_pricing')->default(false)->after('can_set_routes');
            $table->integer('pricing_variance_limit')->default(0)->comment('Max ±% for pricing adjustments')->after('can_adjust_pricing');
            
            // Operational settings
            $table->boolean('allows_online_booking')->default(true)->after('pricing_variance_limit');
            $table->boolean('allows_counter_booking')->default(true)->after('allows_online_booking');
            $table->string('timezone', 50)->default('Asia/Riyadh')->after('allows_counter_booking');
            
            // Contact information
            $table->string('contact_email', 100)->nullable()->after('mobile');
            
            // Business information
            $table->string('tax_registration_no', 100)->nullable()->after('location');
            $table->json('bank_account_details')->nullable()->after('tax_registration_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn([
                'code',
                'type',
                'autonomy_level',
                'can_set_routes',
                'can_adjust_pricing',
                'pricing_variance_limit',
                'allows_online_booking',
                'allows_counter_booking',
                'timezone',
                'contact_email',
                'tax_registration_no',
                'bank_account_details'
            ]);
        });
    }
};
