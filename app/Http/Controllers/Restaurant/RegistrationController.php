<?php

namespace App\Http\Controllers\Restaurant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\RestaurantInfo;
use App\Models\District;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\RadminProfileCompleteness;

class RegistrationController extends Controller
{
    public function add_business(){
        $radmin = auth('radmin')->user();
        if ($radmin->restaurant()->exists() || RadminProfileCompleteness::get($radmin)->is_restaurant_added){
            return redirect()->route('radmin.dashboard');
        }
        
        $districts = District::orderBy('district_name', 'asc')->get();


        return view('theme_landing.pages.add-business', compact('districts'));
    }

    public function post_business(Request $request){
        $radmin = auth('radmin')->user();
        if ($radmin->restaurant()->exists() || RadminProfileCompleteness::get($radmin)->is_restaurant_added){
            return redirect()->route('radmin.dashboard');
        }

        $this->validate($request, [
            'business_name' => 'required|max:191',
            'business_contact_no' => 'required|max:100',
            'district' => 'required:exists:districts,district_id',
            'post_code' => 'required|max:10'
        ]);


        $filename = null;
        if ($request->file('image')){
            $file = $request->file('image');
            $filename = Carbon::now()->getTimestamp().str_random(15).'.'.$file->getClientOriginalExtension();
            $filename = preg_replace('/\s+/', '_', $filename);

            $img = \Image::make($file);
            $img->fit(300); // Fit a 300 by 300 size

            // Store the fit image
            Storage::disk('local')->put("public/rest_logo/".$filename, $img->stream());
        }

        $restaurant = new RestaurantInfo([
            'district_id' => $request->district,
            'rest_post_code' => $request->post_code
        ]);
        $restaurant->rest_name = $request->business_name;
        $restaurant->phone = $request->business_contact_no;
        $restaurant->rest_image_url = $filename;
        $restaurant->is_published = false;

        if ($radmin->restaurant()->exists()){
            $radmin->restaurant()->delete();
        }

        DB::transaction(function () use($radmin, $restaurant) {
            $radmin->restaurant()->save($restaurant);
            $proCom = RadminProfileCompleteness::get($radmin);
            $proCom->is_restaurant_added = true;
            $proCom->save();
        });
        

        return redirect()->route('radmin.profile.addContact')->with('message', 'Almost done! Just give us some more information.');
    }

    public function add_contact_person(){
        $radmin = auth('radmin')->user();
        if (RadminProfileCompleteness::get($radmin)->is_name_added){
            return redirect()->route('radmin.dashboard');
        }

        return view('theme_landing.pages.add-contact');
    }

    public function post_contact_person(Request $request){
        $radmin = auth('radmin')->user();
        if (RadminProfileCompleteness::get($radmin)->is_name_added){
            return redirect()->route('radmin.dashboard');
        }

        $this->validate($request, [
            'owner_name' => 'required|max:100',
            'owner_contact' => 'required|max:20',
            'business_registration' => 'required'
        ]);

        $radmin->name = $request->owner_name;
        $radmin->contact_no = $request->owner_contact;
        $rest = $radmin->restaurant;
        $rest->registration_number = $request->business_registration;

        DB::transaction(function () use($radmin, $rest) {
            $radmin->save();
            $rest->save();
            $proCom = RadminProfileCompleteness::get($radmin);
            $proCom->is_name_added = true;
            $proCom->save();
        });
        

        return redirect()->route('radmin.dashboard');
    }
}
