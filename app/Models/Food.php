<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
	protected $table = 'all_food';
	protected $primaryKey = 'food_id';

	protected $fillable = [
		'food_category_id',
		'food_name'
	];

	// protected $with = 'category';

	public function category(){
		return $this->hasOne(FoodCategory::class,'food_category_id', 'food_category_id');
	}

	public function restaurant(){
		return $this->hasMany(RestFood::class,'food_id', 'food_id');
	}
}
