<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\RestFoodDetailsDataset;
use App\Models\Setting;
use App\Models\UserAddress;
use Carbon\Carbon;

class CartController extends Controller
{
    public function addItem(Request $request){
        return response([
            'success' => false,
            'message' => 'This endpoint has been deprecated. Please use add_bulk instead.',
        ], 404);  

        $this->validate($request, [
            'rest_id' => 'required|exists:rest_info,id',
            'food_id' => 'required|exists:all_food,food_id',
        ]);

        $user = auth('api')->user();

        $rest_food = RestFoodDetailsDataset::
            where('rest_id', $request->input('rest_id'))
            ->where('food_id', $request->input('food_id'))
            ->first();

        if ( !$rest_food ){
            return response([
                'success' => false,
                'message' => 'This food does not belong to the restaurant',
            ], 422);            
        }

        // Find todays cart with the same restaurant
        $cart = Cart::
            where('rest_id', $request->input('rest_id'))
            ->where('user_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->subHours(12))
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$cart){
            $cart = new Cart([
                'rest_id' => $request->input('rest_id'),
                'user_id' => $user->id
            ]);

            $cart->save();
        }

        // Check if this cart already has this item, in this case, just increase the count
        $cart_item = CartItem::where('cart_id', $cart->id)
            ->where('food_id', $request->food_id)
            ->first();

        if ( !$cart_item ){ // Otherwise, add this item to cart
            $cart_item = new CartItem([
                'cart_id' => $cart->id,
                'food_id' => $request->food_id
            ]);
        } else {
            $cart_item->quantity += 1;
        }

        if ($request->has('quantity')){
            $cart_item->quantity = $request->input('quantity');
        }

        $cart_item->save();

        $cart_all_items =  CartItem::where('cart_id', $cart->id)
            ->get();

        return response([
            'success' => true,
            'data' => [
                'cart' => $cart,
                'cart_items' => $cart_all_items
            ]
        ]);
    }


    public function addBulkItems(Request $request){
        $this->validate($request, [
            'rest_id' => 'required|exists:rest_info,id',
            'food_id' => 'required',
            'food_id.*' => 'required|integer|exists:all_food,food_id',
            'quantity' => 'required',
            'quantity.*' => 'required|integer',
            'address_id' => 'required|exists:user_addresses,id'
        ]);

        // dd($request->all());

        $user = auth('api')->user();
        $address = UserAddress::find($request->input('address_id'));

        if ( (int) $address->user_id !== (int) $user->id){
            return response([
                'success' => false,
                'message' => 'The address given does not belong to you.',
            ], 422);  
        }

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

        // Just create a new cart for the user, don't have to work with the existing cart
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

        return response([
            'success' => true,
            'data' => [
                'cart' => $cart,
                'cart_items' => $cart_all_items
            ]
        ]);
    }

    public function showCart(){
        $user = auth('api')->user();

        $cart = Cart::
            where('user_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->subHours(12))
            ->orderBy('created_at', 'desc')
            ->first();

        $order = $cart->order;

        if ($order){
            $cart = null;
        }

        if ( !$cart || $order ){
            $cart_all_items = [];
        } else {
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
        }
        
        return response([
            'success' => true,
            'data' => [
                'cart' => $cart,
                'cart_items' => $cart_all_items
            ]
        ]);
    }
}
