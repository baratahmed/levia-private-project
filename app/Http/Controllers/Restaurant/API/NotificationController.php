<?php

namespace App\Http\Controllers\Restaurant\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\RestaurantInfo;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function get(){
        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();

        $notifs = UserNotification::where('rest_id', $rest->id)
            ->select(['id','user_id','payload','updated_at','is_seen','for_type','for_id', 'rest_id'])
            ->orderBy('id', 'desc')->paginate(20);

        // Add data to Payload
        foreach($notifs as $notif){
            $payloadArray = json_decode($notif->payload, true);
            $payloadArray['is_seen'] = $notif->is_seen;
            $payloadArray['for_type'] = $notif->for_type;
            $payloadArray['for_id'] = $notif->for_id;
            $notif->payload = $payloadArray;
            unset($notif->is_seen);
            unset($notif->for_type);
            unset($notif->for_id);
            $notif->updated_at_string = $notif->updated_at->diffForHumans();
        }

        $response = response()->json([
            'success' => true,
            'notifications' => $notifs
        ], 200);

        DB::table('user_notifications')->whereIn('id', $notifs->pluck('id')->toArray())->update(['is_seen' => true]);

        return $response;
    }
}
