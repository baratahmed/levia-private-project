<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    protected $table = 'wishlist';
    protected $guarded = [];
    protected $appends = ['foodImage'];

    public function restaurant(){
        return $this->hasOne(RestaurantInfo::class, 'id', 'rest_id');
    }
    
    // public function food(){
    //     return $this->hasOne(RestFoodDetailsDataset::class, 'food_id', 'food_id')->where('rest_id', $this->rest_id);
    // }

    public function getFoodImageAttribute(){
        // dd($this);
        return $this->food_image_url == null ? null : asset('storage/rest_food/'. $this->food_image_url);
    }
}
