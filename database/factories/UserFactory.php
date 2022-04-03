<?php

use Faker\Generator as Faker;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'fb_user_no' => $faker->unique()->randomNumber,
        'fb_profile_name' => $faker->name,
        'role_id' => 1,
        'status_id' => 1,
        'fb_profile_pic_url' => 'default.jpg',
        'contact_no' => $faker->unique()->randomNumber,
        'remember_token' => str_random(10)

        //password   $2y$10$rzmWprztooHc2oMNZGeH2OpcZKCKPpN87hBJULUplqKx0V/YOqS7y
    ];
});
