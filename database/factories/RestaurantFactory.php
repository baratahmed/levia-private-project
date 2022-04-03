<?php

use Faker\Generator as Faker;
use App\Models\RestaurantInfo;
use App\Models\District;
use App\Models\RestProperty;
use App\Models\RestSchedule;
use App\Models\RestFood;
use App\Models\FoodCategory;
use App\Models\Food;

// Feed Restaurant Info Table
$factory->define(RestaurantInfo::class, function (Faker $faker) {
    return [
        'rest_name' => $faker->unique()->company,
        'rest_image_url' => 'default.jpg',
        'rest_latitude' => $faker->latitude,
        'rest_longitude' => $faker->longitude,
        'rest_street' => $faker->streetAddress,
        'district_id' => District::inRandomOrder()->first()->district_id,
        'rest_post_code' => $faker->postcode,
        'phone' => $faker->phoneNumber
    ];
});


// Feed Restaurant Facility Table
$factory->define(RestProperty::class, function(Faker $faker){
    return [
        'parking' => random_boolean(),
        'wifi' => random_boolean(),
        'smoking_place' => random_boolean(),
        'kids_corner' => random_boolean(),
        'live_music' => random_boolean(),
        'self_service' => random_boolean(),
        'praying_area' => random_boolean(),
        'game_zone' => random_boolean(),
        'tv' => random_boolean(),
    ];
});

// Feed Restaurant Schedule Table
$factory->define(RestSchedule::class, function(Faker $faker){
    $day = getRandomDay();
    return [
        'day_id' => $day[0],
        'day' => $day[1],
        'opening_time' => "09:00:00",
        'closing_time' => "22:00:00",
    ];
});


// Feed Restaurant Facility Table
$factory->define(RestFood::class, function(Faker $faker){
    return [
        'food_id' => Food::inRandomOrder()->first()->food_id,
        'unit_price' => random_int(150, 400),
        'food_image_url' => 'default.jpg',
        'food_availability' => random_boolean(),
        'food_category_id' => FoodCategory::inRandomOrder()->first()->food_category_id,
    ];
});
