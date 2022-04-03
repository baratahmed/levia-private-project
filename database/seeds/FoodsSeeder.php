<?php

use Illuminate\Database\Seeder;
use App\Models\Food;

class FoodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Food::class, 20)->create();
    }
}
