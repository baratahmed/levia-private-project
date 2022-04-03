<?php

use Faker\Generator as Faker;
use App\Models\District;

$factory->define(District::class, function (Faker $faker) {
    return [
        'district_name' => str_random(10),
        // 'remember_token' => str_random(10),
    ];
});
