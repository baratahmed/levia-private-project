<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddFinixPlanToRestaurantInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE rest_info CHANGE plan plan ENUM("Hype","Splash","Finix") DEFAULT "Hype";');
        // Schema::table('rest_info', function (Blueprint $table) {
        //     $table->string('plan', ['Hype','Splash','Finix'])->default('Hype')->change();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE rest_info CHANGE plan plan ENUM("Hype","Splash") DEFAULT "Hype";');
        // Schema::table('rest_info', function (Blueprint $table) {
        //     $table->enum('plan', ['Hype','Splash'])->default('Hype')->change();
        // });
    }
}
