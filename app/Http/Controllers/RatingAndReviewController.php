<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RestaurantInfo;
use App\Models\RestaurantRatingReview;
use App\Http\Resources\RestaurantRatingAndReview;
use App\Models\RestFoodDetailsDataset;
use App\Models\FoodRatingReview;
use App\Http\Resources\FoodRatingAndReview;
use App\Models\ReviewReply;
use App\Models\User;

class RatingAndReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:radmin')->only(['getRestaurantRatings', 'getFoodRatings', 'getFoodAndCategories', 'postRatingReply']);
        $this->middleware('auth:admin')->only(['getRestaurantRatingsAdmin', 'getFoodRatingsAdmin', 'getFoodAndCategoriesAdmin']);
    }

    private function isAdmin(){
        return auth('admin')->check();
    }

    private function isRestAdmin(){
        return auth('radmin')->check();
    }

    // START : Functions for both Admin and Restaurant Admin
    private function getRestaurantRatingsByInstance(RestaurantInfo $restaurant){

        $model = RestaurantRatingReview::with(['user', 'replies'])
            ->where('rest_id', $restaurant->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

            // dd($model);

        $responseData = RestaurantRatingAndReview::collection($model);

        return $responseData;
    }

    private function getFoodRatingsByInstance(RestaurantInfo $restaurant, Request $r){
        if( ! $r->has('food_id') ) {
            return response()->json([
                'message' => 'food_id is required'
            ], 500);
        }

        $this->validate($r, [
            'food_id' => 'integer|exists:all_food,food_id'
        ]);

        $model = FoodRatingReview::with(['user', 'replies'])
            ->where('rest_id', $restaurant->id)
            ->where('food_id', $r->food_id)
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        $responseData = FoodRatingAndReview::collection($model);

        return $responseData;
    }

    private function getFoodAndCategoriesByInstance(RestaurantInfo $restaurant){
        $rest_food_details = RestFoodDetailsDataset::where('rest_id', $restaurant->id)->get();

        return $rest_food_details;
    }
    // END : Functions for both Admin and Restaurant Admin


    // Rest Admin Functions
    public function getRestaurantRatings(){
        $radmin = auth('radmin')->user();
        $restaurant = RestaurantInfo::where('radmin_id', $radmin->id)->firstOrFail();

        return $this->getRestaurantRatingsByInstance($restaurant);
    }

    public function getFoodRatings(Request $r){
        $radmin = auth('radmin')->user();
        $restaurant = RestaurantInfo::where('radmin_id', $radmin->id)->firstOrFail();

        return $this->getFoodRatingsByInstance($restaurant, $r);
    }

    public function getFoodAndCategories(){
        $radmin = auth('radmin')->user();
        $restaurant = RestaurantInfo::where('radmin_id', $radmin->id)->firstOrFail();

        return $this->getFoodAndCategoriesByInstance($restaurant);
    }

    // Core Admin Functions
    public function getRestaurantRatingsAdmin(RestaurantInfo $restaurant){
        return $this->getRestaurantRatingsByInstance($restaurant);
    }

    public function getFoodRatingsAdmin(RestaurantInfo $restaurant, Request $r){
        return $this->getFoodRatingsByInstance($restaurant, $r);
    }

    public function getFoodAndCategoriesAdmin(RestaurantInfo $restaurant){
        return $this->getFoodAndCategoriesByInstance($restaurant);
    }

    // Core Admin Functions for Users
    public function getRestaurantRatingsUser(User $user){
        $model = RestaurantRatingReview::with('user')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $responseData = RestaurantRatingAndReview::collection($model);

        return $responseData;
    }

    public function getFoodRatingsUser(User $user, Request $r){
        if( ! $r->has('food_id') ) {
            return response()->json([
                'message' => 'food_id is required'
            ], 500);
        }

        $this->validate($r, [
            'food_id' => 'integer|exists:all_food,food_id'
        ]);

        $model = FoodRatingReview::with('user')
            ->where('user_id', $user->id)
            ->where('food_id', $r->food_id)
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        $responseData = FoodRatingAndReview::collection($model);

        return $responseData;
    }

    public function getFoodAndCategoriesUser(User $user){
        $rest_food_details = RestFoodDetailsDataset::whereIn('rest_id', function($query) use($user){
            $query->from('food_rating_review_dataset')
                    ->select('rest_id')
                    ->where('user_id', $user->id);
        })->get();

        return $rest_food_details;
    }

    public function postRatingReply(Request $r){
        $this->validate($r, [
            'reply' => 'required',
            'rating_id' => 'required|exists:rest_rating,id'
        ]);

        $radmin = auth('radmin')->user();

        $rating = RestaurantRatingReview::findOrFail($r->rating_id);
        if (!$rating->has_review){
            return response([
                'success' => false,
                'message' => 'Sorry, this rating doesn\'t have a review'
            ], 422);
        }

        $reply = ReviewReply::where('review_id', $rating->review_id)->first();
        if ($reply){
            return response([
                'success' => false,
                'message' => 'Sorry, you have already replied to this review'
            ], 422);
        }

        $reply = new ReviewReply();
        $reply->review_id = $rating->review_id;
        $reply->user_id = $rating->user_id;
        $reply->rest_id = $radmin->restaurant->id;
        $reply->reply_text = $r->reply;

        if ($reply->save()){
            return response([
                'success' => true,
                'data' => $reply
            ], 200);
        } else {
            return response([
                'success' => false,
                'message' => 'Something went wrong'
            ]);
        }

    }

    public function postFoodRatingReply(Request $r){
        $this->validate($r, [
            'reply' => 'required',
            'rating_id' => 'required|exists:food_rating,id'
        ]);

        $radmin = auth('radmin')->user();

        $rating = FoodRatingReview::findOrFail($r->rating_id);
        if (!$rating->has_review){
            return response([
                'success' => false,
                'message' => 'Sorry, this rating doesn\'t have a review'
            ], 422);
        }

        $reply = ReviewReply::where('review_id', $rating->review_id)->first();
        if ($reply){
            return response([
                'success' => false,
                'message' => 'Sorry, you have already replied to this review'
            ], 422);
        }

        $reply = new ReviewReply();
        $reply->review_id = $rating->review_id;
        $reply->user_id = $rating->user_id;
        $reply->rest_id = $radmin->restaurant->id;
        $reply->reply_text = $r->reply;

        if ($reply->save()){
            return response([
                'success' => true,
                'data' => $reply
            ], 200);
        } else {
            return response([
                'success' => false,
                'message' => 'Something went wrong'
            ]);
        }

    }
}
