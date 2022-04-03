<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\FoodRatingReview;
use App\Models\RestaurantRatingReview;
use App\Models\User;
use App\Services\Blocking;
use Carbon\Carbon;

class ProfileController extends Controller
{
    public function getProfile(User $user){
        $result = $user->only(['id', 'fb_profile_name', 'profile_picture', 'bio']);
        $result['birthdate'] = null !== $user->birthdate ? explode('-',$user->birthdate)[2].'/'.explode('-',$user->birthdate)[1] : null;

        $ratings = RestaurantRatingReview::where('user_id', $user->id)
                    ->selectRaw('count(id) as rest_rating, count(has_review) as rest_review')
                    ->first();

        $foodratings = FoodRatingReview::where('user_id', $user->id)
                    ->selectRaw('count(id) as food_rating, count(has_review) as food_review')
                    ->first();

        return response([
            "user" => $result,
            "rest_ratings" => $ratings->rest_rating,
            "rest_reviews" => $ratings->rest_review,
            "food_ratings" => $foodratings->food_rating,
            "food_reviews" => $foodratings->food_review,
            'following_count' => \App\Models\UserFollow::where('user_id', $user->id)->count(),
            'followers_count' => \App\Models\UserFollow::where('follow_id', $user->id)->count(),
            'follow_status' => my_follow_status( auth('api')->id() , $user->id),
            'number_of_reviews' => number_of_ratings($user->id),
            'is_message_blocked' => Blocking::is_message_blocked(
                "USER", // Current user type
                auth('api')->id(), // Current user id
                get_user_type($user) , // Opponent user type
                $user->id // Opponent user or rest id
            ),
            'is_user_blocked' => Blocking::is_user_blocked( auth('api')->user(), $user),
        ], 200);
    }
}
