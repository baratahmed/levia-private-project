<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserBlock extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function blocked_user(){
        return $this->hasOne(User::class, 'id', 'blocked_user_id');
    }
}
