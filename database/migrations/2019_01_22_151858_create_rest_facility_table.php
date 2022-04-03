<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRestFacilityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rest_facility', function (Blueprint $table) {
            $table->increments('rest_facility_id');
            $table->unsignedInteger('rest_id');
            $table->boolean('parking')->nullable(); // Street Parking
            $table->boolean('wifi')->nullable();
            $table->boolean('smoking_place')->nullable();
            $table->boolean('kids_corner')->nullable();
            $table->boolean('live_music')->nullable();
            $table->boolean('self_service')->nullable();
            $table->boolean('praying_area')->nullable();
            $table->boolean('game_zone')->nullable();
            $table->integer('tv');
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
        Schema::dropIfExists('rest_facility');
    }
}
