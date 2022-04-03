<?php

namespace App\Http\Controllers\Restaurant\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\RestaurantInfo;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function setIsReceivingOrders(Request $request){
        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();

        if ($request->has('is_receiving_orders') && $request->is_receiving_orders == 'true'){
            $rest->is_receiving_orders = true;
            $status = 'You are receiving orders now.';
            $rest->save();
        } else if($request->has('is_receiving_orders') && $request->is_receiving_orders == 'false') {
            $rest->is_receiving_orders = false;
            $status = 'You are not receiving orders anymore';
            $rest->save();
        } else {
            $status = 'unchanged';
        }

        return response([
            'success' => true,
            'message' => $status
        ]);
    }
}
