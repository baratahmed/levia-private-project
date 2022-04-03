<?php

namespace App\Models;

use App\LeviaHelpers\UserBookmarks;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RestFoodDetailsDataset extends Model
{
    use \Awobaz\Compoships\Compoships;
    protected $table = 'rest_food_details_dataset';
    protected $appends = ['foodImage'];

    public function restaurant(){
        return $this->hasOne(RestaurantInfo::class, 'id', 'rest_id');
    }

    public function getFoodImageAttribute(){
        $imgName = $this->food_image_url == null ? 'default.png' : $this->food_image_url;
        return asset('storage/rest_food/'. $imgName);
    }

    public function getRatingAttribute(){
		// TODO: Recalculate and store for future reference.

		return DB::table('food_rating_review_dataset')->selectRaw("avg(food_rating_value) as rating, count(id) as count, count(has_review) as review_count")->where('rest_id', $this->rest_id)->where('food_id', $this->food_id)->get();
    }
    
    public function getIsBookmarkedAttribute(){
        $user = auth('api')->user();
		return UserBookmarks::get($user)->contains($this->restaurant, $this->food_id);
    }

    public function scopeFor($query, $rest_id){
        return $query->where('rest_id', (int) $rest_id)
        ->where('food_availability', true)
        ->selectRaw('food_id, food_name, food_image_url, unit_price');
        // ->selectRaw('rest_id, food_id, food_name, food_image_url, food_availability, food_category_id, food_category_name');
    }
}
