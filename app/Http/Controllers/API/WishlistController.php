<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\RestaurantInfo;
use App\Models\RestFood;
use App\Models\Wishlist;
use Carbon\Carbon;

class WishlistController extends Controller
{
    public function addWish(Request $r){
        $this->validate($r,[
            'rest_id' => 'required|exists:rest_info,id',
            'food_id' => 'sometimes|nullable|exists:all_food,food_id'
        ]);

        $user = auth('api')->user();
        $is_food = ($r->has('food_id') && $r->food_id != null);
        $item = $is_food ? 'food' : 'restaurant';

        $wish = Wishlist::where('user_id', $user->id)->where('rest_id', $r->rest_id);

        if ($is_food){ // A food wish

            $rest_food = RestFood::where('rest_id', $r->rest_id)->where('food_id', $r->food_id)->first();

            if ($rest_food == null){
                return response([
                    'success' => false,
                    'message' => 'Bad Request. This restaurant doesn\'t have this food item.'
                ], 422);
            }

            $wish = $wish->where('food_id', $r->food_id)->where('wish_type', 2)->first();
        } else {
            $wish = $wish->where('wish_type',1)->first();
        }

        if ($wish){
            return response([
                'success' => false,
                'message' => 'This ' . $item . ' is already in your wishlist.'
            ], 422);
        }

        Wishlist::insert([
            'user_id' => $user->id,
            'wish_type' => $is_food ? 2 : 1,
            'rest_id' => $r->rest_id,
            'food_id' => $is_food ? $r->food_id : null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        return response([
            'success' => true,
            'message' => 'This ' . $item . ' has been added to your wishlist.'
        ], 200);
    }

    public function deleteWish(Wishlist $wish){
        $user = auth('api')->user();

        if ($wish->user_id != $user->id){
            return response([
                'success' => false,
                'message' => 'Bad request. You don\'t own this wish item'
            ], 422);
        }

        $wish->delete();

        return response([
            'success' => true,
            'message' => 'Wish has been deleted'
        ], 200);
    }

    public function get(Request $r){
        $user = auth('api')->user();

        if ($r->has('type') && $r->type == 'food'){
            return Wishlist::where('user_id', $user->id)
                ->with('restaurant')
                ->leftJoin('rest_food_details_dataset', function($join){
                    $join->on('wishlist.rest_id','=', 'rest_food_details_dataset.rest_id');
                    $join->on('wishlist.food_id','=', 'rest_food_details_dataset.food_id');
                })
                ->where('wish_type', 2)
                ->whereHas('restaurant',function($query){
                    $query->where('is_published', true);
                })
                ->orderBy('id', 'desc')
                // ->append('foodImage')
                ->paginate(10);
        } else {
            return Wishlist::where('user_id', $user->id)
                ->with('restaurant')
                ->where('wish_type', 1)
                ->whereHas('restaurant',function($query){
                    $query->where('is_published', true);
                })
                ->orderBy('id', 'desc')
                ->paginate(10);
        }
    }
}
