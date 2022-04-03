<?php

namespace App\Http\Controllers;

use App\Models\RestaurantInfo;
use App\Models\RestSchedule;
use App\Models\RestProperty;
use App\Models\District;
use App\Models\FoodCategory;
use App\Models\Food;
use App\Models\RestFood;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Offer;
use App\Models\Promotion;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use App\Models\RestFoodDetailsDataset;
use App\Models\UserNotification;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class RestaurantController extends Controller
{
    public function viewSettingsPage(){
		$radmin = Auth::guard('radmin')->user();
        $userID = $radmin->id;
        $rest = RestaurantInfo::where("radmin_id", $userID)->first();
		$properties = RestProperty::where('rest_id', $rest->id)->first();
    	$districts = District::orderBy('district_name', 'asc')->get();
		$schedules = RestSchedule::where('rest_id', $rest->id)->orderBy('day_id','asc')->get();
        $weekdays = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
		$paymethod = $rest->paymethod;
   		return view('RestaurantOwner/Settings')->with(compact("rest",'districts','schedules','properties', 'weekdays', 'radmin', 'paymethod'));
	}
	
	public function viewMenuDetailsPage(){
        $userID = Auth::guard('radmin')->user()->id;
		$rest = RestaurantInfo::where("radmin_id", $userID)->first();
		
		$food_categories = FoodCategory::orderBy('food_category_name', 'asc')->get();
		return view('RestaurantOwner/MenuDetails')->with(compact("rest", 'food_categories'));
	}

    public function addRestaurant(Request $request){
        $this->validate($request, [
            "rest_name" => "required",
            // "rest_street" => "required",
            // "police_station" => "required",
            // "rest_post_code" => "required",
            "district_id" => "required|integer",
            "phone_no" => "required",
        ]);

        $userID = Auth::guard('radmin')->user()->id;
        $restuarant = "";

        if ($request->rest_image_url) {
            $this->validate($request, [
                "rest_image_url" => "required|mimes: jpg,jpeg,png",
            ]);

            $icon = $request->rest_image_url;

            $iconName = str_random(20) . date('His') . "." . \File::extension($icon); //random string+current time

            $icon->storeAs('public/rest_logo', $iconName);

            $restuarant = RestaurantInfo::create([
                "rest_name" => $request->rest_name,
                "rest_street" => $request->rest_street,
                "rest_image_url" => $iconName,
                "police_station" => $request->police_station,
                "rest_post_code" => $request->rest_post_code,
                "district_id" => $request->district_id,
                "phone" => $request->phone_no,
                "road_no" => $request->road_no,
                "rest_tax_no" => $request->rest_tax_no,
                // "email" => $request->email,
                "radmin_id" => $userID,
            ]);

            // return redirect("/dashboard");
        }

        $restuarant = RestaurantInfo::create([
            "rest_name" => $request->rest_name,
            "rest_street" => $request->rest_street,
            "police_station" => $request->police_station,
            "rest_post_code" => $request->rest_post_code,
            "district_id" => $request->district_id,
            "phone" => $request->phone_no,
            "road_no" => $request->road_no,
            "rest_tax_no" => $request->rest_tax_no,
            // "email" => $request->email,
            "radmin_id" => $userID,
        ]);

        $data = $this->saveRestaurantSchedule($request, $restuarant->id);
        $data = $this->saveRestaurantProperty($request, $restuarant->id);

        return redirect("/dashboard");
    }

    public function saveRestaurantInfo(Request $request){
        $userID = Auth::guard('radmin')->user()->id;
        $rest = RestaurantInfo::where("radmin_id", $userID)->first();

        if($request->rest_image_url){
            $icon = $request->rest_image_url;

            $iconName = str_random(20) . date('His') . "." . $icon->extension(); //random string+current time
	    	// $icon_name = $rest->id."_".$rest->rest_name.".".$icon->extension();

	    	Storage::delete('public/rest_logo/'.$rest->rest_image_url);
	    	$icon->storeAs('public/rest_logo',$iconName);

        	// $rest->update($request->except(['payment_methods', 'lat', 'lng']));
    	   	$rest->update([
	            "rest_image_url" => $iconName
	        ]);
		}

		if (in_array($request->business_category, ['Restaurant', 'Catering House', 'Home Kitchen'])){
			$business_category = $request->business_category;
		} else {
			$business_category = 'Restaurant';
		}
		
		$rest->update($request->except(['payment_methods', 'lat', 'lng', 'rest_image_url', 'rest_plan', 'business_category']));
		$rest->rest_latitude = $request->lat;
		$rest->rest_longitude = $request->lng;
		$rest->business_category = $business_category;
		$payment = $rest->getPaymentMethods();
		// dd($payment);
		$all_methods = ["cash", "visa", "mastercard", "bkash", "rocket", "nexaspay", "upay"];

		foreach($all_methods as $method){
			if (array_search($method, $request->payment_methods) !== false){
				$payment->{$method} = true;
			} else {
				$payment->{$method} = false;
			}
		}
		$rest->save();
		$rest->paymethod()->save($payment);
			   

        return redirect()->back();
    }

    public function updateRestaurantSchedule(Request $request)
    {
        $userID = Auth::guard('radmin')->user()->id;
        $rest = RestaurantInfo::where("radmin_id", $userID)->first();

        $this->saveRestaurantSchedule($request, $rest->id);

        return redirect()->back();

    }

    public function updateRestaurantProperty(Request $request)
    {
        $userID = Auth::guard('radmin')->user()->id;
        $rest = RestaurantInfo::where("radmin_id", $userID)->first();

        $this->saveRestaurantProperty($request, $rest->id);

        return redirect()->back();

    }

    public function saveRestaurantSchedule(Request $request, $rest_id){
        $data = array();
        $id = $rest_id;
		// dump($request->all());
        $data = $this->setScheduleDataArray($request, $rest_id);
		// dd ($data);

        //remove previous data
        $rest = RestSchedule::where('rest_id', $id)->delete();

        //insert into database
        RestSchedule::insert($data[0]);


        // update information database for weekend
        if(count($data[1]) == 2){
	        RestaurantInfo::where('id', $id)->update([
	        	"weekend1" => $data[1][0],
	        	"weekend2" => $data[1][1],
	        ]);
        }
        else if(count($data[1]) == 1){
	        RestaurantInfo::where('id',1)->update([
	        	"weekend1" => $data[1][0]
	        ]);
        }
    }

    private function setScheduleDataArray($request, $rest_id){
    	$day = array();
    	$weekend = array();
    	$id = $rest_id;
    	$open = $request->opening_time;
    	$close = $request->closing_time;

    	if( isset($open[0]) && $open[0] !== null && $open[0] !== '' && isset($close[0]) && $close[0] !== null && $close[0] !== '' ){
    		array_push($day, array("rest_id" => $id,"day_id"=>"1","day"=>"Sunday","opening_time"=>$open[0],"closing_time"=>$close[0]));
    	}else{
    		array_push($weekend,"Sunday");
    	}

    	if( isset($open[1]) && $open[1] !== null && $open[1] !== '' && isset($close[1]) && $close[1] !== null && $close[1] !== '' ){
    		array_push($day, array("rest_id" => $id,"day_id"=>"2","day"=>"Monday","opening_time"=>$open[1],"closing_time"=>$close[1]));
    	}else{
    		array_push($weekend,"Monday");
    	}

    	if( isset($open[2]) && $open[2] !== null && $open[2] !== '' && isset($close[2]) && $close[2] !== null && $close[2] !== '' ){
    		array_push($day, array("rest_id" => $id,"day_id"=>"3","day"=>"Tuesday","opening_time"=>$open[2],"closing_time"=>$close[2]));
    	}else{
    		array_push($weekend,"Tuesday");
    	}

    	if( isset($open[3]) && $open[3] !== null && $open[3] !== '' && isset($close[3]) && $close[3] !== null && $close[3] !== '' ){
    		array_push($day, array("rest_id" => $id,"day_id"=>"4","day"=>"Wednesday","opening_time"=>$open[3],"closing_time"=>$close[3]));
    	}else{
    		array_push($weekend,"Wednesday");
    	}

    	if( isset($open[4]) && $open[4] !== null && $open[4] !== '' && isset($close[4]) && $close[4] !== null && $close[4] !== '' ){
    		array_push($day, array("rest_id" => $id,"day_id"=>"5","day"=>"Thursday","opening_time"=>$open[4],"closing_time"=>$close[4]));
    	}else{
    		array_push($weekend,"Thursday");
    	}

    	if( isset($open[5]) && $open[5] !== null && $open[5] !== '' && isset($close[5]) && $close[5] !== null && $close[5] !== '' ){
    		array_push($day, array("rest_id" => $id,"day_id"=>"6","day"=>"Friday","opening_time"=>$open[5],"closing_time"=>$close[5]));
    	}else{
    		array_push($weekend,"Friday");
    	}

    	if( isset($open[6]) && $open[6] !== null && $open[6] !== '' && isset($close[6]) && $close[6] !== null && $close[6] !== '' ){
    		array_push($day, array("rest_id" => $id,"day_id"=>"7","day"=>"Saturday","opening_time"=>$open[6],"closing_time"=>$close[6]));
    	}else{
    		array_push($weekend,"Saturday");
    	}

    	return array($day, $weekend);
    }

    public function saveRestaurantProperty(Request $request,$rest_id){
        $data = array();
        $data = $this->setPropertyDataArray($request, $rest_id);

        RestProperty::where('rest_id', $rest_id)->delete(); //remove the previous record
        RestProperty::create($data); //create new one
    }


    private function setPropertyDataArray($request, $rest_id){
    	$data = array("rest_id"=> $rest_id);

    	if($request->parking){
    		$data['parking']=1;
    	}else{
    		$data['parking']=0;
    	}

    	if($request->wifi){
    		$data['wifi']=1;
    	}else{
    		$data['wifi']=0;
    	}

    	if($request->smoking_place){
    		$data['smoking_place']=1;
    	}else{
    		$data['smoking_place']=0;
    	}

    	if($request->kids_corner){
    		$data['kids_corner']=1;
    	}else{
    		$data['kids_corner']=0;
    	}

    	if($request->live_music){
    		$data['live_music']=1;
    	}else{
    		$data['live_music']=0;
    	}

    	if($request->self_service){
    		$data['self_service']=1;
    	}else{
    		$data['self_service']=0;
    	}

    	if($request->praying_area){
    		$data['praying_area']=1;
    	}else{
    		$data['praying_area']=0;
    	}

    	if($request->game_zone){
    		$data['game_zone']=1;
    	}else{
    		$data['game_zone']=0;
    	}

    	if($request->tv){
    		$data['tv']=1;
    	}else{
    		$data['tv']=0;
		}
		
		
		foreach($request->only([
			'catering',
			'delivery',
			'good_for_groups',
			'good_for_kids',
			'takes_reservations',
			'take_out',
			'waiter_service',
			'walk_ins_welcome',
			'parking_lot',
			'soft_music',
		]) as $key => $value){
			$data[$key] = $value == "on" ? 1 : 0;
		}

    	return $data;
    }

    public function addCategory(Request $request, $is_api_call = false){
		if ($is_api_call){
			$radmin = auth('api_restaurant')->user();
		} else {
			$radmin = auth('radmin')->user();
		}
		$rest = $radmin->restaurant;
		// dd($request->all());
        $this->validate($request, [
			"categoryId" => "integer|nullable",
			"categoryName" => 'required|max:50',
			"food_name" => 'required',
			"food_name.*" => 'required|max:100',
			"unit_price" => "required",
			"unit_price.*" => "required|numeric",
			// "food_image_url.*" => "sometimes|nullable|mimes:jpeg,jpg,png"
		]);
		
		// dd($request->food_image_url);

		if ($request->categoryId != null){
			$category = FoodCategory::where('food_category_id', $request->categoryId)
							->where("food_category_name", $request->categoryName)
							->first();
		} else {
			$category = FoodCategory::where("food_category_name", $request->categoryName)
							->first();
		}
		
		// get if food category name is already exists or not

		if($category == null){
			// create category from user input
			$category = FoodCategory::create([
				'food_category_name' => $request->categoryName
			]);
			$categoryId = $category->food_category_id;
		} else {
			$categoryId = $category->food_category_id;
		}

		$data = array();
		$names = [];

		$all_food = Food::all();
		// dd($all_food);
		//add all food name into array
        for($i=0; $i<sizeof($request->food_name); $i++){
            if($request->food_name[$i] != null){
				// Make sure the food doesn't exist on 'all_food' table
				if (!$all_food->contains("food_name", $request->food_name[$i])){
					array_push($data, array(
						"food_name"=>$request->food_name[$i]
					));
				}

				array_push($names, $request->food_name[$i]);
            }
		}

		//insert all food name data into database
		Food::insert($data);

		//find all ids of newly added data from database
		$ids = Food::whereIn('food_name', $names)->get();

		$food_data = [];
		//add all food description into array

		// Validate the request and database records for "foods"
		if (sizeof($request->food_name) > sizeof($ids)){
			Log::error("Request and IDs have different size. Insertion aborted. \nFOOD_NAME: " . implode(',',$request->food_name) . "\nIDS:" . $ids->map(function($id){return [$id->food_id, $id->food_name];}));
			throw ValidationException::withMessages([
					'food_name' => 'Something went wrong. Please try again.'
			]);
		}

		for ($i=0; $i<sizeof($request->food_name); $i++) {
			// dd($request->food_image_url[$i]);
			if ($request->has('food_image_url') && $request->food_image_url[$i] !== 'null'){
				//save food image in srtorage
				$picture = $request->food_image_url[$i];
				// dd($picture->getMimeType());

				if(substr($picture->getMimeType(), 0, 5) != 'image') {
					// this is not an image
					$error = \Illuminate\Validation\ValidationException::withMessages([
						'Not Image' => ['Uploaded file for food ' + $ids[$i]->food_name + ' is not an image file']
					]);
					throw $error;
				}

				//generate random name
				$picture_name = str_random(10) . '_' . Carbon::now()->getTimestamp() . '.' . $picture->getClientOriginalExtension();

				//store in storage
				$picture->storeAs('public/rest_food', $picture_name);
			} else {
				$picture_name = null;
			}

			// Get the food id;
			$food_id = $ids->where('food_name', $request->food_name[$i])->first();

			// Validate the request and database records for "foods"
			if (!$food_id){
				Log::error('The name ' . $request->food_name[$i] . ' is causing a problem. We will fix it.');
				throw ValidationException::withMessages([
						'food_name' => 'The name ' . $request->food_name[$i] . ' is causing a problem. We will fix it.'
				]);
			}

			array_push($food_data, array(
				"rest_id" => $rest->id,
				"food_id" => $food_id->food_id,
				"unit_price" => $request->unit_price[$i],
				"food_image_url" => $picture_name,
				"food_category_id" => $categoryId,
				"description" => $request->has('description') ? $request->description[$i] : null,
				"food_availability" => true
			));
		}

		//insert all food description data into database
		try {
			RestFood::insert($food_data);
		} catch(QueryException $e){
			// dd($e);
			$error = \Illuminate\Validation\ValidationException::withMessages([
				// 'Duplicate Entry' => ['The food ' . Food::find(27)->food_name . ' already exists in your menu under this category.'],
				'Problem' => $e->getMessage()
			]);
			throw $error;
		}

		$payload = RestFoodDetailsDataset::where('rest_id', $rest->id)->get();

		return response([
			'message' => 'success',
			'payload' => $payload
		], 200);
	}

	public function editcategory(Request $request, $is_api_call = false){
		if ($is_api_call){
			$radmin = auth('api_restaurant')->user();
		} else {
			$radmin = auth('radmin')->user();
		}

		$rest = $radmin->restaurant;
		$this->validate($request, [
			"category_id" => "required|integer|exists:food_category,food_category_id",
			"category_name" => "sometimes|min:2|max:150"
		]);

		// dd($request->all());
		// Delete Category
		if ($request->has('action') && $request->action == 'delete'){
			RestFood::where('rest_id', $rest->id)->where('food_category_id', $request->category_id)->delete();
		} 
		else { // Edit Category
			$categoryExists = FoodCategory::where('food_category_name',$request->category_name)->first();

			if (!$categoryExists){
				$category = new FoodCategory();
				$category->food_category_name = $request->category_name;
				$category->save();
			} else {
				if ($categoryExists->food_category_id == $request->category_id){
					$error = \Illuminate\Validation\ValidationException::withMessages([
						'No change' => ['No change is made.']
					]);
					throw $error;
				}
				$category = $categoryExists;
			}

			// dd($category);

			// Update Category on All associated foods under this restaurant
			RestFood::where('rest_id', $rest->id)->where('food_category_id', $request->category_id)->update([
				'food_category_id' => $category->food_category_id
			]);
		}

		$payload = RestFoodDetailsDataset::where('rest_id', $rest->id)->get();
		

		return response([
			'message' => 'success',
			'payload' => $payload
		], 200);
	}

	public function addMenu(Request $request, $is_api_call = false){
		if ($is_api_call){
			$radmin = auth('api_restaurant')->user();
		} else {
			$radmin = auth('radmin')->user();
		}

		$rest = $radmin->restaurant;
		$this->validate($request, [
			"food_category_id" => "required|integer|exists:food_category,food_category_id",
			"food_name" => "required",
			"food_name.*" => "required|max:100",
			// "food_image_url" => "nullable|image",
			"unit_price" => "required",
			"unit_price.*" => "required|numeric"
		]);

		// dd($request->all());

		$data = array();
		$names = [];

		$all_food = Food::all();
		
		//add all food name into array
        for($i=0; $i<sizeof($request->food_name); $i++){
            if($request->food_name[$i] != null){
				// Make sure the food doesn't exist on 'all_food' table
				if (!$all_food->contains("food_name", $request->food_name[$i])){
					array_push($data, array(
						"food_name"=>$request->food_name[$i]
					));
				}

				array_push($names, $request->food_name[$i]);
            }
		}

		//insert all food name data into database
		Food::insert($data);

		//find all ids of newly added data from database
		$ids = Food::whereIn('food_name', $names)->groupBy('food_name')->get();

		$food_data = [];
		//add all food description into array

		for ($i=0; $i<sizeof($ids); $i++) {
			// dd($request->food_image_url[$i]);
			if ($request->has('food_image_url') && $request->food_image_url[$i] && $request->food_image_url[$i] !== 'null'){
				//save food image in srtorage
				$picture = $request->food_image_url[$i];
				// dd($picture->getMimeType());

				if(substr($picture->getMimeType(), 0, 5) != 'image') {
					// this is not an image
					$error = \Illuminate\Validation\ValidationException::withMessages([
						'Not Image' => ['Uploaded file for food ' + $ids[$i]->food_name + ' is not an image file']
					]);
					throw $error;
				}

				//generate random name
				$picture_name = str_random(10) . '_' . Carbon::now()->getTimestamp() . '.' . $picture->getClientOriginalExtension();

				//store in storage
				$picture->storeAs('public/rest_food', $picture_name);
			} else {
				$picture_name = null;
			}


			array_push($food_data, array(
				"rest_id" => $rest->id,
				"food_id" => $ids[$i]->food_id,
				"unit_price" => $request->unit_price[$i],
				"food_image_url" => $picture_name,
				"food_category_id" => $request->food_category_id,
				"description" => $request->has('description') ? $request->description[$i] : null,
				"food_availability" => true
			));
		}

		//insert all food description data into database
		try {
			RestFood::insert($food_data);
		} catch(QueryException $e){
			// dd($e);
			$error = \Illuminate\Validation\ValidationException::withMessages([
				// 'Duplicate Entry' => ['The food ' . Food::find(27)->food_name . ' already exists in your menu under this category.']
				'message' => $e->getMessage()
			]);
			throw $error;
		}


		$payload = RestFoodDetailsDataset::where('rest_id', $rest->id)->get();

		return response([
			'message' => 'success',
			'payload' => $payload
		], 200);
	}

	public function toggleMenu(Request $r, $is_api_call = false){
		if ($is_api_call){
			$radmin = auth('api_restaurant')->user();
		} else {
			$radmin = auth('radmin')->user();
		}

		$this->validate($r, [
			'food_id' => 'required|exists:all_food,food_id'
		]);

		$record = $radmin->restaurant->food()->where('food_id', $r->food_id)->firstOrFail();

		if (!$record){
			return response([
				'message' => '404',
				'payload' => 'The given information is not available in database'
			], 500);
		}

		$record->food_availability = !$record->food_availability;
		$record->save();

		return response([
			'message' => 'success',
			'payload' => 'The given food is edited'
		], 200);
	}

	public function deleteMenu(Request $r, $is_api_call = false){
		if ($is_api_call){
			$radmin = auth('api_restaurant')->user();
		} else {
			$radmin = auth('radmin')->user();
		}

		$this->validate($r, [
			'food_id' => 'required|exists:all_food,food_id'
		]);

		$record = $radmin->restaurant->food()->where('food_id', $r->food_id)->firstOrFail();

		if (!$record){
			return response([
				'message' => '404',
				'payload' => 'The given information is not available in database'
			], 500);
		}

		$record->delete();

		return response([
			'message' => 'success',
			'payload' => 'The given food is deleted'
		], 200);
	}

	public function editMenu(Request $r, $is_api_call = false){
		if ($is_api_call){
			$radmin = auth('api_restaurant')->user();
		} else {
			$radmin = auth('radmin')->user();
		}
		// dd($r->all());


		$this->validate($r, [
			'food_id' => 'required|exists:all_food,food_id',
			'food_name' => 'required',
			'unit_price' => 'required|numeric'
		]);

		$record = $radmin->restaurant->food()->where('food_id', $r->food_id)->firstOrFail();
		$food = Food::find($r->food_id);

		$name = $r->food_name;
		$price = $r->unit_price;

		// Edit the food record, not the original name
		if ($food->food_name != $name){
			// If a food with the given name exists, use it
			$newFood = Food::where('food_name', 'LIKE', '%'.$name.'%')->first();

			// Otherwise, create new
			if (!$newFood){
				$newFood = new Food([
					'food_name' => $name
				]);
				$newFood->save();
			}

			$record->food_id = $newFood->food_id;

		}

		$record->unit_price = $price;
		$record->description = $r->description;


		if ($r->food_image_url != 'undefined' && $r->file('food_image_url')){
			$image = $r->file('food_image_url');
			$filename = str_random(10) . '_' . Carbon::now()->getTimestamp() . '.' . $image->getClientOriginalExtension();
			$filename = preg_replace('/\s+/', '_', $filename);

			// dd($filename);

			// Store the original image
			Storage::disk('local')->put("public/rest_food/".$filename, File::get($image));
			
			$record->food_image_url = $filename;
		}

		$record->save();

		$resp = RestFoodDetailsDataset::where('rest_id', $radmin->restaurant->id)->where('food_id', $record->food_id)->first();

		return response([
			'message' => 'success',
			'payload' => $resp
		], 200);
	}

	public function food_category_list(){
		// $category = FoodCategory::with("food_list")->get();
		// dd($category[0]->food_list[0]->food_id);category = FoodCategory::with("food_list")->get();
		// dd($category[0]->food_list[0]->food_id);

		// $category = FoodCategory::whereIn("food_category_id", "select food_category_id from all_food where all_food.food_category_id = food_category.food_category_id")->get();

		$category = FoodCategory::with("food_list")->whereExists(function ($q){
			// $q->RestFood::where("rest_id", 1)->where('food_id', 'food_category.food_id')->get();
			$q->select("all_food.food_category_id")
				->from('all_food')
				->where('all_food.food_category_id = food_category.food_category_id');
		})->get();

		dd($category);
	}

	public function addOffer(Request $request, $is_api_call = false){
        $this->validate($request, [
            "offer_type_id" => "required|integer",
            "offer_title" => "required",
            "offer_starting_date" => "required|date",
            "offer_ending_date" => "required|date",
            "offer_image" => "sometimes|nullable|mimes: jpg,jpeg,png",
			"offer_price" => "nullable|numeric",
        ]);

		if ($request->has('specific_food') && $request->specific_food == "yes" && $request->food_id != "-1"){
			$this->validate($request,[
				"food_id" => "exists:all_food,food_id",
			]);
		}

		if ($is_api_call){
			$userID = auth('api_restaurant')->user()->id;
		} else {
			$userID = auth('radmin')->user()->id;
		}

        // $userID = Auth::guard('radmin')->user()->id;
        $rest = RestaurantInfo::where("radmin_id", $userID)->first();
		
        $image = null;

        if($request->offer_image){
            $picture = $request->offer_image;

            $image = str_random(20) . date('His') . "." . $picture->extension(); //random string+current time
            $picture->storeAs('public/offer', $image);
        }

        $offer = Offer::create([
            "offer_type_id" => $request->offer_type_id,
            "offer_title" => $request->offer_title,
            "offer_desc" => $request->offer_desc,
            "offer_tc" => $request->offer_tc,
            "offer_starting_date" => $request->offer_starting_date,
            "offer_ending_date" => $request->offer_ending_date,
            "offer_image" => $image,
            "rest_id" => $rest->id,
			"food_id" => $request->has('specific_food') ? $request->food_id : null,
            "price" => $request->has('specific_food') ? $request->offer_price : null,
        ]);

		if ($is_api_call){
			return $offer;
		}

        return redirect("/offer");
        // return redirect()->back();
    }

    public function editOffer(Request $request, $is_api_call = false)
    {
        $this->validate($request, [
            "offer_type_id" => "sometimes|integer",
            "offer_title" => "sometimes",
            "offer_starting_date" => "sometimes|date",
            "offer_ending_date" => "sometimes|date",
            "offer_image" => "sometimes|nullable|mimes: jpg,jpeg,png",
			"offer_price" => "sometimes|nullable|numeric",
        ]);

		if ($request->has('specific_food') && $request->specific_food == "yes" && $request->food_id != "-1"){
			$this->validate($request,[
				"food_id" => "exists:all_food,food_id",
			]);
		}

		if ($is_api_call){
			$userID = auth('api_restaurant')->user()->id;
		} else {
			$userID = auth('radmin')->user()->id;
		}
        $rest = RestaurantInfo::where("radmin_id", $userID)->first();
        $image = Offer::where("offer_id", $request->offer_id)->first()->offer_image;

        if ($request->offer_image) {
            $picture = $request->offer_image;

            $image = str_random(20) . date('His') . "." . $picture->extension(); //random string+current time
            $picture->storeAs('public/offer', $image);
		}
				
		$offer = Offer::where("offer_id", $request->offer_id)->where("rest_id", $rest->id)->first();
		if ($offer->status !== "Ongoing"){
			if ($is_api_call){
				throw ValidationException::withMessages([
					'offer' => 'Only Ongoing offers can be edited.'
				]);
				
				return 'Only Ongoing offers can be edited.';
			}

			return redirect()->back()->with('message', 'Only Ongoing offers can be edited.');
		}

        $offer->update([
            "offer_type_id" => $request->has('offer_type_id') ? $request->offer_type_id : $offer->offer_type_id,
            "offer_title" => $request->has('offer_title') ? $request->offer_title : $offer->offer_title,
            "offer_desc" => $request->has('offer_desc') ? $request->offer_desc : $offer->offer_desc,
            "offer_tc" => $request->has('offer_tc') ? $request->offer_tc : $offer->offer_tc,
            "offer_starting_date" => $request->has('offer_starting_date') ? $request->offer_starting_date : $offer->offer_starting_date,
            "offer_ending_date" => $request->has('offer_ending_date') ? $request->offer_ending_date : $offer->offer_ending_date,
            "offer_image" => $image,
			"food_id" => $request->has('specific_food') && $request->has('food_id') ? $request->food_id : $offer->food_id,
            "price" => $request->has('specific_food') && $request->has('offer_price') ? $request->offer_price : $offer->price,
        ]);

		if ($is_api_call){
			return $offer;
		}

        return redirect("/offer");
        // return redirect()->back();
    }
    
	
	public function relaunchOffer(Request $request, $is_api_call = false)
    {
        $this->validate($request, [
            "offer_type_id" => "sometimes|integer",
            "offer_title" => "sometimes",
            "offer_starting_date" => "required|date",
            "offer_ending_date" => "required|date",
            "offer_image" => "sometimes|nullable|mimes: jpg,jpeg,png",
			"offer_price" => "sometimes|nullable|numeric",
        ]);

		if ($request->has('specific_food') && $request->specific_food == "yes" && $request->food_id != "-1"){
			$this->validate($request,[
				"food_id" => "exists:all_food,food_id",
			]);
		}

		if ($is_api_call){
			$userID = auth('api_restaurant')->user()->id;
		} else {
			$userID = auth('radmin')->user()->id;
		}
        $rest = RestaurantInfo::where("radmin_id", $userID)->first();
        $image = Offer::where("offer_id", $request->offer_id)->first()->offer_image;

        if ($request->offer_image) {
            $picture = $request->offer_image;

            $image = str_random(20) . date('His') . "." . $picture->extension(); //random string+current time
            $picture->storeAs('public/offer', $image);
		}
				
		$offer = Offer::where("offer_id", $request->offer_id)->where("rest_id", $rest->id)->first();


        $new_offer = Offer::create([
            "offer_type_id" => $request->has('offer_type_id') ? $request->offer_type_id : $offer->offer_type_id,
            "offer_title" => $request->has('offer_title') ? $request->offer_title : $offer->offer_title,
            "offer_desc" => $request->has('offer_desc') ? $request->offer_desc : $offer->offer_desc,
            "offer_tc" => $request->has('offer_tc') ? $request->offer_tc : $offer->offer_tc,
            "offer_starting_date" => $request->has('offer_starting_date') ? $request->offer_starting_date : $offer->offer_starting_date,
            "offer_ending_date" => $request->has('offer_ending_date') ? $request->offer_ending_date : $offer->offer_ending_date,
            "offer_image" => $image,
			"rest_id" => $rest->id,
			"food_id" => $request->has('specific_food') && $request->has('food_id') ? $request->food_id : $offer->food_id,
            "price" => $request->has('specific_food') && $request->has('offer_price') ? $request->offer_price : $offer->price,
        ]);

		if ($is_api_call){
			return $new_offer;
		}

        return redirect("/offer");
        // return redirect()->back();
    }

    public function deleteOffer($id){
        $userID = Auth::guard('radmin')->user()->id;
        $rest = RestaurantInfo::where("radmin_id", $userID)->first();

        Offer::where("offer_id", $id)->where("rest_id", $rest->id)->delete();

        return redirect()->back();

	}
	
	public function createPromotion(Request $r){
		$this->validate($r, [
			'price' => 'required|exists:promotion_package_prices,id',
			'method' => 'required'
		]);

		$price = DB::table('promotion_package_prices')->find($r->price);
		$radmin = auth('radmin')->user();
		$rest = $radmin->restaurant;

		$promotion = new Promotion([
			'rest_id' => $rest->id,
			'package_id' => $price->id,
			'amount' => $price->price,
			'method' => $r->method == "bkash" ? "bkash" : "cash",
			'is_active' => false
		]);

		$promotion->save();

		return redirect()->back()->with('success', "Promotion request has been received. We'll contact you for further processing.");
	}

	public function acceptReservation(Request $r){
		$this->validate($r,[
			'id' => 'required|exists:reservations,id'
		]);

		$radmin = auth('radmin')->user();
		$rest = $radmin->restaurant;
		$reserve = Reservation::findOrFail($r->id);

		if ($rest->id != $reserve->rest_id){
			return response()->json([
				'success' => false,
				'message' => 'Not authorized'
			], 422);
		}

		$reserve->is_accepted = true;

		$rname = $rest->rest_name;
		$dt = Carbon::parse($reserve->reservation_time)->toDayDateTimeString();

		$notification = new UserNotification([
			'user_id' => $reserve->user_id,
			'notification_type_id' => 1,
			'text' => 'Your reservation at "'.$rname.'" on '.$dt.' has been accepted. Have a good time.' ,
		]);

		$notification->save();

		if ($reserve->save()){
			return response()->json([
				'success' => true
			], 200);
		}

		return response()->json([
			'success' => false
		], 500);
	}


}
