<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDrTransaction extends Model
{
    protected $table = 'user_dr_transactions';

    protected $guarded = [];

    public function order(){
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
