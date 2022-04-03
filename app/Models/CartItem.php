<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $guarded = [];

    public function getFoodImageAttribute(){
        $imgName = $this->food_image_url == null ? 'default.png' : $this->food_image_url;
        return asset('storage/rest_food/'. $imgName);
    }
}
