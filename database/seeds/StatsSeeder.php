<?php

use Illuminate\Database\Seeder;
use App\Models\RestaurantInfo;
use Carbon\Carbon;
use App\Models\Stats\Search;
use App\Models\User;
use App\Models\Stats\MapDirection;
use App\Models\Stats\MobileCalls;
use App\Models\Stats\Bookmark;

class StatsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $restaurants = RestaurantInfo::all();

        foreach($restaurants as $rest){
            $count = random_int(50, 100);
            for($i=1; $i<=$count; $i++){
                if (random_boolean_biased()){
                    Search::addCount(User::inRandomOrder()->first(), $rest, static::randomDay());
                }
                if (random_boolean_biased()){
                    MapDirection::addCount(User::inRandomOrder()->first(), $rest, static::randomDay());
                }
                if (random_boolean_biased()){
                    MobileCalls::addCount(User::inRandomOrder()->first(), $rest, static::randomDay());
                }
                if (random_boolean_biased()){
                    $user = User::inRandomOrder()->first();
                    if (!Bookmark::existsCustom($user, $rest)){
                        Bookmark::addCount($user, $rest, static::randomDay());
                    }
                }
            }
        }
    }

    private static function randomDay(){
        return Carbon::now()->subDays(random_int(0, 400));
    }
}
