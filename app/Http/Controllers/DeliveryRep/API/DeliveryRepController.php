<?php

namespace App\Http\Controllers\DeliveryRep\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserDrStatus;

class DeliveryRepController extends Controller
{
    public function getStatus(){
        $user = auth('api')->user();
        $status = UserDrStatus::getOrCreate($user);

        return response([
            'success' => true,
            'data' => [
                'status' => $status
            ],
        ]);
    }
    
    public function postUpdateStatus(Request $request){
        $this->validate($request, [
            'latitude' => ['sometimes','regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'longitude' => ['sometimes','regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'accepting_orders' => ['sometimes']
        ]);
        
        $user = auth('api')->user();
        $status = UserDrStatus::getOrCreate($user);

        if ($request->has('latitude') && $request->has('longitude')){
            $status->latitude = $request->input('latitude');
            $status->longitude = $request->input('longitude');
        }
        if ($request->has('accepting_orders')){
            $status->accepting_orders = ($request->input('accepting_orders') == 'true' || $request->input('accepting_orders') == '1') ? true : false;
        }
        $status->save();

        return response([
            'success' => true,
            'message' => 'Current location and/or accepting order status has been updated',
            'data' => [
                'status' => $status
            ],
        ]);
    }
}
