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
        \DB::table('transactions')->where('remark', 'b2c_ticket_sale')->update(['remark' => 'app_ticket_sale']);
        \DB::table('transactions')->where('remark', 'b2c_commission_charge')->update(['remark' => 'app_commission_charge']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \DB::table('transactions')->where('remark', 'app_ticket_sale')->update(['remark' => 'b2c_ticket_sale']);
        \DB::table('transactions')->where('remark', 'app_commission_charge')->update(['remark' => 'b2c_commission_charge']);
    }
};
