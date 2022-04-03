<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FoodCategory;
use App\Models\RestFoodDetailsDataset;

class GlobalController extends Controller
{
    public function foodCategoryList(){
		
		return FoodCategory::all();
	}

	public function food_list(Request $request){
		$this->validate($request, [
			'rest_id' => 'exists:rest_info,id'
		]);

		$food_details = RestFoodDetailsDataset::for($request->input('rest_id'))->get();

		return response([
			'success' => true,
			'data' => $food_details,
		]);
		
	}
}
