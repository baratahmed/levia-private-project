<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\District;
use App\Models\Food;

class TestController extends Controller
{
    public function addDumyData(Request $requset){
        District::create($requset->all());
    }

    public function test(){

        $matches = [];
        $result = preg_match("~(.*)\[(.*)\]~", "key[]", $matches);
        dd($result, $matches);

        // $c1 = collect('Zisad', 'Arif', 'Asif', 'Angel');
        // $c2 = collect('Zisad', 'Asif');

        // $c1 = collect(
        //     [
        //         'id' => 1,
        //         'name' => "Zisad",
        //     ],
        //     [
        //         'id' => 1,
        //         'name' => "Arif",
        //     ],
        //     [
        //         'id' => 1,
        //         'name' => "Asif",
        //     ],
        //     [
        //         'id' => 1,
        //         'name' => "Angel",
        //     ]
        // );

        // $c2 = collect(
        //     [
        //         'id' => 1,
        //         'name' => "Asif",
        //     ],
        //     [
        //         'id' => 1,
        //         'name' => "Angel",
        //     ]
        // );

        // $dif = $c1->diffAssoc($c2);

        // dd($dif->all());

        $food = array([1,"Zisad"], [1, "Arif"], [1, "asif"], [1, "Angel"]);
        $data = Food::whereIn("food_name", $food)->get();

        // dd($data->toArray()->food_name);
        $collection = collect($food);

        // dd($collection);
        // dd($food);
        // $collection = collect([1, 2, 3, 4, 5]);

        $multiplied = $data->map(function ($item, $key) {
            return $item->food_name;
        });

        $subtracted = array_map(
            function ($x) {
            return $x[1];
            },
            $food);

        dd($subtracted);

        // $multiplied->all();
        // dd(array_diff($food, $multiplied->toArray()));
        // $arr1 = array('a' => 1, 'b' => 3, 'c' => 10);
        // $arr2 = array('a' => 2, 'b' => 1, 'c' => 5);

        $subtracted = array_map(function ($x, $y) {
            // return $y - $x;
            if(strtolower($y) != strtolower($x)){
                return $x;
            }
        }, $food, $multiplied->toArray());
        // $result = array_combine(array_keys($arr1), $subtracted);

        Food::create($subtracted);

        dd($subtracted);
    }
}
