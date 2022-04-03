<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewInfo extends Model
{
    protected $table = 'review_info';

    public function user(){
        return $this->belongsTo(User::class);
    }
    
}
