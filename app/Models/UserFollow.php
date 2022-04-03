<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFollow extends Model
{
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at'];

    public function following_user(){
        return $this->hasOne(User::class, 'id', 'follow_id');
    }

    public function follower_user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
