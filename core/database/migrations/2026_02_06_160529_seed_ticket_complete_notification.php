<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('notification_templates')->insert([
            'act' => 'TICKET_COMPLETE',
            'name' => 'Ticket Payment Success (B2C)',
            'subject' => 'Your Bus Ticket is Confirmed!',
            'email_body' => '<p>Hello {{name}},</p><p>Your payment via {{method_name}} was successful. Your ticket is now confirmed.</p><p>Ticket Amount: {{amount}} {{method_currency}}</p><p>Transaction ID: {{trx}}</p><p>You can view your digital ticket in the app.</p>',
            'sms_body' => 'Success! Your ticket is confirmed. Trx: {{trx}}. View your e-ticket in the TransLab app.',
            'shortcodes' => '{"name":"Full Name","method_name":"Payment Method","amount":"Amount","method_currency":"Currency","trx":"Transaction ID"}',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('notification_templates')->where('act', 'TICKET_COMPLETE')->delete();
    }
};
