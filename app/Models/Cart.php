<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Cart
 * 
 * @property double $total_price Price for all the foods for this order
 * @property double $discount Discount on the total price
 * @property double $subtotal Price for the foods after deducting discount. This is the amount the restaurant will get.
 * @property double $vat If any vat is applicable
 * @property double $rest_service_charge If any service charge is applicable for the restaurant
 * @property double $delivery_fee The delivery fee for this order
 * @property double $levia_discount If applicable
 * @property double $total_payable This is the amount the user will pay
 */
class Cart extends Model
{
    protected $guarded = [];
    
    /**
     * Associated Order
     *
     * @return mixed
     */
    public function order(){
        return $this->belongsTo(Order::class, 'id', 'cart_id');
    }
}
