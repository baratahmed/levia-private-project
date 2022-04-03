<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFoodRatingReviewDataset extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            CREATE VIEW food_rating_review_dataset AS
            (
                select 
                    fr.id, fr.user_id, fr.rest_id, fr.food_id, fr.food_rating_value, fr.created_at,fr.updated_at,
                    rrb.id as has_review,
                    ri.id as review_id, ri.review_text, ri.created_at as review_created_at, ri.updated_at as review_updated_at
                    
                from food_rating fr
                    left join rating_review_bind_food rrb on fr.id = rrb.rating_id
                    left join food_review as frev on rrb.review_id = frev.id
                    left join review_info as ri on frev.review_id = ri.id
                    
                order by fr.id asc
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
        DB::statement('DROP VIEW `food_rating_review_dataset`');
    }
}
