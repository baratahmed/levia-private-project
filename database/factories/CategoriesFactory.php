<?php

use Faker\Generator as Faker;
use App\Models\FoodCategory;

$factory->define(FoodCategory::class, function (Faker $faker) {
    return [
        'food_category_name' => $faker->unique()->realText(20)
    ];
});
