<?php

namespace App\Models;

use App\LeviaHelpers\UserBookmarks;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotion extends Model
{
    use SoftDeletes;
    
    protected $guarded = [];

    public function restaurant(){
        return $this->belongsTo(RestaurantInfo::class, 'rest_id', 'id');
    }

    public function price(){
        return $this->hasOne(PromotionPackagePrice::class, 'id', 'package_id');
    }

    public function scopeOnlyActive($query){
        $query->where('is_active', true)->where('ending_at', '>=', Carbon::now());
    }

    public function getIsBookmarkedAttribute(){
		$user = auth('api')->user();
		return UserBookmarks::get($user)->contains($this->restaurant);
	}
}
