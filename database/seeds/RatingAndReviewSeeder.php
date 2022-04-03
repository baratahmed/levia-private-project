<?php

use Illuminate\Database\Seeder;
use App\Models\RestaurantInfo;
use App\Models\User;
use App\Models\RestRating;
use App\Models\RestReview;
use App\Models\ReviewInfo;

class RatingAndReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Seed rating/review for restaurants
        $rests = RestaurantInfo::all();


        /** 
         * 
         * The binding of rating and review is as follows:-
         *      RestRating -> RatingReviewBind -> RestReview -> ReviewInfo
         */

        // Select Restaurants to Give Ratings
        foreach($rests as $rest){
            $numOfRatings = random_int(2, 7); // How many ratings? Randomly generate.
            $usersToGiveRating = User::inRandomOrder()->take($numOfRatings)->get();

            // Give rating from each selected user
            foreach($usersToGiveRating as $user){
                $rating = factory(RestRating::class)->create([
                    'user_id' => $user->id,
                    'rest_id' => $rest->id
                ]);
                
                // Determine if this rating has a review bound to it.
                $hasReview = random_boolean_biased();

                if ($hasReview){
                    // Insert the review
                    $review = factory(ReviewInfo::class)->create([
                        'user_id' => $user->id
                    ]);

                    // Bind review to restaurant
                    $restReview = factory(RestReview::class)->create([
                        'review_id' => $review->id,
                        'rest_id' => $rest->id
                    ]);

                    // Bind the review to rating
                    DB::table('rating_review_bind')->insert([
                        'rating_id' => $rating->id,
                        'review_id' => $restReview->id
                    ]);
                }
            }
        }

    }
}
