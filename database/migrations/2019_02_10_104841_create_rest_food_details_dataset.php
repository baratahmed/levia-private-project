<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRestFoodDetailsDataset extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            CREATE VIEW rest_food_details_dataset AS (
                select 
                    rf.rest_id, rf.food_id, rf.unit_price, rf.description, rf.food_image_url, rf.food_availability, af.food_name, rf.food_category_id, fc.food_category_name
            
                from rest_food rf
                left join all_food af on rf.food_id = af.food_id
                left join food_category fc on rf.food_category_id = fc.food_category_id
            )
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW `rest_food_details_dataset`');
    }
}
