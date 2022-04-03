<?php

namespace App\Http\Controllers\DeliveryRep\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\Notifications\SendOrderDeliveryOTP;
use App\Jobs\Notifications\SendOrderPickupOTP;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderVerificationCode;
use App\Models\RestaurantInfo;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserDrStatus;
use App\Models\UserDrTransaction;
use App\Models\UserDrWallet;
use App\Models\UserRejectedDeliveryRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeliveryRequestController extends Controller
{
    public function getDeliveryRequests(Request $request){
        $user = auth('api')->user();
        $dr_status = UserDrStatus::getOrCreate($user);

        if (!$dr_status->accepting_orders){
            return response([
                'success' => false,
                'message' => "You are not currently accepting orders. Please switch to accept orders to find delivery requests.",
            ]);
        }

        // Rejected Orders
        $rejects = UserRejectedDeliveryRequest::where('user_id', $user->id)->select('order_id')->get();

        $orders = Order::with([
            'address',
            'restaurant:id,rest_name,rest_image_url,rest_latitude,rest_longitude,rest_street,city_id,district_id,rest_post_code,road_no,police_station,phone,type,business_category',
            'customer:id,fb_profile_name',
            'cart:id,subtotal,total_payable'
        ])->where('order_status', 'FOOD_READY')->whereNotIn('id', $rejects);


        $orders = $orders->orderBy('created_at', 'desc');
        $orders = $orders->paginate(20);

        return $orders;
    }

    public function getHistory(Request $request){
        $this->validate($request, [
            'delivery_date' => 'sometimes|date',
        ]);
        
        $user = auth('api')->user();

        $orders = Order::with([
            'address',
            'restaurant:id,rest_name,rest_image_url,rest_latitude,rest_longitude,rest_street,city_id,district_id,rest_post_code,road_no,police_station,phone,type,business_category'
        ])->whereIn('order_status', ['DELIVERED','ACCEPTED'])->where('dr_id', $user->id);

        if ($request->has('delivery_date')){
            $orders = $orders->whereDate('delivered_at', $request->input('delivery_date'));
        }


        $orders = $orders->orderBy('created_at', 'desc');
        $orders = $orders->paginate(20);

        return response([
            'success' => true,
            'summary' => [
                'total_distance' => 'TODO:',
                'total_deliveries' => 'TODO:',
                'total_amount' => 'TODO:',
            ],
            'history' => $orders,
        ]);
        
    }
    
    public function getWallet(Request $request){
        $this->validate($request, [
            'delivery_date' => 'sometimes|date',
        ]);
        
        $user = auth('api')->user();
        $dr_status = UserDrStatus::getOrCreate($user);
        $dr_wallet = UserDrWallet::getOrCreate($user);

        $transactions = UserDrTransaction::with(['order:id'])->where('user_id', $user->id)->orderBy('id', 'desc')->paginate(20);

        return response([
            'success' => true,
            'my_wallet' => [
                'user' => $user,
                'cash_money' => $dr_wallet->balance
            ],
            'transactions' => $transactions,
        ]);
        
    }
    
    public function getCurrentDeliveries(Request $request){
        $user = auth('api')->user();
        $dr_status = UserDrStatus::getOrCreate($user);

        if (!$dr_status->accepting_orders){
            return response([
                'success' => false,
                'message' => "You are not currently accepting orders. Please switch to accept orders to find delivery requests.",
            ]);
        }

        $orders = Order::with([
            'address',
            'restaurant:id,rest_name,rest_image_url,rest_latitude,rest_longitude,rest_street,city_id,district_id,rest_post_code,road_no,police_station,phone,type,business_category',
            'customer:id,fb_profile_name',
            'cart:id,subtotal,total_payable'
        ])->whereIn('order_status', ['FOODMAN_ASSIGNED','PICKED'])->where('dr_id', $user->id);


        $orders = $orders->orderBy('created_at', 'desc');
        $orders = $orders->paginate(20);

        return $orders;
    }

    public function getOrderDetails(Request $request, Order $order){
        $user = Auth::guard('api')->user();
        $customer = User::findOrFail($order->user_id);
        $rest = RestaurantInfo::findOrFail($order->rest_id);

        if ( (int) $user->id !== (int) $order->dr_id){
            return response([
                'success' => false,
                'data' => 'This order does not belong to you.',
            ]);
        }

        // $order->load('cart:id,total_price,discount,subtotal');

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
        $cart = Cart::selectRaw('id,total_price,discount,subtotal,vat,rest_service_charge,delivery_fee,levia_discount,total_payable')->whereId($order->cart_id)->firstOrFail();
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
        $receipt = $cart;



        return response([
            'order' => $order,
            'order_info' => $order_info,
            'products' => $items,
            'receipt' => $receipt
        ]);
    }
    
    public function acceptOrder(Request $request){
        $this->validate($request, [
            'order_id' => 'required|exists:orders,id',
        ]);

        $user = auth('api')->user();
        $dr_status = UserDrStatus::getOrCreate($user);

        $order = Order::with([
            'address',
            'restaurant:id,rest_name,rest_image_url,rest_latitude,rest_longitude,rest_street,city_id,district_id,rest_post_code,road_no,police_station,phone,type,business_category'
        ])->where('id',$request->input('order_id'))->first();
        

        if (!$dr_status->accepting_orders){
            return response([
                'success' => false,
                'message' => "You are not currently accepting orders. Please switch to accept orders to find delivery requests.",
            ]);
        }

        if ( $order->order_status === "FOOD_READY" && $order->food_ready_at !== null && $order->foodman_assigned_at === null && $order->dr_id === null ){
            $order->order_status = "FOODMAN_ASSIGNED";
            $order->foodman_assigned_at = Carbon::now();
            $order->dr_id = $user->id;
            $order->save();
        } else {
            return response([
                'success' => false,
                'message' => "This delivery request is already taken.",
            ]);
        }

        return response([
            'success' => true,
            'message' => "You have successfully accepted this delivery request. Please reach the food location.",
            'order' => $order
        ]);
        
    }
    
    public function rejectOrder(Request $request){
        $this->validate($request, [
            'order_id' => 'required|exists:orders,id',
        ]);

        $user = auth('api')->user();
        $dr_status = UserDrStatus::getOrCreate($user);

        $order = Order::where('id',$request->input('order_id'))->first();
        

        if (!$dr_status->accepting_orders){
            return response([
                'success' => false,
                'message' => "You are not currently accepting orders. Please switch to accept orders to find delivery requests.",
            ]);
        }

        if ( $order->order_status === "FOOD_READY" && $order->food_ready_at !== null && $order->foodman_assigned_at === null && $order->dr_id === null ){
            $reject = UserRejectedDeliveryRequest::where([
                'user_id' => $user->id,
                'order_id' => $order->id
            ])->first();
            if (!$reject){
                $reject = new UserRejectedDeliveryRequest([
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'reason' => $request->input('reason')
                ]);
                $reject->save();
            }
        } else {
            return response([
                'success' => false,
                'message' => "This delivery request is already taken.",
            ]);
        }

        return response([
            'success' => true,
            'message' => "You won't see this delivery request anymore"
        ]);
        
    }

    public function pickOrder(Request $request){
        $this->validate($request, [
            'order_id' => 'required|exists:orders,id',
        ]);

        $user = auth('api')->user();
        $dr_status = UserDrStatus::getOrCreate($user);

        $order = Order::with([
            'address',
            'restaurant:id,rest_name,rest_image_url,rest_latitude,rest_longitude,rest_street,city_id,district_id,rest_post_code,road_no,police_station,phone,type,business_category'
        ])->where('id',$request->input('order_id'))->first();
        

        if ( $order->order_status === "FOODMAN_ASSIGNED" && $order->foodman_assigned_at !== null && (int) $order->dr_id === (int) $user->id ){
            // Generate code from for restaurant
            $code = random_int(100000,999999);
            $verification = OrderVerificationCode::where([
                'order_id' => $order->id,
                'action' => "PICK_FOOD"
            ])->first();
            if (!$verification){
                $verification = new OrderVerificationCode([
                    'order_id' => $order->id,
                    'action' => "PICK_FOOD",
                    'verification_digits' => $code
                ]);
    
                $verification->save();
                // TODO: Notify the restaurant about this verification digits

                SendOrderPickupOTP::dispatch($order, $verification);
            }
        } else {
            return response([
                'success' => false,
                'message' => "This delivery doesn't belong to you.",
            ]);
        }

        return response([
            'success' => true,
            'message' => "Pickup verification code has been sent to restaurant. Please take it from them and submit.",
        ]);
        
    }
    
    public function pickOrderConfirm(Request $request){
        $this->validate($request, [
            'order_id' => 'required|exists:orders,id',
            'verification_digits' => 'required|integer'
        ]);

        /** @var User $user */
        $user = auth('api')->user();
        $dr_status = UserDrStatus::getOrCreate($user);
        $dr_wallet = UserDrWallet::getOrCreate($user);

        $order = Order::with([
            'address',
            'restaurant:id,rest_name,rest_image_url,rest_latitude,rest_longitude,rest_street,city_id,district_id,rest_post_code,road_no,police_station,phone,type,business_category'
        ])->where('id',$request->input('order_id'))->first();
        

        if ( $order->order_status === "FOODMAN_ASSIGNED" && $order->foodman_assigned_at !== null && (int) $order->dr_id === (int) $user->id ){
            // Generate code from for restaurant
            $code = random_int(100000,999999);
            $verification = OrderVerificationCode::where([
                'order_id' => $order->id,
                'action' => "PICK_FOOD"
            ])->first();
            if (!$verification){
                return response([
                    'success' => false,
                    'message' => "You have to mark this delivery as Picked before you can confirm.",
                ]);
            } else if ( (int) $verification->verification_digits !== (int) $request->input('verification_digits')){
                return response([
                    'success' => false,
                    'message' => "The verification code is wrong.",
                ]);
            } else {
                $order->order_status = "PICKED";
                $order->picked_at = Carbon::now();

                $transaction = new UserDrTransaction([
                    'user_id' => $user->id,
                    'with' => 'rest',
                    'amount' => $order->cart->subtotal * -1,
                    'status' => 'paid',
                    'order_id' => $order->id,
                    'transaction_type' => 'pick_order'
                ]);

                // Now, we modify the wallet and make changes here
                DB::transaction(function() use($order, $verification, $dr_wallet, $transaction) {
                    $order->save();
                    $dr_wallet->deductBalance($order->cart->subtotal);
                    $transaction->save();
                    $verification->delete();
                });
            }
        } else {
            return response([
                'success' => false,
                'message' => "This food is already picked.",
            ]);
        }

        return response([
            'success' => true,
            'message' => "Food is now picked. Please deliver it to the customer as soon as possible.",
        ]);
        
    }

    public function deliverOrder(Request $request){
        $this->validate($request, [
            'order_id' => 'required|exists:orders,id',
        ]);

        $user = auth('api')->user();
        $dr_status = UserDrStatus::getOrCreate($user);

        $order = Order::with([
            'address',
            'restaurant:id,rest_name,rest_image_url,rest_latitude,rest_longitude,rest_street,city_id,district_id,rest_post_code,road_no,police_station,phone,type,business_category'
        ])->where('id',$request->input('order_id'))->first();
        

        if ( $order->order_status === "PICKED" && $order->picked_at !== null && (int) $order->dr_id === (int) $user->id ){
            // Generate code for user
            $code = random_int(100000,999999);
            $verification = OrderVerificationCode::where([
                'order_id' => $order->id,
                'action' => "DELIVERED"
            ])->first();
            if (!$verification){
                $verification = new OrderVerificationCode([
                    'order_id' => $order->id,
                    'action' => "DELIVERED",
                    'verification_digits' => $code
                ]);
    
                $verification->save();
                // TODO: Notify the user about this verification digits
                SendOrderDeliveryOTP::dispatch($order, $verification);
            }
        } else {
            return response([
                'success' => false,
                'message' => "This delivery doesn't belong to you.",
            ]);
        }

        return response([
            'success' => true,
            'message' => "Delivery verification code has been sent to the user. Please take it from them and submit.",
        ]);
        
    }

    public function deliverOrderConfirm(Request $request){
        $this->validate($request, [
            'order_id' => 'required|exists:orders,id',
            'verification_digits' => 'required|integer'
        ]);

        $user = auth('api')->user();
        $dr_status = UserDrStatus::getOrCreate($user);
        $dr_wallet = UserDrWallet::getOrCreate($user);

        $order = Order::with([
            'address',
            'restaurant:id,rest_name,rest_image_url,rest_latitude,rest_longitude,rest_street,city_id,district_id,rest_post_code,road_no,police_station,phone,type,business_category'
        ])->where('id',$request->input('order_id'))->first();
        

        if ( $order->order_status === "PICKED" && $order->picked_at !== null && (int) $order->dr_id === (int) $user->id ){
            // Generate code from for restaurant
            $code = random_int(100000,999999);
            $verification = OrderVerificationCode::where([
                'order_id' => $order->id,
                'action' => "DELIVERED"
            ])->first();
            if (!$verification){
                return response([
                    'success' => false,
                    'message' => "You have to mark this delivery as Delivered before you can confirm.",
                ]);
            } else if ( (int) $verification->verification_digits !== (int) $request->input('verification_digits')){
                return response([
                    'success' => false,
                    'message' => "The verification code is wrong.",
                ]);
            } else {
                $order->order_status = "DELIVERED";
                $order->delivered_at = Carbon::now();

                $transaction = new UserDrTransaction([
                    'user_id' => $user->id,
                    'with' => 'user',
                    'amount' => $order->cart->subtotal,
                    'status' => 'undeposited',
                    'order_id' => $order->id,
                    'transaction_type' => 'deliver_order'
                ]);

                // Now, we modify the wallet and make changes here
                DB::transaction(function() use($order, $verification, $dr_wallet, $transaction) {
                    $order->save();
                    $dr_wallet->addBalance($order->cart->subtotal);
                    $transaction->save();
                    $verification->delete();
                });

                DB::transaction(function() use($order, $verification){
                    $order->save();
                    $verification->delete();
                });
            }
        } else {
            return response([
                'success' => false,
                'message' => "This food is already delivered.",
            ]);
        }

        return response([
            'success' => true,
            'message' => "Food is now delivered. Good job. You can find new orders now.",
        ]);
        
    }
}
