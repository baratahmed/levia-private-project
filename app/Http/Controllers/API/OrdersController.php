<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\Notifications\PlaceOrderNotification;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderVerificationCode;
use App\Models\RestaurantInfo;
use App\Models\RestFoodDetailsDataset;
use App\Models\RestUserBlock;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserAddress;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrdersController extends Controller
{
    public function placeOrder(Request $request){
        Log::info("Place Order Request", $request->all());
        
        // $request->merge([
        //     'food_id[]' => '69',
        //     'quantity[]' => '69',
        //     'address[road_no]' => '69',
        //     'address[flat_no]' => '69',
        // ]);

        $request->merge(extractStringToArray($request));

        Log::info("Place Order Request After Extraction", $request->all());

        // return $request->all();

        $this->validate($request, [
            'rest_id' => 'required|exists:rest_info,id',
            'food_id' => 'required',
            'food_id.*' => 'required|integer|exists:all_food,food_id',
            'quantity' => 'required',
            'quantity.*' => 'required|integer',
            'address_id' => 'sometimes|exists:user_addresses,id',
            'address' => 'sometimes',

            // Address can be created on the go
            'address.latitude' => ['sometimes','regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'address.longitude' => ['sometimes','regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'address.division' => 'sometimes|exists:divisions,id',
            'address.district' => 'sometimes|exists:districts,district_id',
            'address.upazila' => 'sometimes|exists:upazilas,id'
        ]);

        // return count($request->input('food_id'));

        // return $request->all();

        $user = auth('api')->user();

        if ($request->has('address_id')){
            $address = UserAddress::find($request->input('address_id'));
        } else {
            $division = $request->has('address.division') ? DB::table('divisions')->find($request->input('address.division')) : null;
            $district = $request->has('address.district') ? DB::table('districts')->where('district_id', $request->input('address.district'))->first() : null;
            $upazila = $request->has('address.upazila') ? DB::table('upazilas')->find($request->input('address.upazila')) : null;

            $address = new UserAddress();

            
            
            $address->user_id = $user->id;
            $address->latitude = $request->input('address.latitude');
            $address->longitude = $request->input('address.longitude');
            $address->road_no = $request->input('address.road_no');
            $address->flat_no = $request->input('address.flat_no');
            $address->other_details = $request->input('address.other_details');
            $address->city = null !== $division ? $division->name : '';
            $address->district = null !== $district ? $district->district_name : '';
            $address->upazila = null !== $upazila ? $upazila->name : '';
            $address->phone = $user->contact_no;
            // dd($address);
            $address->save();
        }

        // Verify the Address with the User
        if ( (int) $address->user_id !== (int) $user->id){
            return response([
                'success' => false,
                'message' => 'The address given does not belong to you.',
            ], 422);  
        }

        /** @var RestaurantInfo $rest  */
        $rest = RestaurantInfo::where('id', $request->input('rest_id'))->first();

        // If the restaurant has subscribed to plan that has order permission
        if (!$rest->authorizeReceiveOrder()){
            return response([
                'success' => false,
                'message' => 'This restaurant has not subscribed to receive orders.',
            ], 422);  
        }

        // Check if the restaurant is receiving orders
        if (!$rest->is_receiving_orders){
            return response([
                'success' => false,
                'message' => 'This restaurant is not receiving orders right now.',
            ], 422);  
        }
        // Check if the user is blocked from making orders
        $blocked = RestUserBlock::where('radmin_id', $rest->radmin_id)->where('user_id', $user->id)->first();
        // dd($blocked);
        if ($blocked){
            return response([
                'success' => false,
                'message' => 'You are blocked from making orders to this restaurant.',
            ], 422);  
        }



        /************
         * Work with the Carts
         ************/
        $rest_food = RestFoodDetailsDataset::
            where('rest_id', $request->input('rest_id'))
            ->whereIn('food_id', $request->input('food_id'))
            ->count();

        // dd(count($request->input('food_id')));
        // dd($rest_food);

        if ( count($request->input('food_id')) < $rest_food ){
            return response([
                'success' => false,
                'message' => 'The given foods are not found in the restaurant',
            ], 422);            
        }
        
        if ( count($request->input('food_id')) !== count($request->input('quantity')) ){
            return response([
                'success' => false,
                'message' => 'Foods and Quantities list should be equal in size',
            ], 422);            
        }


        // Just create a new cart for the user, don't have to work with any existing carts
        $cart = new Cart([
            'rest_id' => $request->input('rest_id'),
            'user_id' => $user->id,
            'address_id' => $address->id
        ]);

        $cart->save();

        // And then add the cart items
        $cart_items_array = [];

        for( $i=0; $i< count($request->input('food_id')); $i++ ){
            $cart_items_array[] = [
                'cart_id' => $cart->id,
                'food_id' => $request->input('food_id')[$i],
                'quantity' => $request->input('quantity')[$i],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        CartItem::insert($cart_items_array);

        $cart_all_items =  CartItem::where('cart_id', $cart->id)
            ->leftJoin('carts', 'cart_items.cart_id', '=', 'carts.id')
            ->leftJoin('rest_food', function($join){
                $join->on('carts.rest_id', '=', 'rest_food.rest_id');
                $join->on('cart_items.food_id', '=', 'rest_food.food_id');
            })
            ->selectRaw("cart_items.*, rest_food.unit_price")
            ->get();

        $total_price = 0;
        foreach($cart_all_items as $item){
            $item['item_total'] = (float) $item['quantity'] * (float) $item['unit_price'];
            $total_price += $item['item_total'];
        }

        $cart->total_price = $total_price;
        $cart->subtotal = $cart->total_price - $cart->discount;
        $cart->delivery_fee = Setting::getValue('delivery_fee', 0);
        $cart->total_payable = $cart->subtotal + $cart->vat + $cart->rest_service_charge + $cart->delivery_fee - $cart->levia_discount;
        $cart->save();



        /************
         * Now, the cart is complete, Process the Order
         ************/

        $order = new Order([
            'user_id' => $user->id,
            'cart_id' => $cart->id,
            'address_id' => $cart->address_id,
            'rest_id' => $cart->rest_id
        ]);

        $order->save();

        PlaceOrderNotification::dispatch($order);

        return response([
            'success' => true,
            'data' => [
                'order' => $order,
                'cart' => $cart
            ],
        ]);
        
        
    }

    public function getOrders(Request $request){
        $user = Auth::guard('api')->user();
        // $rest = RestaurantInfo::where("radmin_id", $user->id)->first();

        $orders = Order::with(['cart:id,subtotal', 'restaurant:id,rest_name,rest_image_url,rest_street,district_id,rest_post_code,road_no,police_station,city_id', 'address'])->where('user_id', $user->id);


        $orders = $orders->orderBy('created_at', 'desc');
        $orders = $orders->paginate(20);

        // Append Cart Foods Summary in Orders
        Order::appendCartSummary($orders);

        return $orders;
    }
    
    public function getOrderDetails(Request $request, Order $order){
        $user = Auth::guard('api')->user();
        $rest = RestaurantInfo::findOrFail($order->rest_id);

        if ( (int) $user->id !== (int) $order->user_id){
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
                'fb_profile_name' => $user->fb_profile_name,
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

        // Foodman
        if ($order->dr_id !== null && $order->foodman_assigned_at !== null){
            $foodman = User::selectRaw('id,fb_profile_name,fb_profile_pic_url')->whereId($order->dr_id)->first();
        } else {
            $foodman = null;
        }


        return response([
            'order' => $order,
            'order_info' => $order_info,
            'products' => $items,
            'receipt' => $receipt,
            'foodman' => $foodman
        ]);
    }

    public function getOrderDeliveryCode(Order $order){
        $user = Auth::guard('api')->user();

        if ( (int) $user->id !== (int) $order->user_id){
            return response([
                'success' => false,
                'data' => 'This order does not belong to you.',
            ]);
        }

        if ($order->order_status !== ORDER::ORDER_STATUS_PICKED){
            return response([
                'success' => false,
                'message' => 'This order is not picked yet.',
            ], 200);
        }

        $order_verification_code = OrderVerificationCode::where([
            'order_id' => $order->id,
            'action' => "DELIVERED"
        ])->selectRaw('action,verification_digits')->first();

        if ( !$order_verification_code ){
            return response([
                'success' => false,
                'message' => 'The Delivery Representative must mark this order as delivered.',
            ], 200);
        }

        return $order_verification_code;
    }
}
