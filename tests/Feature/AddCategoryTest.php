<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddCategoryTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {

        $data = [
            'food_category_name' => str_random(10),
            'food_name' => [str_random(20), str_random(20), "Asif Chobi"]
        ];

        dd($this->post('/addcategory', $data)->getContent());

        // $this->assertDatabaseHas('food_category', [
        //     'food_category_name' => $data['food_category_name']
        // ]);

        // foreach($data['food_name'] as $f){
        //     $this->assertDatabaseHas('food', [
        //         'food_name' => $f
        //     ]);
        // }
    }
}
