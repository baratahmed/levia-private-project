<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostLike extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['user_id'];

    public function post(){
        return $this->belongsTo(Post::class, 'id', 'post_id');
    }
    
    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
