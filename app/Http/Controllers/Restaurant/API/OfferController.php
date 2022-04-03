<?php

namespace App\Http\Controllers\Restaurant\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RestaurantController;
use App\Models\Offer;
use App\Models\OfferType;
use App\Models\RestaurantInfo;
use Illuminate\Support\Facades\Auth;

class OfferController extends Controller
{
    public function myOffers(){
        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();

        $offers = Offer::with(['type:offer_type_id,offer_type_name', 'food_details:rest_id,food_id,unit_price,food_name,food_image_url'])->where('rest_id', $rest->id)->orderBy('offer_id', 'desc')->paginate(10);

        return response([
            'success' => true,
            'data' => [
                'offers' => $offers
            ],
        ]);
    }
    
    public function viewOffer(Offer $offer){
        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();

        $offer->load(['type:offer_type_id,offer_type_name', 'food_details:rest_id,food_id,unit_price,food_name,food_image_url']);

        // $offers = Offer::with('type:offer_type_id,offer_type_name')->where('rest_id', $rest->id)->orderBy('offer_id', 'desc')->paginate(10);

        return response([
            'success' => true,
            'data' => [
                'offers' => $offer
            ],
        ]);
    }
    
    public function delete_offer(Offer $offer){
        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();

        // $offers = Offer::with('type:offer_type_id,offer_type_name')->where('rest_id', $rest->id)->orderBy('offer_id', 'desc')->paginate(10);
        $offer->delete();

        return response([
            'success' => true,
            'message' => "Offer has been deleted",
        ]);
    }
    
    public function add_offer(Request $request){
        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();

        // Inject "specific_food" with $request, if request doesn't have it
        if ($request->has('food_id') && $request->food_id !== null && is_int((int)$request->food_id)){
            $request['specific_food'] = "yes";
        }

        return $request->all();

        $restaurant_controller = new RestaurantController;
        $response = $restaurant_controller->addOffer($request, true);

        $response->load(['type:offer_type_id,offer_type_name', 'food_details:rest_id,food_id,unit_price,food_name,food_image_url']);

        return response([
            'success' => true,
            'message' => 'Offer has been created successfully',
            'data' => [
                'offer' => $response
            ]
        ]);
        
    }
    
    public function editOffer(Offer $offer, Request $request){
        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();

        // Inject "specific_food" with $request, if request doesn't have it
        if ($request->has('food_id') && $request->food_id !== null && is_int((int)$request->food_id)){
            $request['specific_food'] = "yes";
        }

        $request['offer'] = $offer;
        $request['offer_id'] = $offer->offer_id;

        // return $request->all();

        $restaurant_controller = new RestaurantController;
        $response = $restaurant_controller->editOffer($request, true);

        if (method_exists($response, 'load')){
            $response->load(['type:offer_type_id,offer_type_name', 'food_details:rest_id,food_id,unit_price,food_name,food_image_url']);
        } else {
            return response([
                'success' => false,
                'message' => 'Something went wrong. We could not update this offer.',
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Offer has been updated successfully',
            'data' => [
                'offer' => $response
            ]
        ]);
        
    }
    
    
    public function relaunchOffer(Offer $offer, Request $request){
        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();
        
        // Inject "specific_food" with $request, if request doesn't have it
        if ($request->has('food_id') && $request->food_id !== null && is_int( (int) $request->food_id)){
            $request['specific_food'] = "yes";
        }
        
        $request['offer'] = $offer;
        $request['offer_id'] = $offer->offer_id;

        
        $restaurant_controller = new RestaurantController;
        $response = $restaurant_controller->relaunchOffer($request, true);
        
        if (method_exists($response, 'load')){
            $response->load(['type:offer_type_id,offer_type_name', 'food_details:rest_id,food_id,unit_price,food_name,food_image_url']);
        } else {
            return response([
                'success' => false,
                'message' => 'Something went wrong. We could not relaunch this offer.',
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Offer has been relanuched successfully',
            'data' => [
                'offer' => $response
            ]
        ]);
        
    }

    public function offer_types(){
        return OfferType::all();
    }
}
