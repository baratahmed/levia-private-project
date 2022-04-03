<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Offer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offer_info', function (Blueprint $table) {
            $table->increments('offer_id');
            $table->unsignedInteger('offer_type_id');
            $table->string('offer_title');
            $table->text('offer_desc')->nullable();
            $table->text('offer_tc')->nullable();
            $table->string('offer_image', 100);
            $table->date('offer_starting_date');
            $table->date('offer_ending_date');
            $table->unsignedInteger('rest_id');

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
        Schema::dropIfExists('offer_info');
    }
}
