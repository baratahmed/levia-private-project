<?php

use Illuminate\Database\Seeder;
use App\Models\RestAdmin;
use App\Models\RestaurantInfo;
use App\Models\RestProperty;
use App\Models\RestSchedule;
use App\Models\RestFood;
use App\Models\Admin;

class RestAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\RestAdmin::class, 5)->create();
        factory(RestAdmin::class)->create([
            'email' => 'rifat.pdb@outlook.com',
            'password' => bcrypt('levia1234')
        ]);

        factory(Admin::class)->create();


        // Now add restaurant to admins
        $radmins = RestAdmin::all();
        foreach($radmins as $admin){

            factory(RestaurantInfo::class)->create([
                'radmin_id' => $admin->id
            ]);

        }

        // Retrieve Admins with Restaurant ID
        $rests = RestaurantInfo::all();

        foreach($rests as $rest){

            factory(RestProperty::class)->create(['rest_id'=>$rest->id]);
            factory(RestSchedule::class)->create(['rest_id'=>$rest->id]);
            
            for($i=0; $i<10; $i++){
                $food = factory(RestFood::class)->make(['rest_id'=>$rest->id]);
                if (RestFood::where('rest_id', $food->rest_id)->where('food_id', $food->food_id)->first()){
                    continue; // Discard
                }
                $food->save();
            }

        }
    }
}
