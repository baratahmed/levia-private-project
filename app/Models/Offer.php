<?php

namespace App\Models;

use App\LeviaHelpers\UserBookmarks;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use \Awobaz\Compoships\Compoships;
    protected $table = 'offer_info';
    protected $guarded = ['_token'];
    protected $appends = ['imageUrl', 'status'];
    protected $primaryKey = "offer_id";

    public function type()
    {
        return $this->hasOne(OfferType::class, 'offer_type_id', 'offer_type_id');
    }

    public function restaurant()
    {
        return $this->hasOne(RestaurantInfo::class, 'id', 'rest_id');
    }
    
    public function food()
    {
        return $this->hasOne(Food::class, 'food_id', 'food_id');
    }
    
    public function food_details()
    {
        return $this->hasOne(RestFoodDetailsDataset::class, ['rest_id', 'food_id'], ['rest_id', 'food_id']);
    }

    public function scopeOnlyActive($query){
        $query->where('offer_ending_date', '>=', Carbon::now());
    }

    public function getImageUrlAttribute(){
		if ($this->offer_image == null){
			return null;
		}
		return asset('storage/offer/'. $this->offer_image);
    }
    
    public function getIsBookmarkedAttribute(){
		$user = auth('api')->user();
		return UserBookmarks::get($user)->contains($this->restaurant);
    }
    
    public function getStatusAttribute(){
        if (Carbon::createFromFormat("Y-m-d",$this->offer_starting_date)->isFuture()){
            return "Upcoming";
        }

        return 
            Carbon::createFromFormat("Y-m-d",$this->offer_ending_date)->isFuture() &&
            Carbon::createFromFormat("Y-m-d",$this->offer_starting_date)->isPast() 
            ? "Ongoing" : "Archived";
    }

}
