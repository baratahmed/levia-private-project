<?php

namespace App\Models;

use App\Models\Stats\StatsHelper;
use App\Models\Stats\TotalHeads;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class BookmarkFood extends Model
{
    use StatsHelper, SoftDeletes;

    protected $table = 'stats_bookmark_foods';
    protected $appends = ['foodImage'];

    public static function existsCustom(User $user, RestaurantInfo $restaurant, Food $food) {
        return static::where('user_id', $user->id)->where('rest_id', $restaurant->id)->where('food_id', $food->food_id)->exists();
    }

    public static function deleteCustom(User $user, RestaurantInfo $restaurant, Food $food) {
        return static::where('user_id', $user->id)->where('rest_id', $restaurant->id)->where('food_id', $food->food_id)->delete();
    }

    public static function addCount(User $user, RestaurantInfo $restaurant, Food $food, $created_at = false) : void {
        
        DB::transaction(function () use($user, $restaurant, $food, $created_at) {
            static::insert([
                'user_id' => $user->id,
                'rest_id' => $restaurant->id,
                'food_id' => $food->food_id,
                'created_at' => ! $created_at ? Carbon::now() : $created_at
            ]);

            TotalHeads::insert([
                'rest_id' => $restaurant->id,
                'created_at' => ! $created_at ? Carbon::now() : $created_at
            ]);
        });
        
    }

    public function restaurant(){
        return $this->hasOne(RestaurantInfo::class, 'id', 'rest_id');
    }

    public function getFoodImageAttribute(){
        $imgName = $this->food_image_url == null ? 'default.png' : $this->food_image_url;
        return asset('storage/rest_food/'. $imgName);
    }

    // Only use with User Bookmarks
    public function getIsBookmarkedAttribute(){
        return true;
    }

    // Only used 
    public function getRatingAttribute(){
		// TODO: Recalculate and store for future reference.

		return DB::table('food_rating_review_dataset')->selectRaw("avg(food_rating_value) as rating, count(id) as count, count(has_review) as review_count")->where('rest_id', $this->rest_id)->where('food_id', $this->food_id)->get();
	}
}
