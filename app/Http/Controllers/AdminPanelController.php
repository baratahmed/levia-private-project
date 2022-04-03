<?php

namespace App\Http\Controllers;

use App\Jobs\Notifications\ReservationAccepted;
use Carbon\Carbon;
use App\Models\Food;
use App\Models\RestFood;
use App\Models\RestAdmin;
use App\Models\FoodCategory;
use App\Models\Promotion;
use Illuminate\Http\Request;
use App\Models\RestSchedule;
use App\Models\RestProperty;
use App\Models\RestaurantInfo;
use App\Models\Reservation;
use App\Models\UserNotification;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use App\Models\RestFoodDetailsDataset;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use App\Models\User;
use App\Models\Offer;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AdminPanelController extends Controller
{
	public function addRestaurant(Request $request)
	{
        $request->validate([
            "rest_name" => ['required', 'string', 'max:255'],
            // "rest_street" => ['required', 'string', 'max:255'],
            // "rest_road_no" => ['required', 'numeric'],
            // "police_station" => ['required', 'string', 'max:255'],
            // "rest_post_code" => ['required', 'numeric'],
            "district_id" => ['required', 'exists:districts,district_id'],
            "rest_registration_no" => ['required', 'string', 'max:255'],
            "rest_email" => ['required', 'email', 'string', 'max:255', 'unique:rest_admins,email'],
            "rest_contact_no" => ['required'],
            "rest_owner_name" => ['required', 'string', 'max:255'],
            "rest_owner_contact_no" => ['required'],
            "rest_owner_password" => ['required', 'string', 'min:6'],
            "lat" => ['required'],
            "lng" => ['required'],
        ]);

        $user = RestAdmin::create([
            "email" => $request->rest_email,
            "password" => Hash::make($request->rest_owner_password),
        ]);

        $user->name = $request->rest_owner_name;
        $user->contact_no = $request->rest_owner_contact_no;
        $user->save();

        if ($request->rest_image_url) {
            $this->validate($request, [
                "rest_image_url" => "required|mimes:jpg,jpeg,png",
            ]);

            $icon = $request->rest_image_url;

            $iconName = str_random(20) . date('His') . "." . $request->file('rest_image_url')->getClientOriginalExtension(); //random string+current time

            $icon->storeAs('public/logo', $iconName);

            $request->rest_image_url = $iconName;
        }
        else {
            $request->rest_image_url = "default.jpg";
        }

        $restaurant = RestaurantInfo::create([
            "rest_name" => $request->rest_name,
            "rest_image_url" => $request->rest_image_url,
            "rest_latitude" => $request->lat,
            "rest_longitude" => $request->lng,
            "rest_street" => $request->rest_street,
            "district_id" => $request->district_id,
            "rest_post_code" => $request->rest_post_code,
            "road_no" => $request->rest_road_no,
            "police_station" => $request->police_station,
            "phone" => $request->rest_contact_no,
            "registration_number" => $request->rest_registration_no,
			"radmin_id" => $user->id,
			"is_published" => false
        ]);

        return redirect()->route("admin.edit_business", ['id' => $restaurant->id]);
    }

	public function updateRestaurantDetails(Request $request, $id)
	{
		// dd($request->all());
        $request->validate([
            "rest_name" => ['required', 'string', 'max:255'],
            // "rest_street" => ['required', 'string', 'max:255'],
            // "rest_road_no" => ['required', 'numeric'],
            // "police_station" => ['required', 'string', 'max:255'],
            // "rest_post_code" => ['required', 'numeric'],
            "district_id" => ['required', 'exists:districts,district_id'],
            "rest_registration_no" => ['required', 'string', 'max:255'],
            "rest_email" => ['required', 'email', 'string', 'max:255'],
            "rest_contact_no" => ['required'],
            "rest_owner_name" => ['required', 'string', 'max:255'],
            "rest_owner_contact_no" => ['required'],
            "lat" => ['required'],
            "lng" => ['required'],
        ]);

        $restaurant = RestaurantInfo::find($id);
        $user = RestAdmin::find($restaurant->radmin_id);

        $other = RestAdmin::where('email', $request->rest_email)->first();

        if ($other && $other->id != $user->id) {
            return Redirect::back()->withErrors("message", "Email already exists");
        }

        $user->name = $request->rest_owner_name;
        $user->contact_no = $request->rest_owner_contact_no;
        $user->email = $request->rest_email;

        $user->save();

        if ($request->rest_image_url) {
            $this->validate($request, [
                "rest_image_url" => "required|mimes:jpg,jpeg,png",
            ]);

            $icon = $request->rest_image_url;

            $iconName = str_random(20) . date('His') . "." . $request->file('rest_image_url')->getClientOriginalExtension(); //random string+current time

            Storage::delete('public/rest_logo'.$restaurant->rest_image_url);
            $icon->storeAs('public/rest_logo', $iconName);

            $request->rest_image_url = $iconName;
        }
        else {
            $request->rest_image_url = $restaurant->rest_image_url;
				}

				if (in_array($request->business_category, ['Restaurant', 'Catering House', 'Home Kitchen'])){
					$business_category = $request->business_category;
				} else {
					$business_category = 'Restaurant';
				}

        $restaurant->rest_name = $request->rest_name;
        $restaurant->rest_image_url = $request->rest_image_url;
        $restaurant->rest_latitude = $request->lat;
        $restaurant->rest_longitude = $request->lng;
        $restaurant->rest_street = $request->rest_street;
        $restaurant->district_id = $request->district_id;
        $restaurant->rest_post_code = $request->rest_post_code;
        $restaurant->road_no = $request->rest_road_no;
        $restaurant->police_station = $request->police_station;
        $restaurant->phone = $request->rest_contact_no;
		$restaurant->registration_number = $request->rest_registration_no;
		$restaurant->type = $request->type;
		$restaurant->business_category = $business_category;
		$restaurant->cuisines = $request->cuisines;
		$restaurant->total_seats = $request->total_seats;
		$restaurant->cost = $request->cost;

		$payment = $restaurant->getPaymentMethods();
		// dd($payment);
		$all_methods = ["cash", "visa", "mastercard", "bkash", "rocket", "nexaspay", "upay"];

		if ($request->has('payment_methods')) {
			foreach($all_methods as $method){
				if (array_search($method, $request->payment_methods) !== false){
					$payment->{$method} = true;
				} else {
					$payment->{$method} = false;
				}
			}
		}

		if ($request->has('rest_plan')) {
			if ("Hype" == $request->rest_plan){
				$restaurant->plan = "Hype";
			}
			else if ("Splash" == $request->rest_plan){
				$restaurant->plan = "Splash";
			}
			else if ("Finix" == $request->rest_plan){
				$restaurant->plan = "Finix";
			}
		}


		$restaurant->save();
		$restaurant->paymethod()->save($payment);

        return redirect()->route("admin.edit_business", ['id' => $id]);
    }

    public function updateRestaurantSchedule(Request $request, $id)
    {
        $data = array();
        $data = $this->setScheduleDataArray($request, $id);

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

        return redirect()->route("admin.edit_business", ['id' => $id]);
    }

	private function setScheduleDataArray($request, $rest_id)
	{
    	$day = array();
    	$weekend = array();
    	$id = $rest_id;
    	$open = $request->opening_time;
    	$close = $request->closing_time;

    	if($request->Sunday){
    		array_push($day, array("rest_id" => $id,"day_id"=>"1","day"=>"Sunday","opening_time"=>$open[0],"closing_time"=>$close[0]));
    	}else{
    		array_push($weekend,"Sunday");
    	}

    	if($request->Monday){
    		array_push($day, array("rest_id" => $id,"day_id"=>"2","day"=>"Monday","opening_time"=>$open[1],"closing_time"=>$close[1]));
    	}else{
    		array_push($weekend,"Monday");
    	}

    	if($request->Tuesday){
    		array_push($day, array("rest_id" => $id,"day_id"=>"3","day"=>"Tuesday","opening_time"=>$open[2],"closing_time"=>$close[2]));
    	}else{
    		array_push($weekend,"Tuesday");
    	}

    	if($request->Wednesday){
    		array_push($day, array("rest_id" => $id,"day_id"=>"4","day"=>"Wednesday","opening_time"=>$open[3],"closing_time"=>$close[3]));
    	}else{
    		array_push($weekend,"Wednesday");
    	}

    	if($request->Thursday){
    		array_push($day, array("rest_id" => $id,"day_id"=>"5","day"=>"Thursday","opening_time"=>$open[4],"closing_time"=>$close[4]));
    	}else{
    		array_push($weekend,"Thursday");
    	}

    	if($request->Friday){
    		array_push($day, array("rest_id" => $id,"day_id"=>"6","day"=>"Friday","opening_time"=>$open[5],"closing_time"=>$close[5]));
    	}else{
    		array_push($weekend,"Friday");
    	}

    	if($request->Saturday){
    		array_push($day, array("rest_id" => $id,"day_id"=>"7","day"=>"Saturday","opening_time"=>$open[6],"closing_time"=>$close[6]));
    	}else{
    		array_push($weekend,"Saturday");
    	}

    	return array($day, $weekend);
	}

	public function restaurantPublisher(RestaurantInfo $rest, Request $r){
		$this->validate($r, [
			'action' => 'required'
		]);

		if ($rest->is_published && $r->action == "Unpublish"){
			$rest->is_published = false;
			$rest->save();
			return redirect()->back()->with('success', 'Restaurant has been unpublished');
		}
		else if (!$rest->is_published && $r->action == "Publish"){
			$rest->is_published = true;
			$rest->save();
			return redirect()->back()->with('success', 'Restaurant has been published');
		}

		return redirect()->back()->with('error', 'Restaurant is already '. $r->action . 'ed');
	}

	public function deleteRestaurant(RestaurantInfo $rest, Request $r){
		$this->validate($r, [
			'action' => 'required'
		]);

		if ($r->action == "Delete"){
			DB::table('rest_rating')->where('rest_id', $rest->id)->delete();
			DB::table('rest_review')->where('rest_id', $rest->id)->delete();
			// Remove Food Rating/Reviews
			DB::table('food_rating')->where('rest_id', $rest->id)->delete();
			DB::table('food_review')->where('rest_id', $rest->id)->delete();
			// Remove News Feed Items
			$ids = DB::table('news_feeds')->where('rest_id', $rest->id)->select('id')->get();
			DB::table('news_feeds')->where('rest_id', $rest->id)->delete();
			// Remove Posts
			DB::table('posts')->whereIn('post_meta_id', $ids->toArray())->where('post_meta_type', 'news_feeds')->delete();
			// Remove Offers
			DB::table('offer_info')->where('rest_id', $rest->id)->delete();
			// Remove Promotions
			DB::table('promotions')->where('rest_id', $rest->id)->delete();
			// Remove Reservations
			DB::table('reservations')->where('rest_id', $rest->id)->delete();
			// Remove Rest Facility
			DB::table('rest_facility')->where('rest_id', $rest->id)->delete();
			// Remove Rest Food
			DB::table('rest_food')->where('rest_id', $rest->id)->delete();
			// Remove Rest Schedule
			DB::table('rest_schedule')->where('rest_id', $rest->id)->delete();
			// Remove Wish list
			DB::table('wishlist')->where('rest_id', $rest->id)->delete();
			$rest->delete();
			return redirect()->back()->with('success', 'Restaurant has been deleted');
		}

		return redirect()->back()->with('error', 'Can\'t delete restaurant.');
	}

    public function updateRestaurantProperty(Request $request, $id)
    {
        $data = array();
        $data = $this->setPropertyDataArray($request, $id);

        RestProperty::where('rest_id', $id)->delete(); //remove the previous record
        RestProperty::create($data); //create new one

        return redirect()->route("admin.edit_business", ['id' => $id]);
    }

	private function setPropertyDataArray($request, $rest_id)
	{
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

	public function addCategory(Request $request, $id)
	{
        $this->validate($request, [
			"categoryId" => "integer|nullable",
			"categoryName" => 'required|max:50',
			"food_name.*" => 'required|max:100',
			"unit_price.*" => "required|integer",
			// "food_image_url.*" => "required|mimes:jpeg,jpg,png"
        ]);

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
			if ($request->food_image_url[$i] !== 'null'){
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
				"rest_id" => $id,
				"food_id" => $food_id->food_id,
				"unit_price" => $request->unit_price[$i],
				"food_image_url" => $picture_name,
				"food_category_id" => $categoryId,
				"description" => $request->description[$i],
				"food_availability" => true
			));
		}

		//insert all food description data into database
		try {
			RestFood::insert($food_data);
		} catch(QueryException $e){
			$error = \Illuminate\Validation\ValidationException::withMessages([
				// 'Duplicate Entry' => ['The food ' . Food::find(27)->food_name . ' already exists in your menu under this category.']
				'Problem' => $e->getMessage()
			]);
			throw $error;
		}

		$payload = RestFoodDetailsDataset::where('rest_id', $id)->get();

		return response([
			'message' => 'success',
			'payload' => $payload
		], 200);
	}

	public function editcategory(Request $request, $id){
		$this->validate($request, [
			"category_id" => "required|integer|exists:food_category,food_category_id",
			"category_name" => "sometimes|min:2|max:150"
		]);


		// dd($request->all());
		// Delete Category
		if ($request->has('action') && $request->action == 'delete'){
			RestFood::where('rest_id', $id)->where('food_category_id', $request->category_id)->delete();
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
			RestFood::where('rest_id', $id)->where('food_category_id', $request->category_id)->update([
				'food_category_id' => $category->food_category_id
			]);
		}

		$payload = RestFoodDetailsDataset::where('rest_id', $id)->get();


		return response([
			'message' => 'success',
			'payload' => $payload
		], 200);
	}

	public function addMenu(Request $request, $id)
	{
		$this->validate($request, [
			"food_category_id" => "required|integer|exists:food_category,food_category_id",
			"food_name.*" => "required",
			// "food_image_url" => "nullable|image",
			"unit_price.*" => "required|integer"
		]);

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
		$ids = Food::whereIn('food_name', $names)->get();

		$food_data = [];
		//add all food description into array

		for ($i=0; $i<sizeof($ids); $i++) {
			// dd($request->food_image_url[$i]);
			if ($request->food_image_url[$i] !== 'null'){
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
				"rest_id" => $id,
				"food_id" => $ids[$i]->food_id,
				"unit_price" => $request->unit_price[$i],
				"food_image_url" => $picture_name,
				"food_category_id" => $request->food_category_id,
				"description" => $request->description[$i],
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

		$payload = RestFoodDetailsDataset::where('rest_id', $id)->get();

		return response([
			'message' => 'success',
			'payload' => $payload
		], 200);
    }

	public function editMenu(Request $request, $id)
	{
		$this->validate($request, [
			'food_id' => 'required|exists:all_food,food_id',
			'food_name' => 'required',
			'unit_price' => 'required|numeric'
		]);

		$record = RestaurantInfo::find($id)->food()->where('food_id', $request->food_id)->firstOrFail();
		$food = Food::find($request->food_id);

		$name = $request->food_name;
		$price = $request->unit_price;

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
		$record->description = $request->description;


		if ($request->food_image_url != 'undefined' && $request->file('food_image_url')){
			$image = $request->file('food_image_url');
			$filename = str_random(10) . '_' . Carbon::now()->getTimestamp() . '.' . $image->getClientOriginalExtension();
			$filename = preg_replace('/\s+/', '_', $filename);

			// Store the original image
			Storage::disk('local')->put("public/rest_food/".$filename, File::get($image));

			$record->food_image_url = $filename;
		}

		$record->save();

		$resp = RestFoodDetailsDataset::where('rest_id', $id)->where('food_id', $record->food_id)->first();

		return response([
			'message' => 'success',
			'payload' => $resp
		], 200);
    }

	public function deleteMenu(Request $request, $id)
	{

		$this->validate($request, [
			'food_id' => 'required|exists:all_food,food_id'
		]);

		$record = RestaurantInfo::find($id)->food()->where('food_id', $request->food_id)->firstOrFail();

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

	public function toggleMenu(Request $request, $id)
	{

		$this->validate($request, [
			'food_id' => 'required|exists:all_food,food_id'
		]);

		$record = RestaurantInfo::find($id)->food()->where('food_id', $request->food_id)->firstOrFail();

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

	public function getFoodAndCategories($id)
	{
        $rest_food_details = RestFoodDetailsDataset::where('rest_id', $id)->get();

        return $rest_food_details;
	}

	public function deleteUser(Request $r){
		$user = User::findOrFail($r->user_id);

		$user->delete();

		return redirect()->back();
	}

	public function addOffer(Request $request){
        $this->validate($request, [
			"rest_id" => "required|exists:rest_info,id",
            "offer_type_id" => "required|integer",
            "offer_title" => "required",
            "offer_starting_date" => "required|date",
            "offer_ending_date" => "required|date",
            // "offer_image" => "nullable|mimes:jpg,jpeg,png",
            "offer_image" => "nullable|mimes:jpg,jpeg,png",
            "offer_price" => "nullable|numeric",
        ]);

		if ($request->has('specific_food') && $request->specific_food == "yes" && $request->food_id != "-1"){
			$this->validate($request,[
				"food_id" => "exists:all_food,food_id",
			]);
		}

        $rest = RestaurantInfo::findOrFail($request->rest_id);
        $image = null;

        if($request->offer_image){
            $picture = $request->offer_image;

            $image = str_random(20) . date('His') . "." . $picture->extension(); //random string+current time
            $picture->storeAs('public/offer', $image);
        }

        Offer::create([
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

        return redirect()->route('admin.offers');
        // return redirect()->back();
	}

	public function editOffer($id, Request $request){
		$offer = Offer::where('offer_id', $id)->with(['type', 'restaurant'])->first();

        $this->validate($request, [
            "offer_type_id" => "required|integer",
            "offer_title" => "required",
            "offer_starting_date" => "required|date",
            "offer_ending_date" => "required|date",
            "offer_image" => "nullable|mimes:jpg,jpeg,png",
			"offer_price" => "nullable|numeric",
        ]);

		if ($request->has('specific_food') && $request->specific_food == "yes" && $request->food_id != "-1"){
			$this->validate($request,[
				"food_id" => "exists:all_food,food_id",
			]);
		}

        $image = $offer->offer_image;

        if($request->offer_image){
            $picture = $request->offer_image;

            $image = str_random(20) . date('His') . "." . $picture->extension(); //random string+current time
            $picture->storeAs('public/offer', $image);
        }

        $offer->update([
            "offer_type_id" => $request->offer_type_id,
            "offer_title" => $request->offer_title,
            "offer_desc" => $request->offer_desc,
            "offer_tc" => $request->offer_tc,
            "offer_starting_date" => $request->offer_starting_date,
            "offer_ending_date" => $request->offer_ending_date,
            "offer_image" => $image,
            "rest_id" => $request->rest_id,
			"food_id" => $request->has('specific_food') ? $request->food_id : null,
            "price" => $request->has('specific_food') ? $request->offer_price : null,
		]);

		$offer->save();

        return redirect()->route('admin.offers')->with('message','Offer updated.');
        // return redirect()->back();
    }

	public function deleteOffer(Request $r){
	    $this->validate($r, [
			'offer_id' => 'required|exists:offer_info,offer_id'
		]);
		$offer = Offer::where('offer_id',$r->offer_id)->first();

		$offer->delete();

		return redirect()->back()->with('success', 'Offer has been deleted.');
	}

	public function managePromotion(Request $r){
		$this->validate($r, [
			'promo_id' => 'required|exists:promotions,id'
		]);

		$promotion = Promotion::findOrFail($r->promo_id);

		if ($r->action == 'approve'){

			$now = Carbon::now();
			$duration = $promotion->price->duration;

			$ends = Carbon::now()->addDays($duration);
			// dd($ends, $now);

			$promotion->starting_at = $now;
			$promotion->ending_at = $ends;
			$promotion->is_active = true;


			$promotion->save();

			return redirect()->back()->with('success', 'Promotion has been approved.');
		} else {
			$promotion->delete();

			return redirect()->back()->with('success', 'Promotion has been deleted.');
		}
	}

    public function acceptReservation(Request $r){
        $this->validate($r,[
            'id' => 'required|exists:reservations,id'
        ]);

        $reserve = Reservation::findOrFail($r->id);
        $rest = RestaurantInfo::findOrFail($reserve->rest_id);


        $reserve->is_accepted = true;

        $rname = $rest->rest_name;
        $dt = Carbon::parse($reserve->reservation_time)->toDayDateTimeString();

				// Send Reservation Accepted Notification
        ReservationAccepted::dispatch($reserve, $rname, $dt);

        if ($reserve->save()){
            return response()->json([
                'success' => true
            ], 200);
        }

        return response()->json([
            'success' => false
        ], 500);
    }

	public function postSettings(Request $request){

		foreach($request->except('_token') as $key=>$value){
			Setting::setValue($key, $value);
		}

		return redirect()->back()->with('success', 'Settings have been updated.');
	}
}
