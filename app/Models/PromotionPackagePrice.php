<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionPackagePrice extends Model
{
    public function pack(){
        return $this->belongsTo(PromotionPackage::class, 'package_id', 'id');
    }
}
