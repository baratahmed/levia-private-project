<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsFeedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news_feeds', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('rest_id')->nullable();
            $table->unsignedBigInteger('food_id')->nullable();
            $table->enum('type',['R','F'])->comment('R:Restaurant, F:Food');
            $table->string('action', 20)->comment('reviewd, rated');
            $table->string('object')->comment('details about this post');
            $table->enum('rating_value', ['1','2','3','4','5']);
            $table->text('review_text')->nullable();
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
        Schema::dropIfExists('news_feeds');
    }
}
