<?php

use Faker\Generator as Faker;
use App\Models\Food;
use App\Models\FoodCategory;

$factory->define(Food::class, function(Faker $faker){
    return [
        'food_name' => $faker->unique()->realText(20)
    ];
});