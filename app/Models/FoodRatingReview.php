<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Stats\StatsFilter;

class FoodRatingReview extends Model
{
    use StatsFilter;
    
    protected $table="food_rating_review_dataset";
    protected $appends = ['foodImage'];

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function restaurant(){
        return $this->hasOne(RestaurantInfo::class, 'id', 'rest_id');
    }

    public function replies(){
        return $this->hasMany(ReviewReply::class, 'review_id', 'review_id');
    }

    public function getFoodImageAttribute(){
        $imgName = $this->food_image_url == null ? 'default.png' : $this->food_image_url;
        return asset('storage/rest_food/'. $imgName);
    }

    public function getReviewImageAttribute(){
        if ($this->media != null){
            $decoded = json_decode($this->media);

            $images = collect($decoded->image);
            // Generate URL from names
            $images = $images->map(function($image){
                return asset('storage/review_media_photos/'. $image);
            });

            return $images;
        }
    }
}
