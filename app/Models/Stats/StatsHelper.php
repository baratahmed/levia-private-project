<?php

namespace App\Models\Stats;

use App\Models\User;
use App\Models\RestaurantInfo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait StatsHelper {
    use StatsFilter;
    
    public static function addCount(User $user, RestaurantInfo $restaurant, $created_at = false) : void {
        
        DB::transaction(function () use($user, $restaurant, $created_at) {
            static::insert([
                'user_id' => $user->id,
                'rest_id' => $restaurant->id,
                'created_at' => ! $created_at ? Carbon::now() : $created_at
            ]);

            TotalHeads::insert([
                'rest_id' => $restaurant->id,
                'created_at' => ! $created_at ? Carbon::now() : $created_at
            ]);
        });
        
    }
}