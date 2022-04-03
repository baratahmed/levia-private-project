<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BookmarkFood;
use App\Models\FoodRatingReview;
use App\Models\RestaurantRatingReview;
use App\Models\Stats\Bookmark;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function getRatingReviews(){
        $user = auth('api')->user();

        $ratings = RestaurantRatingReview::where('user_id', $user->id)
                    ->selectRaw('count(id) as rest_rating, count(has_review) as rest_review')
                    ->first();

        $foodratings = FoodRatingReview::where('user_id', $user->id)
                    ->selectRaw('count(id) as food_rating, count(has_review) as food_review')
                    ->first();
        
        // dd($ratings);
        return response([
            "user_id" => $user->id,
            "rest_ratings" => $ratings->rest_rating,
            "rest_reviews" => $ratings->rest_review,
            "food_ratings" => $foodratings->food_rating,
            "food_reviews" => $foodratings->food_review,
        ], 200);
    }

    public function editUser(Request $r){
        // Fix Birthdate format before validation
        if ($r->has('birthdate')){
            $birthdate = explode('-', $r->birthdate);
            if (count($birthdate) === 3){
                if (strlen($birthdate[0]) < 2){
                    $birthdate[0] = "0" . $birthdate[0];
                }
                if (strlen($birthdate[1]) < 2){
                    $birthdate[1] = "0" . $birthdate[1];
                }

                $r->replace(array_merge($r->except('birthdate'), ['birthdate' => implode('-', $birthdate)]));
            }
        }

        // dd ($r->all());
        // Now, validate the request
        $this->validate($r, [
            'name' => 'sometimes|min:6|max:191',
            'email' => 'sometimes|email|max:191',
            // 'contact' => 'sometimes|digits_between:10,18',
            'bio' => 'sometimes',
            'birthdate' => 'sometimes|date_format:d-m-Y',
            'gender' => 'sometimes',
            'propic' => 'sometimes|file|mimes:jpeg,jpg,png'
        ]);

        // return $r->all();

        $user = auth('api')->user();

        if ($r->has('contact')){
            $found = User::where('contact_no', $r->contact)->first();

            if ($found && $found->id != $user->id){
                throw ValidationException::withMessages([
                    'contact' => 'This contact no. is already in use.'
                ]);
            }
        }

        if ($r->has('name')){
            $user->fb_profile_name = $r->name;
        }
        if ($r->has('email')){
            $user->user_email = $r->email;
        }
        // Phone number can't be changed directly
        // if ($r->has('contact')){
        //     $user->contact_no = $r->contact;
        // }
        if ($r->has('bio')){
            $user->user_bio = $r->bio;
        }
        if ($r->has('birthdate')){
            $user->birthdate = Carbon::createFromFormat('d-m-Y',$r->birthdate, 'Asia/Dhaka');
        }
        if ($r->has('gender') && ($r->gender === 'Male' || $r->gender === 'Female' || $r->gender === 'Other')){
            $user->gender = $r->gender;
        }
        if ($r->has('propic')){
            // return "has propic";
            $file = $r->file('propic');
            $propic = \Image::make($file)->fit(100)->encode('jpg');
            $name = $user->id . "-" . \Illuminate\Support\Str::random(20) . "-" . Carbon::now()->getTimestamp() . ".jpg";
            $name = str_replace(" ", "_", $name);

            Storage::disk('public')->put( 'propic/'.$name, $propic);

            $user->fb_profile_pic_url = "file:".$name;
        }

        $user->save();

        return response([
            'success' => true,
            'user' => $user
        ], 200);
    }

    public function getRestRatings($user=null){
        $self_request = request()->is('api/my/*');
        if ($user === null && $self_request) {
            $user = auth('api')->user();
        } 
        else {
            $user = User::findOrFail($user);
        }

        $ratings = RestaurantRatingReview::with('restaurant:id,rest_name,rest_image_url')->where('user_id', $user->id)->orderBy('id', 'desc')->paginate(10);

        foreach($ratings as $rating){
            $rating->created_at_string = $rating->created_at->diffForHumans();
        }

        return $ratings;
    }

    public function getFoodratings($user=null){
        $self_request = request()->is('api/my/*');
        if ($user === null && $self_request) {
            $user = auth('api')->user();
        } 
        else {
            $user = User::findOrFail($user);
        }

        $ratings = FoodRatingReview::with('restaurant:id,rest_name,rest_image_url')
                ->where('user_id', $user->id)
                ->leftJoin('rest_food_details_dataset', function($join){
                    $join->on('rest_food_details_dataset.rest_id', 'food_rating_review_dataset.rest_id');
                    $join->on('rest_food_details_dataset.food_id', 'food_rating_review_dataset.food_id');
                })
                ->orderBy('id', 'desc')
                ->paginate(10);

        foreach($ratings as $rating){
            $rating->created_at_string = $rating->created_at->diffForHumans();
        }

        return $ratings;
    }
    
    public function getRestReviews($user=null){
        $self_request = request()->is('api/my/*');
        if ($user === null && $self_request) {
            $user = auth('api')->user();
        } 
        else {
            $user = User::findOrFail($user);
        }
        
        $ratings = RestaurantRatingReview::with('restaurant:id,rest_name,rest_image_url')->where('user_id', $user->id)->where('has_review', '!=', null)->orderBy('id', 'desc')->paginate(10);

        foreach($ratings as $rating){
            $rating->created_at_string = $rating->created_at->diffForHumans();
        }

        return $ratings;
    }
    
    public function getFoodreviews($user=null){
        $self_request = request()->is('api/my/*');
        if ($user === null && $self_request) {
            $user = auth('api')->user();
        } 
        else {
            $user = User::findOrFail($user);
        }

        $ratings = FoodRatingReview::with('restaurant:id,rest_name,rest_image_url')
                ->where('user_id', $user->id)
                ->where('has_review', '!=', null)
                ->leftJoin('rest_food_details_dataset', function($join){
                    $join->on('rest_food_details_dataset.rest_id', 'food_rating_review_dataset.rest_id');
                    $join->on('rest_food_details_dataset.food_id', 'food_rating_review_dataset.food_id');
                })
                ->orderBy('id', 'desc')
                ->paginate(10);

        foreach($ratings as $rating){
            $rating->created_at_string = $rating->created_at->diffForHumans();
        }

        return $ratings;
    }

    public function getBookmarks(){
        $user = auth('api')->user();

        $list = Bookmark::with(['restaurant'])->where('user_id', $user->id)->orderBy('id', 'desc')->paginate(10);
        foreach($list as $l){
            $l->restaurant->append(['isBookmarked']);
        }

        return $list;
    }

    public function getBookmarksFood(){
        $user = auth('api')->user();

        $bookmarks = BookmarkFood::with(['restaurant'])
                    ->where('user_id', $user->id)
                    ->orderBy('id', 'desc')
                    ->leftJoin('rest_food_details_dataset', function($join){
                        $join->on('rest_food_details_dataset.rest_id', 'stats_bookmark_foods.rest_id');
                        $join->on('rest_food_details_dataset.food_id', 'stats_bookmark_foods.food_id');
                    })
                    ->paginate(10);

        $bookmarks->getCollection()->each->append(['rating', 'isBookmarked']);
        // $bookmarks->getCollection()->each->append('isBookmarked');

        return $bookmarks;
    }
}
