<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->decimal('latitude',15,10)->nullable();
            $table->decimal('longitude',15,10)->nullable();
            $table->string('city')->nullable();
            $table->string('district', 50);
            $table->string('upazila', 50);
            $table->string('police_station', 100)->nullable();
            $table->string('post_code', 10)->nullable();
            $table->string('road_no', 50)->nullable();
            $table->string('flat_no', 50)->nullable();
            $table->string('other_details')->nullable();
            $table->string('phone');
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
        Schema::dropIfExists('user_addresses');
    }
}
