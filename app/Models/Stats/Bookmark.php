<?php

namespace App\Models\Stats;

use Illuminate\Database\Eloquent\Model;
use App\Models\RestaurantInfo;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bookmark extends Model
{
    use StatsHelper, SoftDeletes;

    protected $table = 'stats_bookmark';

    public static function existsCustom(User $user, RestaurantInfo $restaurant) {
        return static::where('user_id', $user->id)->where('rest_id', $restaurant->id)->exists();
    }

    public static function deleteCustom(User $user, RestaurantInfo $restaurant) {
        return static::where('user_id', $user->id)->where('rest_id', $restaurant->id)->delete();
    }

    public function restaurant(){
        return $this->hasOne(RestaurantInfo::class, 'id', 'rest_id');
    }

    // Only use with User Bookmarks
    public function getIsBookmarkedAttribute(){
        return true;
    }
}
