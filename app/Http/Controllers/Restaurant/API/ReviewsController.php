<?php

namespace App\Http\Controllers\Restaurant\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\FoodRatingReview;
use App\Models\RestaurantInfo;
use App\Models\RestaurantRatingReview;
use App\Models\RestFood;
use Illuminate\Support\Facades\Auth;

class ReviewsController extends Controller
{
    public function getRestaurantReviews(){
        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();

        $ratings = RestaurantRatingReview::
            where('rest_id', $rest->id)->
            // orderBy('offer_id', 'desc')->
            paginate(10);

        return response([
            'success' => true,
            'data' => [
                'ratings' => $ratings
            ],
        ]);
    }
    
    public function getRestaurantFoodReviews(Food $food){
        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();

        $rest_food = RestFood::where('rest_id', $rest->id)->where('food_id', $food->food_id)->first();

        // dd($rest_food);
        if (! $rest_food ){
            return response([
                'success' => false,
                'message' => 'This food does not belong to this restaurant',
            ]);
        }

        $ratings = FoodRatingReview::
            where('rest_id', $rest->id)->
            where('food_id', $food->food_id)->
            // orderBy('offer_id', 'desc')->
            paginate(10);

        return response([
            'success' => true,
            'data' => [
                'ratings' => $ratings
            ],
        ]);
    }
}
