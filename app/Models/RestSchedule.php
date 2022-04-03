<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestSchedule extends Model
{
	protected $table = 'rest_schedule';
	protected $guarded = ['_token'];
}
