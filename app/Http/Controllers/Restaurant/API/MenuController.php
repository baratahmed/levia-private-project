<?php

namespace App\Http\Controllers\Restaurant\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RestaurantController;
use App\Models\FoodCategory;
use App\Models\RestaurantInfo;
use App\Models\RestFood;
use App\Models\RestFoodDetailsDataset;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    public function system_categories(){
        return FoodCategory::select(['food_category_id', 'food_category_name'])->get();
    }

    public function getCategoriesAndMenus(){
        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();

        // $foods = RestFoodDetailsDataset::selectRaw("distinct(food_name), food_id")->get();
        // DB::enableQueryLog();
        $foods = RestFoodDetailsDataset::
            where('rest_id', $rest->id)->
            whereNotNull('food_name')->
            selectRaw("distinct(food_name), food_id, food_category_id, unit_price, description, food_availability, food_image_url")->get();
        // $foods->each->setAppends([]);

        // dd($foods);
        // return DB::getQueryLog();

        // Don't show foods, taking too much of bandwidth
        // TODO: build an endpoint for keywords
        // $foods = Food::distinct('food_name')->get(['food_id','food_name']);

        $categories = RestFoodDetailsDataset::
            where('rest_id', $rest->id)->
            whereNotNull('food_category_name')->
            selectRaw("distinct(food_category_name), food_category_id")->
            get();
        $categories->each->setAppends([]);

        $foods_by_category = [];

        foreach( $foods as $food ){
            $foods_by_category['category:'.$food->food_category_id][] = $food;
        }

        foreach( $categories as $category ){
            $category['menu'] = $foods_by_category['category:'.$category->food_category_id];
        }

        return [
            'categories' => $categories,
            // 'menu' => $foods
        ];
    }

    public function addCategory(Request $request){
        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();

        $restaurant_controller = new RestaurantController;
        $response = $restaurant_controller->addCategory($request, true);

        return $response;
    }

    public function editCategory(Request $request){
        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();

        $restaurant_controller = new RestaurantController;
        $response = $restaurant_controller->editcategory($request, true);

        return $response;
    }

    public function deleteCategory(Request $request){
        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();

		$this->validate($request, [
			"category_id" => "required|integer|exists:food_category,food_category_id",
		]);

		// dd($request->all());
		// Delete Category
		if (RestFood::where('rest_id', $rest->id)->where('food_category_id', $request->category_id)->exists()){
			RestFood::where('rest_id', $rest->id)->where('food_category_id', $request->category_id)->delete();

            return response([
                'success' => true,
                'message' => 'Category and associated menu items has been deleted',
            ]);
		} 

        
        return response([
            'success' => false,
            'message' => 'Category not found',
        ], 404);
    }

    public function addMenu(Request $request){
        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();

        $restaurant_controller = new RestaurantController;
        $response = $restaurant_controller->addMenu($request, true);

        return $response;
    }
    
    public function editMenu(Request $request){
        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();

        $restaurant_controller = new RestaurantController;
        $response = $restaurant_controller->editMenu($request, true);

        return $response;
    }
    
    public function toggleMenu(Request $request){
        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();

        $restaurant_controller = new RestaurantController;
        $response = $restaurant_controller->toggleMenu($request, true);

        return $response;
    }
    
    public function deleteMenu(Request $request){
        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();

        $restaurant_controller = new RestaurantController;
        $response = $restaurant_controller->deleteMenu($request, true);

        return $response;
    }
}
