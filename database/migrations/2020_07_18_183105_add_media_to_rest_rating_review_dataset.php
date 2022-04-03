<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMediaToRestRatingReviewDataset extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            CREATE OR REPLACE VIEW rest_rating_review_dataset AS
            (
                select 
                    rr.id, rr.user_id, rr.rest_id, rr.rest_rating_value, rr.created_at,rr.updated_at,
                    rrb.id as has_review,
                    ri.id as review_id, ri.review_text, ri.media, ri.created_at as review_created_at, ri.updated_at as review_updated_at
                    
                from rest_rating rr
                    left join rating_review_bind rrb on rr.id = rrb.rating_id
                    left join rest_review as rrev on rrb.review_id = rrev.id
                    left join review_info as ri on rrev.review_id = ri.id
                    
                order by rr.id asc
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
        // Do nothing
    }
}
