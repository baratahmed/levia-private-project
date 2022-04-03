<?php

namespace App\Http\Controllers\Restaurant\API;

use App\Models\RestAdmin;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\SendRegistrationEmail;
use App\Models\RadminProfileCompleteness;
use App\Models\RestaurantInfo;
use App\Models\RestPaymentMethod;
use App\Models\RestProperty;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request){
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        $user = RestAdmin::where('email', $request->input('email'))->first();

        if (!$user){
            throw ValidationException::withMessages([
                'email' => __('auth.no_account_email')
            ]);
        }

        // return $user;

        // return [
        //     'password' => $request->password,
        //     'hashed_password' => $user->password,
        //     'check' => Hash::check($request->password, $user->password)
        // ];

        if (Hash::check($request->password, $user->password)){
            $tokenBody = $user->createToken('Restaurant Personal Access Token');
            $token = $tokenBody->token;

            $token->expires_at =  Carbon::now()->addMonth(1);
            $token->save();

            if ($request->has('firebase_notification_token')){
                $user->firebase_notification_token = $request->firebase_notification_token;
                $user->save();
            }

            return response([
                'success' => 'true',
                'user' => $user,
                'token' => $tokenBody->accessToken
            ], 200);
        } else {
            throw ValidationException::withMessages([
                'credentials' => __('auth.invalid_credentials_email')
            ]);
        }
    }

    public function register(Request $request){
        $this->validate($request, [
            'email' => 'required|email|unique:rest_admins,email',
            'password' => 'required|min:6|max:255',

            // Business Related
            'business_name' => 'required|max:191',
            'business_phone' => 'required|unique:rest_info,phone',
            'district_id' => 'required:exists:districts,district_id',
            'post_code' => 'required|max:10',
            'business_logo' => 'sometimes|file',

            // Owner Related
            'owner_name' => 'required',
            'owner_phone' => 'required|unique:rest_admins,contact_no',
        ]);


        $business_phone = $this->getFullNumber($request->input('business_phone'));
        $owner_phone = $this->getFullNumber($request->input('owner_phone'));

        // Verify FUll Number
        if (substr($business_phone, 0,4) != "+880"){
            throw ValidationException::withMessages([
                'business_phone' => __('auth.invalid_phone')
            ]);
        }
        if (substr($owner_phone, 0,4) != "+880"){
            throw ValidationException::withMessages([
                'owner_phone' => __('auth.invalid_phone')
            ]);
        }

        $user = new RestAdmin([
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        $user->contact_no = $owner_phone;
        $user->name = $request->owner_name;

        $user->save();

        // Generate Token and Store
        $token = str_random(50);
        $otp = random_int(100000,999999);
        DB::table('rest_admins_password_resets')->insertGetId([
            'email' => $user->email,
            'token' => $token,
            'otp' => $otp,
            'created_at' => Carbon::now()
        ]);

        // Send Registration Email
        $link = env('APP_URL').'/verifyEmail?token='.$token;


        // Restaurant Info Related
        $filename = null;
        if ($request->file('restaurant_logo')){
            $file = $request->file('restaurant_logo');
            $filename = Carbon::now()->getTimestamp().str_random(15).'.'.$file->getClientOriginalExtension();
            $filename = preg_replace('/\s+/', '_', $filename);

            $img = \Image::make($file);
            $img->fit(300); // Fit a 300 by 300 size

            // Store the fit image
            Storage::disk('local')->put("public/rest_logo/".$filename, $img->stream());
        }

        $restaurant = new RestaurantInfo([
            'district_id' => $request->district_id,
            'rest_post_code' => $request->post_code
        ]);
        $restaurant->rest_name = $request->business_name;
        $restaurant->phone = $business_phone;
        $restaurant->rest_image_url = $filename;
        $restaurant->registration_number = $request->input('business_registration_no');
        $restaurant->is_published = false;

        DB::transaction(function () use($user, $restaurant) {
            $user->restaurant()->save($restaurant);
            $restaurant->properties()->save(new RestProperty());
            $restaurant->paymethod()->save(new RestPaymentMethod());
            $proCom = RadminProfileCompleteness::get($user);
            $proCom->is_restaurant_added = true;
            $proCom->is_name_added = true;
            $proCom->save();
        });

        SendRegistrationEmail::dispatch($link, $user, $otp);


        $tokenBody = $user->createToken('Restaurant Personal Access Token');
        $token = $tokenBody->token;

        $token->expires_at =  Carbon::now()->addMonth(1);
        $token->save();


        return response([
            'success' => 'true',
            'token' => $tokenBody->accessToken,
            'user' => $user,
            'restaurant' => $restaurant
        ], 200);
    }

    private function getFullNumber($number){
        $prefix = '+880';
        $givenLength = strlen($number);
        $required = 14-$givenLength;
        $addFirst = substr($prefix, 0, $required);

        return $addFirst . $number;
    }

    public function changePassword(Request $request){
        $this->validate($request, [
            'current_password' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        $user = auth('api_restaurant')->user();

        if ( ! Hash::check($request->current_password, $user->password) ){
            return response([
                'success' => false,
                'message' => 'Your current password is incorrect.',
            ]);
        }

        $user->password = bcrypt($request->password);
        $user->save();

        return response([
            'success' => true,
            'message' => 'Your password has been updated.',
        ]);
        
    }

    public function logout(Request $request){
        $request->user()->token()->revoke();

        return response()->json([
            'success' => 'true',
            'message' => 'Successfully logged out'
        ]);
    }
}
