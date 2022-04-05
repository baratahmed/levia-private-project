<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LeviaHelpers\FirebaseTokenVerifier;
use App\Models\PhoneVerification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request, $dr_mode = false){

        $this->validate($request,[
            'full_number' => 'required|unique:user_info,contact_no|min:14',
            'firebase_id_token' => 'sometimes',
            'firebase_uid' => 'sometimes',
            'firebase_notification_token' => 'sometimes',
            'user_name' => 'required',
            'password' => 'required|confirmed|min:8',
            'email' => 'sometimes|email|unique:user_info,user_email',
            'propic' => 'sometimes|file|mimes:jpeg,jpg,png'
        ]);

        extract($request->all());

        // dd($full_number);

        // $verification = PhoneVerification::where('phone', $full_number)->orderBy('id', 'desc')->first();

        // dd($verification);

        // if ($verification->verification_code != $verification_code){
        //     throw ValidationException::withMessages([
        //         'verification_code' => __('auth.invalid_code')
        //     ]);
        // }


        $full_number = $this->getFullNumber($full_number);

        // Verify FUll Number
        if (substr($full_number, 0,4) != "+880"){
            throw ValidationException::withMessages([
                'full_number' => __('auth.invalid_phone')
            ]);
        }

        // Verify Firebase Token
        $firebaseTokenVerifier = new FirebaseTokenVerifier(false);
        $firebaseCheck = $firebaseTokenVerifier->verify_firebase_token($request->firebase_id_token, $request->firebase_uid, $full_number);

        if ($firebaseCheck['success'] === false){
            throw ValidationException::withMessages([
                'firebase_id_token' => isset($firebaseCheck['error']) ? $firebaseCheck['error'] : 'Invalid ID Token. Please try again.'
            ]);
        }

        $user = new User([
            'fb_user_no' => 'FIREBASE:'.$firebaseCheck['UID'],
            'fb_profile_name' => $user_name,
            'fb_profile_pic_url' => '-1',
            'firebase_notification_token' => $request->firebase_notification_token,
            'user_email' => isset($email) ? $email : null,
            'contact_no' => $full_number,
            'password' => bcrypt($password),
            'user_type' => $dr_mode ? "DR" : "USER",
        ]);

        if ($request->has('propic')){
            // return "has propic";
            $file = $request->file('propic');
            $propic = \Image::make($file)->fit(100)->encode('jpg');
            $name = $user->id . "-" . \Illuminate\Support\Str::random(20) . "-" . Carbon::now()->getTimestamp() . ".jpg";
            $name = str_replace(" ", "_", $name);

            Storage::disk('public')->put( 'propic/'.$name, $propic);

            $user->fb_profile_pic_url = "file:".$name;
        }



        DB::transaction(function () use($user) {
            $user->save();
            // $verification->delete();
        });

        if ($dr_mode){ // Delivery Rep Mode
            $tokenBody = $user->createToken('Delivery Rep Personal Access Token');
        } else { // User Mode
            $tokenBody = $user->createToken('Personal Access Token');
        }

        $token = $tokenBody->token;

        $token->expires_at =  Carbon::now()->addMonth(1);
        $token->save();


        return response([
            'success' => 'true',
            'user' => $user,
            'token' => $tokenBody->accessToken
        ], 200);
    }


    public function login(Request $request){


        $this->validate($request, [
            'phone' => 'required|min:10|max:14',
            'password' => 'required|min:8'
        ]);


        $full_number = $this->getFullNumber($request->phone);
    
        $user = User::where('contact_no', $full_number)->first();

        if (!$user){
            throw ValidationException::withMessages([
                'phone' => __('auth.no_account')
            ]);
        }


        if (Auth::attempt(['contact_no' => $full_number, 'password' => $request->password])){
            $user = Auth::user();
            $tokenBody = $user->createToken('Personal Access Token');
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
                'credentials' => __('auth.invalid_credentials')
            ]);
        }
    }

    public function postResetPassword(Request $request){
        $this->validate($request,[
            'full_number' => 'required|min:14',
            'firebase_id_token' => 'sometimes',
            'firebase_uid' => 'sometimes',
            'password' => 'required|confirmed|min:8',
        ]);

        extract($request->all());

        $full_number = $this->getFullNumber($full_number);

        // Verify FUll Number
        if (substr($full_number, 0,4) != "+880"){
            throw ValidationException::withMessages([
                'full_number' => __('auth.invalid_phone')
            ]);
        }

        // Verify Firebase Token
        $firebaseTokenVerifier = new FirebaseTokenVerifier(false);
        $firebaseCheck = $firebaseTokenVerifier->verify_firebase_token($request->firebase_id_token, $request->firebase_uid, $full_number);

        if ($firebaseCheck['success'] === false){
            throw ValidationException::withMessages([
                'firebase_id_token' => isset($firebaseCheck['error']) ? $firebaseCheck['error'] : 'Invalid ID Token. Please try again.'
            ]);
        }

        $user = User::where('contact_no', $full_number)->firstOrFail();

        $user->password = bcrypt($password);

        if ($request->has('firebase_notification_token')){
            $user->firebase_notification_token = $request->firebase_notification_token;
        }

        DB::transaction(function () use($user) {
            $user->save();
            // $verification->delete();
        });

        return response([
            'success' => 'true',
            'message' => "Password has been changed successfully. Please login with new password."
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

        $user = auth('api')->user();

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
