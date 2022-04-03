<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RestaurantInfoAddTypeCuisineCostField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rest_info', function (Blueprint $table) {
            $table->string('type')->nullable();
            $table->string('cuisines')->nullable();
            $table->string('cost')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rest_info', function (Blueprint $table) {
            $table->dropColumn(['type','cuisines','cost']);
        });
    }
}
