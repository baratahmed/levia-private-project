<?php

use Illuminate\Database\Seeder;
use App\Models\RestFood;
use App\Models\FoodRating;
use App\Models\FoodReview;
use App\Models\User;
use App\Models\ReviewInfo;

class FoodRatingAndReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get the foods
        $foods = RestFood::all();

        // Populate data
        foreach($foods as $food){
            $numOfRatings = random_int(2, 7); // How many ratings? Randomly generate.
            $usersToGiveRating = User::inRandomOrder()->take($numOfRatings)->get();

            // Give rating from each selected user
            foreach($usersToGiveRating as $user){
                $rating = factory(FoodRating::class)->create([
                    'user_id' => $user->id,
                    'rest_id' => $food->rest_id,
                    'food_id' => $food->food_id
                ]);
                
                // Determine if this rating has a review bound to it.
                $hasReview = random_boolean_biased();

                if ($hasReview){
                    // Insert the review
                    $review = factory(ReviewInfo::class)->create([
                        'user_id' => $user->id
                    ]);

                    // Bind review to restaurant
                    $restReview = factory(FoodReview::class)->create([
                        'review_id' => $review->id,
                        'rest_id' => $food->rest_id,
                        'food_id' => $food->food_id
                    ]);

                    // Bind the review to rating
                    DB::table('rating_review_bind_food')->insert([
                        'rating_id' => $rating->id,
                        'review_id' => $restReview->id
                    ]);
                }
            }
        }
    }
}
