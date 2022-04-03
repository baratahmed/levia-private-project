<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRestFoodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rest_food', function (Blueprint $table) {
            $table->unsignedInteger('rest_id');
            $table->unsignedInteger('food_id');
            $table->decimal('unit_price', 8,4);
            $table->string('food_image_url');
            $table->boolean('food_availability')->nullable();
            $table->unsignedInteger('food_category_id');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['rest_id', 'food_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rest_food');
    }
}
