<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageBlock extends Model
{
    protected $guarded = [];
    
    public function blocked_user_instance(){
        return $this->hasOne(User::class, 'id', 'blocked_user');
    }
    
    public function blocked_restaurant_instance(){
        return $this->hasOne(RestaurantInfo::class, 'id', 'blocked_user');
    }
}