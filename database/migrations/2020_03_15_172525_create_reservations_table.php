<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('rest_id');
            $table->unsignedInteger('user_id');
            $table->unsignedTinyInteger('seats');
            $table->timestamp('reservation_time')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->boolean('is_accepted')->default(false);
            $table->boolean('is_seen')->default(false); // by restaurant
            $table->timestamp('checked_in_time')->nullable(); // is checked in? if so, when?
            $table->decimal('checked_in_lat',15,10)->nullable(); // is checked in? if so, where?
            $table->decimal('checked_in_long',15,10)->nullable(); // is checked in? if so, where?
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservations');
    }
}
