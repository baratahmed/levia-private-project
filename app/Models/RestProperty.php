<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestProperty extends Model
{
	protected $table = 'rest_facility';
	protected $guarded = ['_token'];
}
