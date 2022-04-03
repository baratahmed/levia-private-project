<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRestInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rest_info', function (Blueprint $table) {
            $table->increments('id');
            $table->string('rest_name');
            // $table->string('email', 100)->unique();
            $table->string('rest_image_url')->nullable();
            $table->decimal('rest_latitude',15,10)->nullable();
            $table->decimal('rest_longitude',15,10)->nullable();
            $table->string('rest_street')->nullable();
            $table->unsignedInteger('city_id')->nullable();
            $table->unsignedInteger('district_id');
            $table->string('rest_post_code', 10)->nullable();
            $table->text('rest_description')->nullable();
            $table->string('road_no')->nullable();
            $table->string('police_station')->nullable();
            $table->string('rest_tax_no')->nullable();
            $table->string('phone', 100)->nullable();
            $table->boolean('rest_verified')->nullable()->default(false);
            $table->dateTime('rest_reg_date')->nullable();
            $table->enum('weekend1', ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'])->nullable();
            $table->enum('weekend2', ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'])->nullable();
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
        Schema::dropIfExists('rest_info');
    }
}
