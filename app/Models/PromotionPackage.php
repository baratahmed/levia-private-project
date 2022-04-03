<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionPackage extends Model
{
    public function prices(){
        return $this->hasMany(PromotionPackagePrice::class, 'package_id', 'id');
    }
}
