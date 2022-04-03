<?php
namespace App\LeviaHelpers;

use App\Models\Offer;
use App\Models\Promotion;

class RestaurantHelper {
    public static function getSponsored($pagination = false){
        // $user = auth('api')->user();

        $restaurants = Promotion::onlyActive()
                        ->with('restaurant')
                        ->whereHas('restaurant', function($q){$q->onlyPublished();})
                        ->orderBy('starting_at', 'desc');

        return $pagination ? $restaurants->paginate(10) : $restaurants->take(10)->get();
    }

    public static function getOffers($pagination = false){
        // $user = auth('api')->user();

        $restaurants = Offer::onlyActive()
                        ->with('restaurant')
                        ->whereHas('restaurant', function($q){$q->onlyPublished();})
                        ->with('type')
                        ->orderBy('offer_starting_date', 'desc');

        return $pagination ? $restaurants->paginate(10) : $restaurants->take(10)->get();
    }
}