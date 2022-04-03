<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAllFoodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('all_food', function (Blueprint $table) {
            $table->increments('food_id');
            // $table->unsignedInteger('food_category_id');
            $table->string('food_name', 100);
            $table->timestamps();

            // $table->unique(['food_name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('all_food');
    }
}
