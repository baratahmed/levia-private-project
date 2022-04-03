<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodCategory extends Model
{
	protected $table = 'food_category';
	protected $guarded = ['_token'];
	protected $primaryKey = 'food_category_id';

	public function food_list(){
		return $this->hasMany(Food::class, 'food_category_id', 'food_category_id');
	}
}
