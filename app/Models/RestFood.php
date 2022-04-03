<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RestFood extends Model
{
    protected $table = 'rest_food';
    protected $guarded = ['_token'];
    protected $appends = ['rating'];

    public function food(){
        return $this->belongsTo(Food::class, 'food_id', 'food_id');
    }

    public function getRatingAttribute(){
		// TODO: Recalculate and store for future reference.

		return DB::table('food_rating_review_dataset')->selectRaw("avg(food_rating_value) as rating, count(id) as count, count(has_review) as review_count")->where('rest_id', $this->rest_id)->where('food_id', $this->food_id)->get();
	}
}
