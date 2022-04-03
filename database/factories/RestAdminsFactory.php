<?php

use Faker\Generator as Faker;

$factory->define(App\Models\RestAdmin::class, function (Faker $faker) {
    return [
        'email' => $faker->email,
        'password' => bcrypt('levia@rest')
    ];
});

// Let's define the admin factory here
$factory->define(App\Models\Admin::class, function(Faker $faker){
    return [
        'email' => 'admin@levia.com',
        'username' => 'admin',
        'password' => bcrypt('admin')
    ];
});