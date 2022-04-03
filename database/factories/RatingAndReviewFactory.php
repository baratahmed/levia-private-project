<?php

use Faker\Generator as Faker;
use App\Models\RestRating;
use App\Models\RestaurantInfo;
use App\Models\FoodRating;
use App\Models\Food;
use App\Models\ReviewInfo;
use App\Models\User;
use App\Models\RestReview;
use App\Models\FoodReview;

$factory->define(RestRating::class, function (Faker $faker) {
    return [
        'user_id' => User::inRandomOrder()->first()->id,
        'rest_id' => RestaurantInfo::inRandomOrder()->first()->id,
        'rest_rating_value' => random_int(1,5)
    ];
});


$factory->define(FoodRating::class, function (Faker $faker) {
    return [
        'user_id' => User::inRandomOrder()->first()->id,
        'rest_id' => RestaurantInfo::inRandomOrder()->first()->id,
        'food_id' => Food::inRandomOrder()->first()->food_id,
        'food_rating_value' => random_int(1,5)
    ];
});

$factory->define(ReviewInfo::class, function (Faker $faker) {
    return [
        'user_id' => User::inRandomOrder()->first()->id,
        'review_text' => $faker->realText(50)
    ];
});


$factory->define(RestReview::class, function (Faker $faker) {
    return [
        
    ];
});

$factory->define(FoodReview::class, function (Faker $faker) {
    return [
        
    ];
});