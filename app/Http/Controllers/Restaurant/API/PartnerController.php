<?php

namespace App\Http\Controllers\Restaurant\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RestaurantController;
use App\Models\Message;
use App\Models\RestAdmin;
use App\Models\RestaurantInfo;
use App\Models\RestProperty;
use App\Models\RestSchedule;
use App\Models\UserNotification;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PartnerController extends Controller
{
    public function partnerInfo(){
        $radmin = auth('api_restaurant')->user();
        $rest = $radmin->restaurant;

        $properties = RestProperty::where('rest_id', $rest->id)->first();
        $schedules = RestSchedule::where('rest_id', $rest->id)->orderBy('day_id','asc')->get();
        $weekdays = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
		$paymethod = $rest->paymethod;
        $bank_accounts = $rest->bank_account()->get();

        $unseen_notifications = UserNotification::where('rest_id', $rest->id)->where('is_seen',false)->count();
        $unseen_messages = Message::toUser($rest->id, "REST")->onlyUnseen()->groupBy('conversation_id')->get()->count();

        return response([
            'success' => true,
            'data' => [
                'rest_admin' => $radmin,
                'rest_properties' => $properties,
                'rest_schedules' => $schedules,
                'rest_paymethod' => $paymethod,
                'bank_accounts' => $bank_accounts,
                "unseen_notifications_count" => $unseen_notifications,
                "unseen_messages_count" => $unseen_messages,
            ],
        ]);
    }


    public function saveRestaurantInfo(Request $request){

        // Patch the request for arrays
        Log::info("Update rest info request (Before Patch): ", $request->all());
        $request->merge(extractStringToArray($request));
        Log::info("Update rest info request (After Patch): ", $request->all());

        $user = auth('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();

        // dd($request->all());

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

        $all_methods = ["cash", "visa", "mastercard", "bkash", "rocket", "nexaspay", "upay"];


        $updatables = $request->except(['payment_methods', 'lat', 'lng', 'rest_image_url', 'rest_plan', 'business_category']);

        $removables = collect($all_methods)->map(function($method){
            return "payment_methods[".$method."]";
        })->toArray();

        // Remove payment methods from restaurant info
        Arr::forget($updatables, $removables);
		
		$rest->update($updatables);
		$rest->rest_latitude = $request->lat;
		$rest->rest_longitude = $request->lng;
		$rest->business_category = $business_category;
		$payment = $rest->getPaymentMethods();
		// dd($payment);
		

		foreach($all_methods as $method){
            // dd($method, $request->payment_methods);

			if ( is_array($request->payment_methods) && isset($request->payment_methods[$method]) ){
				$payment->{$method} = true;
			} else {
				$payment->{$method} = false;
			}
		}


        
		$rest->save();
		$rest->paymethod()->save($payment);
			   

        return response([
            'success' => true,
            'message' => "Information has been updated successfully",
            'data' => [
                'rest_admin' => $user,
                'rest_info' => $rest,
                // 'rest_properties' => $rest->properties,
                // 'rest_schedules' => $rest->schedules,
                // 'rest_paymethod' => $payment,
            ],
        ]);
    }


    public function saveContactInfo(Request $request){
        /** @var RestAdmin $user */
        $user = auth('api_restaurant')->user();

        // dd($request->all());

        if($request->has('contact_no') && null !== $request->input('contact_no')){
            // check if the number already exists on other accounts
            $other_account = RestAdmin::where('contact_no', $request->input('contact_no'))->first();

            if (null !== $other_account && $other_account->id !== $user->id){
                throw ValidationException::withMessages([
                    'contact_no' => 'This contact number already exists on other account.'
                ]);
            }

            // Otherwise, modify the contact number
            $user->contact_no = $request->input('contact_no');
		}
        
        if($request->has('name') && null !== $request->input('name')){
            $user->name = $request->input('name');
		}

        $user->save();			   

        return response([
            'success' => true,
            'message' => "Information has been updated successfully",
            'data' => [
                'rest_admin' => $user,
            ],
        ]);
    }


    public function updateRestaurantProperty(Request $request)
    {
        // dd($request->all());
        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();

        $restaurant_controller = new RestaurantController;
        $restaurant_controller->saveRestaurantProperty($request, $rest->id);
        $rest->properties;

        return response([
            'success' => true,
            'message' => "Information has been updated successfully",
            'data' => [
                'rest_admin' => $user,
                'rest_info' => $rest,
                // 'rest_properties' => $rest->properties,
                // 'rest_schedules' => $rest->schedules,
                // 'rest_paymethod' => $payment,
            ],
        ]);
    }

    public function updateRestaurantSchedule(Request $request)
    {
        // dd($request->all());

        // $request->merge([
        //     //Sunday: on
        //     "opening_time[0]"=>"10:00",
        //     "closing_time[0]"=>"16:00",
        //     //Monday: on,
        //     "opening_time[1]"=> "02:00",
        //     "closing_time[1]"=> "04:30",
        //     //Tuesday: off,
        //     "opening_time[2]"=> '',
        //     "closing_time[2]"=> '',
        //     //Wednesday: off,
        //     "opening_time[3]"=> '',
        //     "closing_time[3]"=> '',
        //     //Thursday: on,
        //     "opening_time[4]"=>"10:00",
        //     "closing_time[4]"=>"16:00",
        //     //Friday: off,
        //     "opening_time[5]"=> '',
        //     "closing_time[5]"=> '',
        //     //Saturday: off,
        //     "opening_time[6]"=> '',
        //     "closing_time[6]"=> '',
        // ]);

        // Patch the request for arrays
        Log::info("Update rest schedule request (Before Patch): ", $request->all());
        $request->merge(extractStringToArray($request));
        Log::info("Update rest schedule request (After Patch): ", $request->all());

        // return $request->all();


        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();

        $restaurant_controller = new RestaurantController;
        $restaurant_controller->saveRestaurantSchedule($request, $rest->id);

        
        return response([
            'success' => true,
            'message' => "Information has been updated successfully",
            'data' => [
                'rest_admin' => $user,
                'rest_info' => $rest,
                // 'rest_properties' => $rest->properties,
                'rest_schedules' => $rest->schedule,
                // 'rest_paymethod' => $payment,
            ],
        ]);
    }

    public function updateContactInfo(Request $request){
        $user = Auth::guard('api_restaurant')->user();
        // $rest = RestaurantInfo::where("radmin_id", $user->id)->first();

        $user->update($request->only(['name']));

        // $user->name = "Anything";

        return response([
            'success' => true,
            'message' => "Information has been updated successfully",
            'data' => [
                'rest_admin' => $user,
                // 'rest_info' => $rest,
                // 'rest_properties' => $rest->properties,
                // 'rest_schedules' => $rest->schedule,
                // 'rest_paymethod' => $payment,
            ],
        ]);
    }
}
