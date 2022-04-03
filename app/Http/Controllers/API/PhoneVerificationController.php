<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\SendSMSVerification;
use App\Models\PhoneVerification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class PhoneVerificationController extends Controller
{
    public function requestSms(Request $r){
        $this->validate($r,[
            'country' => 'required',
            'phone' => 'required|numeric'
        ]);

        $country = $r->input('country');
        $phone = $r->input('phone');
        $retry = $r->has('retry') && $r->input('retry') ? true : false;
        $full_number = null;

        if ($country !== '+88' && $country !== '+880'){
            throw ValidationException::withMessages([
                'country' => __('auth.country_error')
            ]);
        }

        // Must be a valid number
        if ($this->testNumber($country, $phone)) { // A Valid Number | Pass the request
            $full_number = $country . $phone;
        } else { // Throw error, invalid number
            throw ValidationException::withMessages([
                'phone' => __('auth.format_error')
            ]);
        }

        // Check if already a user exists with that number
        if (!$r->has('for') && (User::where('contact_no', $full_number)->exists())){
            // throw ValidationException::withMessages([
            //     'user' => __('auth.phone_exists_on_user')
            // ]);
            return response([
                'success' => false,
                'message'    => __('auth.phone_exists_on_user'),
            ], 200);
        }

        // Check if we should retry
        $phoneVerification = PhoneVerification::where('phone', $full_number)->first();
        if ($phoneVerification){
            if (Carbon::now()->diffInSeconds($phoneVerification->updated_at) > 120){
                $retry = true;
            } else {
                throw ValidationException::withMessages([
                    'phone_verification' => __('auth.please_wait_120_seconds')
                ]);
            }
        } else {
            $retry = false;
        }
        
        

        // Generate the Code
        $verification_code = random_int(100000, 999999);

        // Send the SMS using API
        SendSMSVerification::dispatch($full_number, $verification_code);

        // Store things on the database
        if (!$retry){
            PhoneVerification::create([
                'phone' => $full_number,
                'verification_code' => $verification_code
            ]);
        } else {
            $phoneVerification->verification_code = $verification_code;
            $phoneVerification->save();
        }

        return response([
            'success' => 'true',
            'data'    => [
                'full_number' => $full_number
            ]
        ], 200);
    }

    public function checkNumber(Request $r){
        $this->validate($r,[
            'country' => 'required',
            'phone' => 'required|numeric'
        ]);

        $country = $r->country;
        $phone = $r->phone;
        $full_number = null;

        // return $r->all();

        if ($country !== '+88' && $country !== '+880'){
            throw ValidationException::withMessages([
                'country' => __('auth.country_error')
            ]);
        }

        // Must be a valid number
        if ($this->testNumber($country, $phone)) { // A Valid Number | Pass the request
            $full_number = $country . $phone;
        } else { // Throw error, invalid number
            throw ValidationException::withMessages([
                'phone' => __('auth.format_error')
            ]);
        }

        // Check if already a user exists with that number
        $user = User::where('contact_no', $full_number)->first();
        if ($user){
            return response([
                'success' => true,
                'number_exists' => true,
                'user_type' => $user->user_type,
                'message'    => __('auth.phone_exists_on_user'),
            ], 200);
        } else {
            return response([
                'success' => true,
                'number_exists' => false
            ], 200);
        }
    }

    public function submitCode(Request $r){
        $this->validate($r,[
            'full_number' => 'required|min:14',
            'verification_code' => 'required|min:6|max:6'
        ]);

        $full_number = $r->input('full_number');
        $verification_code = $r->input('verification_code');

        $phone_verification = PhoneVerification::where('phone', $full_number)->orderBy('id', 'desc')->first();

        if (!$phone_verification){
            throw ValidationException::withMessages([
                'full_number' => __('auth.phone_not_found')
            ]);
        } else {
            if ($phone_verification->is_verified == true){
                throw ValidationException::withMessages([
                    'full_number' => __('auth.phone_already_verified')
                ]);
            }
        }

        if ($verification_code == $phone_verification->verification_code){
            $phone_verification->is_verified = true;
            $phone_verification->save();
        } else {
            throw ValidationException::withMessages([
                'verification_code' => __('auth.invalid_code')
            ]);
        }

        return response([
            'success' => true,
            'message' => __('auth.phone_verified'),
            'data'    => [
                'full_number' => $full_number,
                'verification_code' => $verification_code
            ]
        ], 200);

    }

    /**
     * Test for valid number
     *
     * Takes a country code and phone number and returns true only if the number is of Bangladeshi
     *
     * @param String $country - Country Code
     * @param String $phone - Rest of the number
     * @return boolean True if number is : (+880,xxxxxxxxxx) or (+88, xxxxxxxxxxx)
     **/
    private function testNumber($country, $phone){
        return (($country == '+88' && strlen($phone) == 11) || ($country == '+880' && strlen($phone) == 10));
    }
}
