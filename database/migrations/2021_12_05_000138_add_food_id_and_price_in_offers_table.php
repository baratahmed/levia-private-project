<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFoodIdAndPriceInOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offer_info', function (Blueprint $table) {
            $table->unsignedInteger('food_id')->nullable()->default(NULL);
            $table->float('price', 8, 4)->nullable()->default(NULL);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offer_info', function (Blueprint $table) {
            $table->dropColumn(['food_id', 'price']);
        });
    }
}
