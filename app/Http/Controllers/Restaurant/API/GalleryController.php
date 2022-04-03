<?php

namespace App\Http\Controllers\Restaurant\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\RestaurantInfo;
use App\Models\RestFoodDetailsDataset;
use Illuminate\Support\Facades\Auth;

class GalleryController extends Controller
{
    public function getImages(){
        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();
        $foods = RestFoodDetailsDataset::where('rest_id', $rest->id)->groupBy('food_image_url')->selectRaw('food_image_url')->get();

        return $foods;
    }
}
