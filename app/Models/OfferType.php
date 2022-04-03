<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferType extends Model
{
    protected $table = 'offer_type';
    protected $guarded = ['_token'];
}
