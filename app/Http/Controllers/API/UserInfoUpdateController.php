<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\SendPhoneChangeVerification;
use App\Models\User;
use App\Models\UserInfoUpdateRequest;
use Illuminate\Validation\ValidationException;

class UserInfoUpdateController extends Controller
{
    public function requestForUpdate(Request $r){
        $user = auth('api')->user();

        // Make a contact no change request
        if ($r->has('contact_no')){
            // Get full 14 digit number +8801xxxxxxxxx
            $contact_no = getFullNumber($r->contact_no);

            // echo substr($contact_no, 0,4);

            if (strlen($contact_no) !== 14 || substr($contact_no, 0,4) !== '+880'){
                return response([
                    'success' => 'false',
                    'error'    => 'Phone number invalid',
                ], 200);
            }

            // Check if it exists
            if (User::where('contact_no', $contact_no)->exists() || UserInfoUpdateRequest::where('contact_no', $contact_no)->exists()){
                // throw ValidationException::withMessages([
                //     'phone_exists' => 'Sorry, the phone number already exists in an account'
                // ]);
                return response([
                  'success' => 'false',
                  'error'    => 'Sorry, the phone number already exists or pending in an account.',
                ], 200);
            }

            // If already a change request exists
            if (UserInfoUpdateRequest::where('user_id', $user->id)->exists()){
                // throw ValidationException::withMessages([
                //     'phone_exists' => 'You already have a pending change request'
                // ]);
                return response([
                    'success' => 'false',
                    'error'    => 'You already have a pending phone change request',
                ], 200);
            }

            // Generate the Code
            $verification_code = random_int(100000, 999999);

            // Send the SMS using API
            SendPhoneChangeVerification::dispatch($contact_no, $verification_code);
            
            $user_info_change_request = new UserInfoUpdateRequest([
                'user_id' => $user->id,
                'type' => 'contact_no',
                'contact_no' => $contact_no,
                'verification_code' => $verification_code
            ]);

            if ($user_info_change_request->save()){
                return response([
                  'success' => 'true',
                  'data'    => 'An SMS has been sent to ' . $contact_no . '. Please use that OTP to change your number.',
                ], 200);
            }
        }


        return response([
          'success' => false,
          'data'    => "Invalid request",
        ], 200);
    }

    public function getPendingRequests(){
        $user = auth('api')->user();

        $requests = UserInfoUpdateRequest::where('user_id', $user->id)->select(
            ['id','type','contact_no','email','created_at']
        )->get();

        return response([
          'success' => true,
          'data'    => $requests,
        ], 200);
    }

    public function executeUpdateRequest(UserInfoUpdateRequest $update, Request $r){
        $user = auth('api')->user();

        $this->validate($r, [
          'verification_code' => 'required|min:6|max:6'
        ]);

        // valid verification code
        if ($r->verification_code === $update->verification_code && $update->user_id === $user->id){
            $user->contact_no = $update->contact_no;
            $user->save();

            $update->delete();

            return response([
                'success' => true,
                'message' => 'Your contact number has been changed to ' . $user->contact_no . " successfully",
            ], 200);
        }

        return response([
            'success' => false,
            'message' => 'You are not authorized to make the update.',
        ], 200);
    }
}
