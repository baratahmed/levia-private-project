<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\RestaurantInfo;
use App\Http\Resources\NewInTownResource;
use App\Http\Resources\SponsoredResource;
use App\LeviaHelpers\RestaurantHelper;
use App\Models\Food;
use App\Models\FoodCategory;
use App\Models\FoodRatingReview;
use App\Models\Message;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\RestaurantRatingReview;
use App\Models\RestFoodDetailsDataset;
use App\Models\UserNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    // public function test(){
    //     return response()->json([
    //         'status' => 'success',
    //     ]);
    // }


    public function home(){
        return response(
            self::dataHome(),
            200
        );
    }

    public function homeWithAdditionalData(){
        return response([
            'home' => self::dataHome(),
            'catmenrest' => self::dataCategoryAndMenu()
        ], 200);
    }

    public static function dataHome(){
        $user = auth('api')->user();

        $ratings = RestaurantRatingReview::where('user_id', $user->id)
                ->selectRaw('count(id) as rest_rating, count(has_review) as rest_review')
                ->first();

        $foodratings = FoodRatingReview::where('user_id', $user->id)
                ->selectRaw('count(id) as food_rating, count(has_review) as food_review')
                ->first();

        $unseen_notifications = UserNotification::where('user_id', $user->id)->where('is_seen',false)->count();
        $unseen_messages = Message::toUser($user->id, "USER")->onlyUnseen()->groupBy('conversation_id')->get()->count();

        $user["rest_ratings"] = $ratings->rest_rating;
        $user["rest_reviews"] = $ratings->rest_review;
        $user["food_ratings"] = $foodratings->food_rating;
        $user["food_reviews"] = $foodratings->food_review;

        // TODO: select genuine new in town restaurants
        $newInTownRestaurants = RestaurantInfo::onlyPublished()->with('district')->orderBy('created_at', 'desc')->get();

        $newInTownRestaurants->each->append('isBookmarked');

        $newInTown = NewInTownResource::collection($newInTownRestaurants);

        $sponsored = SponsoredResource::collection(RestaurantHelper::getSponsored());

        $offers = RestaurantHelper::getOffers();
        $offers->each->append('isBookmarked');

        return [
            "user" => $user,
            "newInTown" => $newInTown,
            "sponsored" => $sponsored,
            "offers" => $offers,
            "current_app_version" => env('CURRENT_APP_VERSION', 29),
            "app_force_update" => env('APP_FORCE_UPDATE', true),
            "unseen_notifications_count" => $unseen_notifications,
            "unseen_messages_count" => $unseen_messages,
            'following_count' => \App\Models\UserFollow::where('user_id', $user->id)->count(),
            'followers_count' => \App\Models\UserFollow::where('follow_id', $user->id)->count(),
            'upcoming_reservation_count' => Reservation::where('user_id', $user->id)->whereDate('reservation_time', '>=', Carbon::now()->toDateTimeString())->count(),
            'current_orders_count' => Order::where('user_id', $user->id)->whereNull('delivered_at')->count()
        ];
    }

    public static function dataCategoryAndMenu(){
        $rests = RestaurantInfo::onlyPublished()->selectRaw('id as rest_id, rest_name')->get();
        $rests->each->setAppends([]);

        // $foods = RestFoodDetailsDataset::selectRaw("distinct(food_name), food_id")->get();
        // DB::enableQueryLog();
        $foods = RestFoodDetailsDataset::whereIn('rest_id', function($query){
            $query->select('id')->from('rest_info')->where('is_published', 1)->whereNull('deleted_at');
        })->whereNotNull('food_name')->selectRaw("distinct(food_name), food_id")->get();
        $foods->each->setAppends([]);
        // return DB::getQueryLog();

        // Don't show foods, taking too much of bandwidth
        // TODO: build an endpoint for keywords
        // $foods = Food::distinct('food_name')->get(['food_id','food_name']);

        $categories = RestFoodDetailsDataset::whereIn('rest_id', function($query){
            $query->select('id')->from('rest_info')->where('is_published', 1)->whereNull('deleted_at');
        })->whereNotNull('food_category_name')->selectRaw("distinct(food_category_name), food_category_id")->get();
        $categories->each->setAppends([]);

        return [
            'categories' => $categories,
            'menu' => $foods,
            'restaurants' => $rests
        ];
    }

    public function getCategoryAndMenu(){
        return response(
            self::dataCategoryAndMenu(),
            200
        );
    }

    public function getSearch(Request $r){
        if ($r->has('text')){
            $rests = RestaurantInfo::onlyPublished()->where('rest_name', 'LIKE', "%".$r->text."%")
                        ->selectRaw('rest_info.*, avg(rest_rating_value) as avg_rating')
                        ->leftJoin('rest_rating', 'rest_info.id', 'rest_rating.rest_id')
                        ->groupBy('rest_rating.rest_id')
                        ->orderBy('avg_rating', 'desc')
                        ->paginate(10);

            $rests->getCollection()->each->append('isBookmarked');

            $foods = RestFoodDetailsDataset::where('food_name', 'LIKE', "%".$r->text."%")
                        ->selectRaw('rest_food_details_dataset.*, avg(food_rating_value) as avg_rating')
                        ->leftJoin('food_rating', function($join){
                            $join->on('food_rating.rest_id', 'rest_food_details_dataset.rest_id');
                            $join->on('food_rating.food_id', 'rest_food_details_dataset.food_id');
                        })
                        ->groupBy('rest_food_details_dataset.rest_id', 'rest_food_details_dataset.food_id')
                        ->orderBy('avg_rating', 'desc')
                        ->with('restaurant')
                        ->whereHas('restaurant', function($q){$q->onlyPublished();})
                        ->paginate(10);

            $foods->getCollection()->each->append('isBookmarked');

            return response([
                'restaurants' => $rests,
                'foods' => $foods
            ], 200);
        }
    }

    public function getSearchPeople(Request $r){
        $user = auth('api')->user();

        if ($r->has('text')){
            DB::enableQueryLog();
            $people = User::where('fb_profile_name', 'LIKE', "%".$r->text."%")
                        ->orWhere('user_email', $r->text);

            // return strlen($r->text);
            if (strlen($r->text) > 9){
                $people = $people->orWhere('contact_no', 'LIKE', "%".$r->text."%");
            }

            $people = $people->selectRaw("id, fb_profile_name, fb_profile_name as name, fb_profile_pic_url")
                        ->paginate(10);

            $followings = $user->getFollowings();

            foreach ($people as $p){
                $p['is_following'] = $followings->where('following_user_id', $p->id)->count() > 0;
                $p['follow_status'] = my_follow_status( auth('api')->id() , $p->id);
                $p['number_of_reviews'] = number_of_ratings($p->id);
            }

            $people->appends($r->input());
            // echo DB::getQueryLog()[0]['query'];
            return response([
                'people' => $people,
            ], 200);
        }
    }

    public function getSearchAdvanced(Request $r){
        $joined_facility = false;
        $joined_payment = false;

        // \Log::info("Searched For: " . json_encode($r->all()));
        // \DB::enableQueryLog();

        $foods = RestFoodDetailsDataset
                ::selectRaw('rest_food_details_dataset.*, avg(food_rating_value) as avg_rating')
                ->leftJoin('food_rating', function($join){
                    $join->on('food_rating.rest_id', 'rest_food_details_dataset.rest_id');
                    $join->on('food_rating.food_id', 'rest_food_details_dataset.food_id');
                })
                ->groupBy(DB::raw('rest_food_details_dataset.rest_id, rest_food_details_dataset.food_id'))
                ->orderBy('avg_rating', 'desc')
                ->with('restaurant')
                ->whereHas('restaurant', function($q){$q->onlyPublished();});

        $rests = RestaurantInfo::onlyPublished()
                ->selectRaw('rest_info.*, avg(rest_rating_value) as avg_rating')
                ->leftJoin('rest_rating', 'rest_info.id', 'rest_rating.rest_id')
                ->groupBy('rest_rating.rest_id')
                ->orderBy('avg_rating', 'desc');

        if ($r->has('text') && $r->text != null){
            $foods = $foods->where('food_name', 'LIKE', "%".$r->text."%");
            $rests = $rests->where('rest_name', 'LIKE', "%".$r->text."%");
        } else {
            // If it doesn't have text, we don't have to search foods,
            // to return empty result on foods, we try to find food with
            // a random absurd name that doesn't have any probability to exist
            // thus, returning an empty dataset
            if ($r->has('price_min') && $r->price_min != 0 || $r->has('price_max') && $r->price_max != 2000 || $r->has('category') && $r->category != null){
                $rests = $rests->where('rest_name', "289f923hf923h892h2893hd8923hd8923h3289y892r3h8923yr8923yr8923yr8923yr8923yr8923yr2893yr8923yr2893yr8923yr3r2");
            } else {
                $foods = $foods->where('food_name', "289f923hf923h892h2893hd8923hd8923h3289y892r3h8923yr8923yr8923yr8923yr8923yr8923yr2893yr8923yr2893yr8923yr3r2");
            }

        }

        if ($r->has('category')){
            $foods = $foods->where('food_category_name', 'LIKE', $r->category);
        }

        if ($r->has('price_min')){
            $foods = $foods->where('unit_price', '>=', $r->price_min);
        }

        if ($r->has('price_max')){
            $foods = $foods->where('unit_price', '<=', $r->price_max);
        }

        // Only join rest_info table if query is required
        if ($r->has('city_id') || $r->has('district_id')){
            $foods = $foods->leftJoin('rest_info', 'rest_food_details_dataset.rest_id', '=', 'rest_info.id');
        }

        if ($r->has('city_id')){
            $foods = $foods->where('city_id', $r->city_id);
            $rests = $rests->where('city_id', $r->city_id);
        }

        if ($r->has('district_id')){
            $foods = $foods->where('district_id', $r->district_id);
            $rests = $rests->where('district_id', $r->district_id);
        }

        $facilities = ['wifi', 'parking', 'smoking_place', 'kids_corner', 'live_music', 'self_service', 'praying_area', 'game_zone', 'tv'];

        foreach($facilities as $facility){
            if ($r->has($facility) && $r->get($facility) == true){
                if (!$joined_facility){
                    $foods = $foods->leftJoin('rest_facility', 'rest_food_details_dataset.rest_id', '=', 'rest_facility.rest_id')->addSelect('rest_facility.*');
                    $rests = $rests->leftJoin('rest_facility', 'rest_info.id', '=', 'rest_facility.rest_id')->addSelect('rest_facility.*');
                    $joined_facility = true;
                }
                $foods = $foods->where($facility, true);
                $rests = $rests->where($facility, true);
            }
        }

        $payment_methods = ['cash', 'visa', 'mastercard', 'bkash', 'rocket', 'nexaspay', 'upay'];
        foreach($payment_methods as $method){
            if ($r->has($method) && $r->get($method) == true){
                if (!$joined_payment){
                    $foods = $foods->leftJoin('rest_payment_methods', 'rest_food_details_dataset.rest_id', '=', 'rest_payment_methods.rest_id')->addSelect('rest_payment_methods.*');
                    $rests = $rests->leftJoin('rest_payment_methods', 'rest_info.id', '=', 'rest_payment_methods.rest_id')->addSelect('rest_payment_methods.*');
                    $joined_payment = true;
                }
                $foods = $foods->where($method, true);
                $rests = $rests->where($method, true);
            }
        }

        $data = $foods->paginate();
        $data->getCollection()->each->append(['rating', 'isBookmarked']);
        $data->appends(request()->except('page'));

        $rests = $rests->paginate();
        $rests->getCollection()->each->append(['isBookmarked']);
        $rests->appends(request()->except('page'));


        // $log = \DB::getQueryLog();
        // return $data;
        // \Log::info(json_encode($log));

        return response([
            'restaurants' => $rests,
            'foods' => $data
        ], 200);
    }
}
