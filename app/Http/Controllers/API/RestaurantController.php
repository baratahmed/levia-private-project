<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\RestaurantInfo;
use App\Models\RestFoodDetailsDataset;
use App\Models\RestaurantRatingReview;
use App\Http\Resources\RestFoodDataResource;
use App\Http\Resources\RestaurantRatingAndReview;
use App\Models\RestFood;
use App\Http\Resources\FoodRatingAndReview;
use App\LeviaHelpers\RestaurantHelper;
use App\Models\BookmarkFood;
use App\Models\Food;
use App\Models\FoodRatingReview;
use Illuminate\Support\Facades\DB;
use App\Models\RestRating;
use App\Models\ReviewInfo;
use App\Models\RestReview;
use App\Models\FoodRating;
use App\Models\FoodReview;
use App\Models\NewsFeed;
use App\Models\Post;
use App\Models\Promotion;
use App\Models\Stats\Search;
use App\Models\Stats\Bookmark;
use App\Models\Stats\MapDirection;
use App\Models\Stats\MobileCalls;
use App\Models\Stats\TotalHeads;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class RestaurantController extends Controller
{
    public function getRestaurant($restaurant){
        $rest = RestaurantInfo::with(['district', 'schedule', 'paymethod', 'properties'])->find($restaurant);
        if (!$rest){
            return response([
                'error' => 'No restaurant found'
            ], 200);
        }
        $rest->append('isBookmarked');

        if (!$rest->is_published){
            return response([
                'error' => 'This restaurant is Unpublished'
            ], 200);
        }

        $food_categories = RestFoodDetailsDataset::where('rest_id', $restaurant)
                            ->groupBy('food_category_id')
                            ->selectRaw('food_category_id, food_category_name, count(food_id) as food_count')
                            ->orderBy('food_category_name', 'asc')
                            ->get();


        // $foods = RestFoodDetailsDataset::where('rest_id', $restaurant)
        //             ->selectRaw('*')
        //             ->get();

        // $foods->each->append('rating');


        $categories = array();
        foreach($food_categories as $category){
            $category_foods = RestFoodDetailsDataset::where('rest_id', $restaurant)
                                ->selectRaw('*')
                                ->where('food_category_id', $category->food_category_id)
                                ->get();
            $category_foods->each->append(['rating', 'isBookmarked']);

            array_push($categories, [
                'food_category_id' => $category->food_category_id,
                'food_category_name' => $category->food_category_name,
                'food_count' => $category->food_count,
                'foods' => RestFoodDataResource::collection($category_foods)
            ]);
        }

        // Add count to statistics
        Search::addCount(auth('api')->user(), $rest);

        // $foodsResource = RestFoodDataResource::collection($foods);

        return [
            'restaurant' => $rest,
            'food_categories' => $categories,
            // 'foods' => $foodsResource
        ];
    }

    public function getRestRatingAndReviews($restaurant){
        $rest = RestaurantInfo::onlyPublished()->with(['district'])->find($restaurant);

        if ($rest==null){
            return response([
                'success' => false,
                'message' => 'The restaurant is no longer available.'
            ], 200);
        }

        $model = RestaurantRatingReview::with('user')
            ->where('rest_id', $rest->id)
            ->orderBy('created_at', 'desc')
            ->whereHas('user', function($q){$q->notDeleted();})
            ->paginate(10);

        RestaurantRatingAndReview::collection($model);
        // dd($model);

        return [
            'restaurant' => $rest,
            // 'ratings' => $ratings,
            'ratings' => $model
        ];
    }

    public function postRestRatingAndReviews($restaurant, Request $request){
        $this->validate($request, [
            'rating_value' => "required|integer|max:5|min:1",
            'has_review' => 'required',
            'image' => 'sometimes|image|mimes:jpeg,jpg,png,gif|max:2048'
        ]);

        $rest = RestaurantInfo::with(['district'])->find($restaurant);
        $user = auth()->user();
        $media = null;

        // Process Image Upload
        if ($request->has_review == "true" && $request->has('image')){
            $file = $request->file('image');
            $name = str_random(10) . '-' .Carbon::now()->toDayDateTimeString() . '-LEVIA' . '.' . $file->getClientOriginalExtension();
            $name = preg_replace('/[,:\ \t]/i', '-', $name);

            // Store the image
            Storage::disk('local')->put('public/review_media_photos/'.$name, file_get_contents($file));

            $media = json_encode(['image' => $name]);
        }
        
        DB::transaction(function () use($request, $user, $rest, $media) {
            $rating = RestRating::insertGetId([
                'user_id' => $user->id,
                'rest_id' => $rest->id,
                'rest_rating_value' => $request->rating_value,
                "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
                "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()
            ]);

            // Add record to statistics
            TotalHeads::insert([
                'rest_id' => $rest->id,
                'created_at' => Carbon::now()
            ]);

            $feed = [
                'user_id' => $user->id,
                'rest_id' => $rest->id,
                'food_id' => null,
                'type' => 'R',
                'action' => 'rated',
                'object' => $rest->rest_name,
                'rating_value' => $request->rating_value,
                'review_text' => null,
                'created_at' => Carbon::now('Asia/Dhaka'),
                'updated_at' => Carbon::now('Asia/Dhaka'),
            ];

            if ($request->has_review == "true" && $request->has('review_text') && $request->review_text != null){
                // Insert the review
                $review = ReviewInfo::insertGetId([
                    'user_id' => $user->id,
                    'review_text' => $request->review_text,
                    'media' => $media,
                    "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
                    "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()
                ]);

                // Bind review to restaurant
                $rest_review = RestReview::insertGetId([
                    'rest_id' => $rest->id,
                    'review_id' => $review
                ]);

                // Bind the review to rating
                DB::table('rating_review_bind')->insert([
                    'rating_id' => $rating,
                    'review_id' => $rest_review
                ]);

                // Add review record to statistics
                TotalHeads::insert([
                    'rest_id' => $rest->id,
                    'created_at' => Carbon::now()
                ]);

                // Add this to newsfeed
                $feed['action'] = 'reviewed';
                $feed['review_text'] = $request->review_text;
                $feed['media'] = $media;

                
            }

            $newsfeed_id = NewsFeed::insertGetId($feed);

            Post::addNewsfeedPost($newsfeed_id, $user->id);
        });
        


        return [
            'message' => 'success',
            'data' => $this->getRestRatingAndReviews($restaurant)
        ];
    }

    public function postRestFoodRatingAndReviews($restaurant, $food, Request $request){
        $this->validate($request, [
            'rating_value' => "required|integer|max:5|min:1",
            'has_review' => 'required',
            'image' => 'sometimes|image|mimes:jpeg,jpg,png,gif|max:2048'
        ]);

        $rest = RestaurantInfo::find($restaurant);
        $restfood = RestFood::where('rest_id', $restaurant)->where('food_id', $food)->firstOrFail();
        $user = auth()->user();
        $food_data = Food::find($food);
        $media = null;

        // Process Image Upload
        if ($request->has_review == "true" && $request->has('image')){
            $file = $request->file('image');
            $name = str_random(10) . '-' .Carbon::now()->toDayDateTimeString() . '-LEVIA' . '.' . $file->getClientOriginalExtension();
            $name = preg_replace('/[,:\ \t]/i', '-', $name);

            // Store the image
            Storage::disk('local')->put('public/review_media_photos/'.$name, file_get_contents($file));

            $media = json_encode(['image' => $name]);
        }
        
        DB::transaction(function () use($request, $user, $restfood, $rest, $food_data, $media) {
            $rating = FoodRating::insertGetId([
                'user_id' => $user->id,
                'rest_id' => $restfood->rest_id,
                'food_id' => $restfood->food_id,
                'food_rating_value' => $request->rating_value,
                "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
                "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()
            ]);


            $feed = [
                'user_id' => $user->id,
                'rest_id' => $rest->id,
                'food_id' => $restfood->food_id,
                'type' => 'F',
                'action' => 'rated',
                'object' => $rest->rest_name.'\'s Menu ' . $food_data->food_name,
                'rating_value' => $request->rating_value,
                'review_text' => null,
                'created_at' => Carbon::now('Asia/Dhaka'),
                'updated_at' => Carbon::now('Asia/Dhaka'),
            ];

            // Add record to statistics
            TotalHeads::insert([
                'rest_id' => $restfood->rest_id,
                'created_at' => Carbon::now()
            ]);

            if ($request->has_review == "true" && $request->has('review_text') && $request->review_text != null){
                // Insert the review
                $review = ReviewInfo::insertGetId([
                    'user_id' => $user->id,
                    'review_text' => $request->review_text,
                    'media' => $media,
                    "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
                    "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()
                ]);

                // Bind review to restaurant
                $food_review = FoodReview::insertGetId([
                    'rest_id' => $restfood->rest_id,
                    'food_id' => $restfood->food_id,
                    'review_id' => $review
                ]);

                // Bind the review to rating
                DB::table('rating_review_bind_food')->insert([
                    'rating_id' => $rating,
                    'review_id' => $food_review
                ]);

                // Add record to statistics
                TotalHeads::insert([
                    'rest_id' => $restfood->rest_id,
                    'created_at' => Carbon::now()
                ]);


                // Add this to newsfeed
                $feed['action'] = 'reviewed';
                $feed['review_text'] = $request->review_text;
                $feed['media'] = $media;
            }

            $newsfeed_id = NewsFeed::insertGetId($feed);

            Post::addNewsfeedPost($newsfeed_id, $user->id);
        });
        

        return [
            'message' => 'success',
            'data' => $this->getRestFoodRatingAndReviews($restaurant, $food)
        ];
    }

    public function getRestFoodRatingAndReviews($restaurant, $food){
        $rest = RestaurantInfo::onlyPublished()->with(['district'])->find($restaurant);

        if ($rest==null){
            return response([
                'success' => false,
                'message' => 'The restaurant is no longer available.'
            ], 200);
        }
        
        $foodData = RestFoodDetailsDataset::where('rest_id', $restaurant)->where('food_id', $food)->firstOrFail();

        $model = FoodRatingReview::with('user')
            ->where('rest_id', $restaurant)
            ->where('food_id', $food)
            ->orderBy('created_at', 'desc')
            ->whereHas('user', function($q){$q->notDeleted();})
            ->paginate(10);

        FoodRatingAndReview::collection($model);

        return [
            'food' => $foodData,
            'food_ratings' => $model
        ];
    }

    public function getNearbyRestaurants(Request $r){
        $this->validate($r,[
            'lat' => ['required','regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'], 
            'long' => ['required','regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/']
        ]);

        $rests = RestaurantInfo::
                    selectRaw("*, ( 6371 * acos( cos( radians(".$r->lat.") ) * cos( radians( rest_latitude ) ) * cos( radians( rest_longitude ) - radians(".$r->long.") ) + sin( radians(".$r->lat.") ) * sin( radians( rest_latitude ) ) ) ) AS distance")
                    // ->take(10)
                    ->havingRaw("distance < 5") // within 5km range
                    ->orderBy('distance', 'asc')
                    ->where('is_published', true)
                    ->get();

        $rests->each->append('isBookmarked');

        return [
            'restaurants' => $rests->map(function($rest){
                $rest['district'] = $rest->district;
                return $rest;
            })
        ];
    }

    public function addBookmark(RestaurantInfo $restaurant){
        $user = auth('api')->user();

        if (!Bookmark::existsCustom($user, $restaurant)){
            Bookmark::addCount($user, $restaurant);

            return response([
                'status' => 'success',
                'message' => 'Successfully bookmarked this restaurant'
            ], 200);
        }

        return response([
            'status' => 'error',
            'message' => 'You already bookmarked this restaurant'
        ], 406);
    }

    public function deleteBookmark(RestaurantInfo $restaurant){
        $user = auth('api')->user();

        if (Bookmark::existsCustom($user, $restaurant)){
            Bookmark::deleteCustom($user, $restaurant);

            return response([
                'status' => 'success',
                'message' => 'Successfully deleted the bookmark'
            ], 200);
        }

        return response([
            'status' => 'error',
            'message' => 'You didn\'t bookmark this restaurant yet'
        ], 406);
    }
    
    
    public function addBookmarkFood(RestaurantInfo $restaurant, Food $food){
        $user = auth('api')->user();

        if (!BookmarkFood::existsCustom($user, $restaurant, $food)){
            BookmarkFood::addCount($user, $restaurant, $food);

            return response([
                'status' => 'success',
                'message' => 'Successfully bookmarked this food'
            ], 200);
        }

        return response([
            'status' => 'error',
            'message' => 'You already bookmarked this food'
        ], 406);
    }

    public function deleteBookmarkFood(RestaurantInfo $restaurant, Food $food){
        $user = auth('api')->user();

        if (BookmarkFood::existsCustom($user, $restaurant, $food)){
            BookmarkFood::deleteCustom($user, $restaurant, $food);

            return response([
                'status' => 'success',
                'message' => 'Successfully deleted the bookmark'
            ], 200);
        }

        return response([
            'status' => 'error',
            'message' => 'You didn\'t bookmark this food yet'
        ], 406);
    }

    public function mapDirection(RestaurantInfo $restaurant){
        $user = auth('api')->user();

        MapDirection::addCount($user, $restaurant);

        return response([
            'status' => 'success',
            'message' => 'Statistics updated',
            'payload' => [
                'rest_latitude' => $restaurant->rest_latitude,
                'rest_longitude' => $restaurant->rest_longitude
            ]
        ], 200);
    }

    public function mobileCall(RestaurantInfo $restaurant){
        $user = auth('api')->user();

        MobileCalls::addCount($user, $restaurant);

        return response([
            'status' => 'success',
            'message' => 'Statistics updated',
            'payload' => [
                'phone' => $restaurant->phone
            ]
        ], 200);
    }

    public function getSponsored(){
        $sponsored = RestaurantHelper::getSponsored(true);
        $sponsored->getCollection()->each->append('isBookmarked');
        return $sponsored;
    }

    public function getOffers(){
        $offers = RestaurantHelper::getOffers(true);
        $offers->getCollection()->each->append('isBookmarked');
        return $offers;
    }
}
