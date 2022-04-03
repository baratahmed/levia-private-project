<?php
namespace App\LeviaHelpers;

use App\Models\BookmarkFood;
use App\Models\Food;
use App\Models\RestaurantInfo;
use App\Models\Stats\Bookmark;

class UserBookmarks {
    public $rests = null;
    public $foods = null;
    private static $instance = null;

    public static function get($user){
        if (static::$instance == null){
            $i = new UserBookmarks;
            $i->rests = Bookmark::select('rest_id')->where('user_id', $user->id)->get();
            $i->foods = BookmarkFood::select(['rest_id','food_id'])->where('user_id', $user->id)->get();
            static::$instance = $i;
        }
        return static::$instance;
    }

    public function contains(RestaurantInfo $rest, $food = null){
        if ($food == null){
            return $this->rests->where('rest_id',$rest->id)->first() !== null;
        }
        return $this->foods->where('rest_id',$rest->id)->where('food_id',$food)->first() !== null;
    }
}
