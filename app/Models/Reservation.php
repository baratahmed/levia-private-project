<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $guarded = [];

    public function restaurant(){
        return $this->hasOne(RestaurantInfo::class, 'id', 'rest_id');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
