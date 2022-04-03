<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(FoodsSeeder::class);
        $this->call(RestAdminSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(RatingAndReviewSeeder::class);
        $this->call(FoodRatingAndReviewSeeder::class);
    }
}
