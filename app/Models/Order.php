<?php

namespace App\Models;

use App\Models\Stats\StatsFilter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Order
 * 
 * @property Cart $cart The associated cart
 */
class Order extends Model
{
    use StatsFilter;

    protected $guarded = [];

    public $appends = ['order_number'];

    const ORDER_STATUS_PLACED = 'PLACED';
    const ORDER_STATUS_CONFIRMED = 'CONFIRMED';
    const ORDER_STATUS_FOOD_READY = 'FOOD_READY';
    const ORDER_STATUS_FOODMAN_ASSIGNED = 'FOODMAN_ASSIGNED';
    const ORDER_STATUS_PICKED = 'PICKED';
    const ORDER_STATUS_DELIVERED = 'DELIVERED';
    const ORDER_STATUS_ACCEPTED = 'ACCEPTED';
    const ORDER_STATUS_CANCELED = 'CANCELED';

    protected $casts = [
        'confirmed_at' => 'datetime',
        'food_ready_at' => 'datetime',
        'foodman_assigned_at' => 'datetime',
        'picked_at' => 'datetime',
        'delivered_at' => 'datetime',
        'accepted_at' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    public function scopeJoinCart($query){
        return $query->leftJoin('carts', 'carts.id', '=', 'orders.cart_id');
            // ->selectRaw('orders.*, carts.total_payable');
    }

    public function getOrderNumberAttribute(){
        return env("ORDER_NUMBER_PREFIX","LV") . (env("ORDER_NUMBER_BASE", 10005145415) + $this->id);
    }

    public function scopeOnlyDelivered($query){
        return $query->where('order_status', $this::ORDER_STATUS_DELIVERED)->whereNotNull('delivered_at');
    }
    
    /**
     * scopeWhereStatusByRestaurant
     *
     * @param  mixed $query
     * @param  mixed $status_for_restaurant
     * @return void
     */
    public function scopeWhereStatusByRestaurant($query, $status_for_restaurant){
        $order_status = $this->convertRestaurantStatusToOrderStatus($status_for_restaurant);

        $new_query = $query;

        if ( is_array($order_status) ){
            $new_query = $query->whereIn('order_status', $order_status );
        } else if (is_string($order_status) && !empty($order_status)){
            $new_query = $query->where('order_status', $order_status );
        }

        return $new_query;
    }
    
    /**
     * Convert the status from restaurant's perspective to develoopment perspective
     *
     * @param  string $status_for_restaurant
     * @return string|array
     */
    protected function convertRestaurantStatusToOrderStatus($status_for_restaurant){
        $order_status = "";
        switch( strtolower($status_for_restaurant) ){
            case strtolower("Pending"):
                $order_status = $this::ORDER_STATUS_PLACED;
                break;
            case strtolower("Preparing"):
                $order_status = $this::ORDER_STATUS_CONFIRMED;
                break;
            case strtolower("Ready"):
                $order_status = $this::ORDER_STATUS_FOOD_READY;
                break;
            case strtolower("Foodman Coming"):
                $order_status = $this::ORDER_STATUS_FOODMAN_ASSIGNED;
                break;
            case strtolower("Picked"):
                $order_status = $this::ORDER_STATUS_PICKED;
                break;
            case strtolower("Delivered"):
                $order_status = [
                    $this::ORDER_STATUS_DELIVERED,
                    $this::ORDER_STATUS_ACCEPTED
                ];
                break;
            case strtolower("Canceled"):
                $order_status = $this::ORDER_STATUS_CANCELED;
                break;
        }

        return $order_status;
    }

    public function scopeWhereOrderNumber($query, $order_number){
        $id = (int)(substr($order_number, strlen(env("ORDER_NUMBER_PREFIX","LV")))) - env("ORDER_NUMBER_BASE", 10005145415);
        return $query->where('id', $id);
    }
    
    /**
     * Visible Order Status for Restaurant
     *
     * @return string
     */
    public function getStatusForRestaurantAttribute(){
        if ( in_array( $this->order_status, [$this::ORDER_STATUS_PLACED] ) ){
            return "Pending";
        } else if ( in_array( $this->order_status, [$this::ORDER_STATUS_CONFIRMED] ) ){
            return "Preparing";
        } else if ( in_array( $this->order_status, [$this::ORDER_STATUS_FOOD_READY] ) ){
            return "Ready";
        } else if ( in_array( $this->order_status, [$this::ORDER_STATUS_FOODMAN_ASSIGNED] ) ){
            return "Foodman Coming";
        } else if ( in_array( $this->order_status, [$this::ORDER_STATUS_PICKED] ) ){
            return "Picked";
        } else if ( in_array( $this->order_status, [
            $this::ORDER_STATUS_DELIVERED,
            $this::ORDER_STATUS_ACCEPTED
        ] ) ){
            return "Delivered";
        } else if ( in_array( $this->order_status, [$this::ORDER_STATUS_CANCELED] ) ){
            return "Canceled";
        }

        return "N/A";
    }
    
    /**
     * Get Remaining Time for the Order In Seconds
     *
     * @return int Seconds Remaining
     */
    public function getRemainingTimeInSecondsAttribute(){
        $default_order_remaining_hours_in_seconds = config('levia.order_remaining_hours', 48) * 60 * 60; // 48 Hours to Seconds
        $default_foodman_arrival_minutes_in_seconds = config('levia.order_foodman_arrival_minutes', 25) * 60; // 25 Minutes to Seconds

        switch ($this->order_status){
            case $this::ORDER_STATUS_PLACED:
                return $default_order_remaining_hours_in_seconds;
                break;
            
            case $this::ORDER_STATUS_CONFIRMED:
                /** @var Carbon $confirmed_at */
                $confirmed_at = $this->confirmed_at;

                $seconds_passed_since_confirmation = (int) $confirmed_at->diffInSeconds(Carbon::now());
                
                return (int) ( $default_order_remaining_hours_in_seconds - $seconds_passed_since_confirmation ); // 48 Hours
                break;
            
            case $this::ORDER_STATUS_FOOD_READY:
                /** @var Carbon $food_ready_at */
                $food_ready_at = $this->food_ready_at;

                $seconds_passed_since_confirmation = (int) $food_ready_at->diffInSeconds(Carbon::now());
                
                return (int) ( $default_foodman_arrival_minutes_in_seconds - $seconds_passed_since_confirmation ); // 25 minutes
                break;

            default:
                return 0;
                break;
        }
    }

    public function address(){
        return $this->hasOne(UserAddress::class, 'id', 'address_id');
    }
    
    public function restaurant(){
        return $this->hasOne(RestaurantInfo::class, 'id', 'rest_id');
    }
    
    public function dr(){
        return $this->hasOne(User::class, 'id', 'dr_id');
    }
    
    public function customer(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    
    public function cart(){
        return $this->hasOne(Cart::class, 'id', 'cart_id');
    }

    // public function pickup_verification_code(){
    //     return $this->hasOne(OrderVerificationCode::class, 'order_id', 'id');
    // }

    public static function appendCartSummary($orders){
        $ids = $orders->pluck('cart_id');

        $items = CartItem::
            leftJoin('all_food', 'all_food.food_id', '=', 'cart_items.food_id')->
            whereIn('cart_id', $ids)->get();
        
        foreach($orders as $order){
            $order_items = $items->where('cart_id', $order->cart_id);
            $order['food_items'] = $order_items->pluck('food_name');
        };
    }
}
