<?php

namespace App\Http\Controllers\Restaurant\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\Notifications\SendFoodReadyNotification;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderVerificationCode;
use App\Models\RestaurantInfo;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserDrStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function getOrders(Request $request){
        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();

        $orders = Order::with(['cart:id,subtotal'])->where('rest_id', $rest->id);

        if ( 
            $request->has('status_for_restaurant')  &&
            in_array( strtolower($request->input('status_for_restaurant')) , [
                strtolower('Preparing'), strtolower('Pending'), strtolower('Ready'), strtolower('Picked'), strtolower('Delivered'), strtolower('Canceled')
            ]) 
        ){
            $orders->whereStatusByRestaurant($request->input('status_for_restaurant'));
        }

        $orders = $orders->orderBy('created_at', 'desc');
        $orders = $orders->paginate(20);
        
        $orders->getCollection()->each->append([
            'status_for_restaurant',
            'remaining_time_in_seconds'
        ]);

        $orders->load('dr:id,fb_profile_name,contact_no,fb_profile_pic_url');

        // Append Cart Foods Summary in Orders
        Order::appendCartSummary($orders);

        return $orders;
    }

    public function getOrderDetails(Request $request, Order $order){
        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();
        $customer = User::findOrFail($order->user_id);

        if ( (int) $rest->id !== (int) $order->rest_id){
            return response([
                'success' => false,
                'data' => 'This order does not belong to you.',
            ]);
        }

        // $order->load('cart:id,total_price,discount,subtotal');
        $order->append([
            'status_for_restaurant',
            'remaining_time_in_seconds'
        ]);

        // Order Info
        $order_info = [
            'restaurant' => $rest->only(explode(',','id,rest_name,rest_image_url,rest_latitude,rest_longitude,rest_street,city_id,district_id,rest_post_code,road_no,police_station,phone,type,business_category,district_name,imageUrl')),
            'customer' => [
                'fb_profile_name' => $customer->fb_profile_name,
                'address' => UserAddress::find($order->address_id)
            ]
            // 'restaurant' => $rest
        ];

        // Products
        $cart = Cart::selectRaw('id,total_price,discount,subtotal')->whereId($order->cart_id)->firstOrFail();
        $items = CartItem::
            selectRaw("cart_items.*, rest_food_details_dataset.food_name, rest_food_details_dataset.food_image_url, rest_food_details_dataset.unit_price")->
            leftJoin('carts', 'carts.id', '=', 'cart_items.cart_id')->
            leftJoin('rest_food_details_dataset', function($join) use($order){
                $join->on('rest_food_details_dataset.rest_id', '=', 'carts.rest_id');
                $join->on('rest_food_details_dataset.food_id', '=', 'cart_items.food_id');
            })->
            where('cart_id', $cart->id)->get();

        $items->each->append('foodImage');

        // Receipt
        $receipt = $cart->only(['total_price', 'discount', 'subtotal']);

        // Foodman
        if ($order->dr_id !== null && $order->foodman_assigned_at !== null){
            $foodman = User::selectRaw('id,fb_profile_name,fb_profile_pic_url,contact_no')->whereId($order->dr_id)->first();
        } else {
            $foodman = null;
        }


        return response([
            'order' => $order,
            'order_info' => $order_info,
            'products' => $items,
            'receipt' => $receipt,
            'foodman' => $foodman,
            'pickup_verification_code' => $this->getOrderPickupCode($order, 'plain-text')
        ]);
    }

    public function getOrderPickupCode(Order $order, $responseType = 'object'){
        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();

        if ( (int) $rest->id !== (int) $order->rest_id){
            return response([
                'success' => false,
                'data' => 'This order does not belong to you.',
            ]);
        }

        if ($order->order_status !== ORDER::ORDER_STATUS_FOODMAN_ASSIGNED){
            if ($responseType === 'plain-text'){
                return 'No delivery representative has accepted this order yet for delivery.';
            }
            else {
                return response([
                    'success' => false,
                    'message' => 'No delivery representative has accepted this order yet for delivery.',
                ], 200);
            }
        }

        $order_verification_code = OrderVerificationCode::where([
            'order_id' => $order->id,
            'action' => "PICK_FOOD"
        ])->selectRaw('action,verification_digits')->first();

        if ( !$order_verification_code ){
            if ($responseType === 'plain-text'){
                return 'The Delivery Representative must mark this order as Picked first.';
            }
            else {
                return response([
                    'success' => false,
                    'message' => 'The Delivery Representative must mark this order as Picked first.',
                ], 200);
            }
        }

        return $order_verification_code;
    }

    public function getTrackDR(Order $order){
        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();

        if ( (int) $rest->id !== (int) $order->rest_id){
            return response([
                'success' => false,
                'data' => 'This order does not belong to you.',
            ]);
        }
        
        if ( empty($order->dr_id) ){
            return response([
                'success' => false,
                'data' => 'Delivery representative has not been assigned for this order yet.',
            ]);
        }

        $dr = User::find($order->dr_id);

        return response([
            'success' => true,
            'data' => [
                'dr' => $dr,
                'dr_tracking' => UserDrStatus::getOrCreate($dr)
            ]
        ]);
    }

    public function acceptOrder(Request $request){
        $this->validate($request, [
            'order_id' => 'required|exists:orders,id',
        ]);

        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();

        $order = Order::find($request->input('order_id'));

        // authorize
        if ( (int) $order->rest_id !== (int) $rest->id){
            return response([
                'success' => false,
                'message' => 'This order does not belong to you',
            ]);
        }
        
        if ( $order->order_status !== "PLACED" ){
            return response([
                'success' => false,
                'message' => "You can't accept this order anymore.",
            ]);
        }

        $order->order_status = "CONFIRMED";
        $order->confirmed_at = Carbon::now();
        $order->save();

        return response([
            'success' => true,
            'message' => 'Order has been accepted',
        ]);
        
        
    }
    
    public function cancelOrder(Request $request){
        $this->validate($request, [
            'order_id' => 'required|exists:orders,id',
        ]);

        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();

        $order = Order::find($request->input('order_id'));

        // authorize
        if ( (int) $order->rest_id !== (int) $rest->id){
            return response([
                'success' => false,
                'message' => 'This order does not belong to you',
            ]);
        }
        
        if ( $order->order_status !== "PLACED" ){
            return response([
                'success' => false,
                'message' => "You can't cancel this order anymore.",
            ]);
        }

        $order->order_status = "CANCELED";
        $order->canceled_at = Carbon::now();
        $order->save();

        // TODO: Notify User that the order has been canceled
 
        return response([
            'success' => true,
            'message' => 'Order has been canceled',
        ]);
    }
    
    public function readyOrder(Request $request){
        $this->validate($request, [
            'order_id' => 'required|exists:orders,id',
        ]);

        $user = Auth::guard('api_restaurant')->user();
        $rest = RestaurantInfo::where("radmin_id", $user->id)->first();

        $order = Order::find($request->input('order_id'));

        // authorize
        if ( (int) $order->rest_id !== (int) $rest->id){
            return response([
                'success' => false,
                'message' => 'This order does not belong to you',
            ]);
        }
        
        if ( $order->order_status !== "CONFIRMED" ){
            return response([
                'success' => false,
                'message' => "This order can't be marked as ready. Only accepted orders can be marked as ready.",
            ]);
        }

        $order->order_status = "FOOD_READY";
        $order->food_ready_at = Carbon::now();
        $order->save();

        // TODO: Notify all the foodman about this order
        SendFoodReadyNotification::dispatch($order);
 
        return response([
            'success' => true,
            'message' => 'Food has been marked as ready. We are informing delivery representatives.',
        ]);
    }
}
