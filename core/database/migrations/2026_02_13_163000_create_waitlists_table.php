<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('waitlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('passenger_id')->constrained()->onDelete('cascade');
            $table->foreignId('trip_id')->constrained()->onDelete('cascade');
            $table->date('date_of_journey');
            $table->integer('seat_count')->default(1);
            $table->integer('pickup_id');
            $table->integer('destination_id');
            $table->tinyInteger('status')->default(0)->comment('0: Pending, 1: Notified, 2: Booked, 3: Cancelled');
            $table->timestamps();
            
            $table->index(['trip_id', 'date_of_journey', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('waitlists');
    }
};
